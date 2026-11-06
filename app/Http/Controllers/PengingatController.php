<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\Modelpengingat;
use App\Services\WhatsappService;
use Carbon\Carbon;
use App\Jobs\KirimPengingatJob;

class PengingatController extends Controller
{
    protected WhatsappService $wa;

    public function __construct(WhatsappService $wa)
    {
        $this->middleware('auth')->except(['kirimPengingat']); // allow scheduler/CLI to call kirimPengingat without web auth
        $this->wa = $wa;
    }

public function kirimNow(Request $request)
    {
        // optional: cek permission
        // $this->authorize('kirimPengingat');

        // dispatch job ke queue, cepat (non-blocking)
        dispatch(new KirimPengingatJob());

        return response()->json(['ok' => true, 'message' => 'Tugas dikirim ke queue']);
    }

    /**
     * Kirim pengingat otomatis
     * Bisa dipanggil via scheduler/artisan atau via route (untuk testing/admin).
     */
    public function kirimPengingat(Request $request = null)
    {
        $now = Carbon::now();
        // Pastikan locale id terpasang agar menghasilkan "Senin","Selasa",...
        $todayName = $now->locale('id')->isoFormat('dddd'); // e.g. "Senin"
        $currentTime = $now->format('H:i:00'); // exact match "08:30:00"

        // Untuk feedback (jika dipanggil via CLI, gunakan $this->info)
        $isConsole = app()->runningInConsole();

        if ($isConsole) {
            $this->info("Menjalankan pengiriman pengingat - {$todayName} {$currentTime}");
        } else {
            Log::info("Menjalankan pengiriman pengingat (via HTTP) - {$todayName} {$currentTime}");
        }

        $query = Modelpengingat::where('is_active', true)
            ->where('hari', $todayName)
            ->where(function ($q) use ($currentTime) {
                // all-day (waktu null) OR exact time match -> mencegah pengiriman berulang
                $q->whereNull('waktu')
                  ->orWhere('waktu', $currentTime);
            })
            ->with(['user.profile']); // eager load relasi untuk menghindari N+1

        $sent = 0;
        $skippedNoNumber = 0;
        $skippedRecentlySent = 0;

        // gunakan chunk supaya aman bila dataset besar
        $query->chunk(100, function ($pengingats) use (&$sent, &$skippedNoNumber, &$skippedRecentlySent, $now, $isConsole) {
            foreach ($pengingats as $p) {
                try {
                    // Hindari pengiriman ulang dalam window tertentu (mis. 5 menit)
                    if ($p->last_sent_at && $p->last_sent_at->diffInMinutes($now) < 5) {
                        $skippedRecentlySent++;
                        Log::info("Lewatkan pengingat {$p->id_pengingat} karena last_sent_at terlalu dekat.", ['last_sent_at' => $p->last_sent_at]);
                        continue;
                    }

                    // Ambil nomor WA dari profile (sesuaikan nama field)
                    $target = $p->user->profile->nomor_telepon ?? $p->user->profile->no_wa ?? null;
                    if (empty($target)) {
                        $skippedNoNumber++;
                        Log::warning("Tidak ada nomor WA untuk pengingat id={$p->id_pengingat}, user_id={$p->id_user}");
                        continue;
                    }

                    // Bangun pesan — bisa disesuaikan formatting
                    $body = trim(($p->pesan ?: $p->judul) . PHP_EOL . PHP_EOL
                        . "Hari: {$p->hari}" . PHP_EOL
                        . "Waktu: " . ($p->waktu ?? 'Tidak ditentukan') . PHP_EOL
                        . "- Dari Sistem Pengingat -");

                    // Kirim via service
                    $response = $this->wa->send($target, $body);

                    // Log response (sesuaikan parsing jika API mengembalikan JSON)
                    Log::info("WA dikirim untuk pengingat id={$p->id_pengingat}", [
                        'target' => $target,
                        'response' => $response,
                    ]);

                    // Update last_sent_at setelah sukses
                    $p->last_sent_at = Carbon::now();
                    $p->save();

                    $sent++;
                } catch (\Throwable $e) {
                    // Tangani error agar loop tetap berjalan
                    Log::error("Gagal kirim pengingat id={$p->id_pengingat}", [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString(),
                    ]);
                }
            }
        });

        $summary = [
            'sent' => $sent,
            'skipped_no_number' => $skippedNoNumber,
            'skipped_recently_sent' => $skippedRecentlySent,
        ];

        if ($isConsole) {
            $this->info("Pengiriman selesai. Terkirim: {$sent}. Lewatkan(no number): {$skippedNoNumber}. Lewatkan(recent): {$skippedRecentlySent}.");
            return 0; // sukses exit code untuk CLI
        }

        return response()->json([
            'message' => 'Proses pengingat selesai',
            'summary' => $summary,
        ]);
    }

    /**
     * List pengingat user (dashboard / management)
     */
    public function index(Request $request)
    {
        $userId = Auth::user()->id_user ?? Auth::id();

        $query = Modelpengingat::where('id_user', $userId);

        // filter aktif / semua
       if ($request->status === 'inactive') {
    $query->where('is_active', false);
} elseif ($request->status === 'active') {
    $query->where('is_active', true);
} 

        // optional search by judul
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where('judul', 'like', "%{$q}%");
        }

        // urutkan berdasarkan hari mulai dari hari ini, lalu waktu
        $days = ['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu'];
        $fieldExpr = "FIELD(hari, " . implode(',', array_map(fn($d) => "'$d'", $days)) . ")";
        $todayIso = now()->dayOfWeekIso; // 1..7

        // Untuk menjaga agar urutan mulai dari hari ini, gunakan CASE + FIELD
        $orderRaw = "CASE WHEN {$fieldExpr} >= ? THEN {$fieldExpr} ELSE {$fieldExpr} + 7 END ASC, COALESCE(waktu, '00:00:00') ASC";

        $pengingat = $query->whereNotNull('hari') // prioritaskan yang punya hari (opsional)
                          ->orderByRaw($orderRaw, [$todayIso])
                          ->paginate(15)
                          ->appends($request->only(['q','status']));

        return view('pengingat.index', compact('pengingat'));
    }

    /**
     * Show form create
     */
    public function create()
    {
        $days = ['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu'];
        return view('pengingat.create', compact('days'));
    }

    /**
     * Store new pengingat
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'judul' => 'required|string|max:255',
            'pesan' => 'nullable|string',
            'hari'  => 'nullable|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu',
            'waktu' => 'nullable|date_format:H:i',
        ]);

        $data['id_user'] = Auth::user()->id_user ?? Auth::id();
        $data['is_active'] = $request->has('is_active') ? (bool) $request->input('is_active') : true;

        if (!empty($data['waktu'])) {
            $data['waktu'] = Str::length($data['waktu']) === 5 ? $data['waktu'] . ':00' : $data['waktu'];
        }

        Modelpengingat::create($data);

        return redirect()->route('pengingat.index')->with('success', 'Pengingat berhasil ditambahkan.');
    }

    /**
     * Show edit form
     */
    public function edit($id)
    {
        $userId = Auth::user()->id_user ?? Auth::id();
        $p = Modelpengingat::where('id_pengingat', $id)->where('id_user', $userId)->firstOrFail();

        $days = ['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu'];
        return view('pengingat.edit', compact('p','days'));
    }

    /**
     * Update pengingat
     */
    public function update(Request $request, $id)
    {
        $userId = Auth::user()->id_user ?? Auth::id();
        $p = Modelpengingat::where('id_pengingat', $id)->where('id_user', $userId)->firstOrFail();

        $data = $request->validate([
            'judul' => 'required|string|max:255',
            'pesan' => 'nullable|string',
            'hari'  => 'nullable|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu',
            'waktu' => 'nullable|date_format:H:i',
            'is_active' => 'nullable|boolean',
        ]);

        if (!array_key_exists('is_active', $data)) {
            $data['is_active'] = $p->is_active;
        }

        if (!empty($data['waktu'])) {
            $data['waktu'] = Str::length($data['waktu']) === 5 ? $data['waktu'] . ':00' : $data['waktu'];
        } else {
            $data['waktu'] = null;
        }

        $p->update($data);

        return redirect()->route('pengingat.index')->with('success', 'Pengingat berhasil diperbarui.');
    }

    /**
     * Toggle active/inactive
     */
    public function toggle($id)
    {
        $pengingat = Modelpengingat::findOrFail($id);
        $pengingat->is_active = ! $pengingat->is_active;
        $pengingat->save();

        return back()->with('success', 'Status pengingat diubah');
    }

    /**
     * Delete pengingat
     */
    public function destroy($id)
    {
        $pengingat = Modelpengingat::findOrFail($id);
        $pengingat->delete();

        return back()->with('success', 'Pengingat berhasil dihapus');
    }
}
