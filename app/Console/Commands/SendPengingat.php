<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Modelpengingat;
use App\Models\Modeljadwal;
use App\Models\Modelruang;
use App\Models\Modeluser;        
use App\Models\Modelmatkul;
use Carbon\Carbon;
use App\Services\WhatsappService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class SendPengingat extends Command
{
    protected $signature = 'pengingat:kirim';
    protected $description = 'Kirim pengingat via WA dengan waktu fleksibel (termasuk jadwal kuliah)';

    protected WhatsappService $wa;

    public function __construct(WhatsappService $wa)
    {
        parent::__construct();
        $this->wa = $wa;
    }

    public function handle()
    {
        $lock = Cache::lock('pengingat:kirim-lock', 180);
        if (! $lock->get()) {
            $this->info('Instance lain sedang berjalan, keluar...');
            return 0;
        }

        try {
            $now = Carbon::now(config('app.timezone', 'UTC'));

            try {
                $todayName = $now->locale('id')->isoFormat('dddd');
            } catch (\Throwable $e) {
                $todayName = $now->format('l');
            }

            $start = $now->copy()->startOfMinute();
            $end   = $now->copy()->endOfMinute();

            $this->info("=== SendPengingat (Gabungan) ===");
            $this->info("Waktu sekarang : {$now}");
            $this->info("Range waktu    : {$start->format('H:i:s')} - {$end->format('H:i:s')}");
            $this->info("Hari           : {$todayName}");

            // --- Modelpengingat (sama seperti sebelumnya) ---
            $pengingatQuery = Modelpengingat::where('is_active', true)
                ->where('hari', $todayName)
                ->where(function ($q) use ($start, $end) {
                    $q->whereBetween(DB::raw("TIME(waktu)"), [
                        $start->format('H:i:s'),
                        $end->format('H:i:s')
                    ]);
                    $q->orWhereBetween('waktu', [
                        $start->format('H:i:s'),
                        $end->format('H:i:s')
                    ]);
                })
                ->with(['user.profile']);

            $this->info("Jumlah kandidat (Modelpengingat): " . $pengingatQuery->count());

            $sent = 0;
            $skippedNoNumber = 0;
            $skippedRecently = 0;

            $pengingatQuery->chunk(100, function ($chunk) use (&$sent, &$skippedNoNumber, &$skippedRecently, $now) {
                foreach ($chunk as $p) {
                    try {
                        $lastSent = $p->last_sent_at ? Carbon::parse($p->last_sent_at) : null;
                        if ($lastSent && $lastSent->diffInMinutes($now) < 1) {
                            $skippedRecently++;
                            continue;
                        }

                        $target = optional($p->user->profile)->no_wa
                            ?? optional($p->user->profile)->nomor_telepon
                            ?? null;

                        if (!$target) {
                            $skippedNoNumber++;
                            Log::warning("Tidak ada nomor WA | id_pengingat={$p->id_pengingat}");
                            continue;
                        }

                        try {
                            if ($p->waktu instanceof \Carbon\Carbon) {
                                $waktuText = $p->waktu->format('H:i');
                            } else {
                                $waktuText = Carbon::parse($p->waktu)->format('H:i');
                            }
                        } catch (\Throwable $e) {
                            $waktuText = (string) $p->waktu;
                        }

                        $text = "📌 *Pengingat Kegiatan*\n\n"
                            . "*Judul:* " . ($p->judul ?: '-') . "\n"
                            . "*Hari:* {$p->hari}\n"
                            . "*Waktu:* {$waktuText}\n\n"
                            . ($p->pesan ?: '-') . "\n\n"
                            . "Terima kasih.";

                        $result = $this->wa->send($target, $text);

                        Log::info("WA attempt (pengingat)", [
                            'id' => $p->id_pengingat,
                            'target' => $target,
                            'http_code' => $result['http_code'] ?? null,
                            'error' => $result['error'] ?? null,
                        ]);

                        $http = $result['http_code'] ?? null;
                        $hasError = !empty($result['error']);

                        if ($http >= 200 && $http < 300 && !$hasError) {
                            $p->update(['last_sent_at' => Carbon::now()]);
                            $sent++;
                        }
                    } catch (\Throwable $e) {
                        Log::error("Gagal kirim pengingat id={$p->id_pengingat}: {$e->getMessage()}");
                    }
                }
            });

         // --- Modeljadwal (Pengingat jadwal kuliah) ---
// Kirim pengingat 15 menit sebelum jam_mulai
$sendOffsetMinutes = 15;

$jadwalQuery = Modeljadwal::where('is_active', 1)              // hanya jadwal aktif
    ->where('hari', $todayName)
    // ambil jadwal yang jam_mulai-nya adalah now + $sendOffsetMinutes (per menit)
    ->whereBetween(DB::raw("TIME(jam_mulai)"), [
        $start->copy()->addMinutes($sendOffsetMinutes)->format('H:i:s'),
        $end->copy()->addMinutes($sendOffsetMinutes)->format('H:i:s'),
    ])
    ->with(['mataKuliah.dosen.profile', 'ruang']);             // eager load lengkap

$this->info("Jumlah kandidat (Modeljadwal): " . $jadwalQuery->count());

$jadwalQuery->chunk(100, function ($chunk) use (&$sent, &$skippedNoNumber, $now, $sendOffsetMinutes) {

    foreach ($chunk as $j) {
        try {
            $id = $j->getKey();

            // Anti duplikasi (1x per 2 menit)
            $cacheKey = "jadwal_sent:{$id}:" . $now->format('YmdHi');
            if (Cache::has($cacheKey)) {
                continue;
            }

            // --- Ambil nomor WA ---
            $target = null;

            // 1) Jika jadwal punya relasi user() langsung
            if (method_exists($j, 'user')) {
                $usr = $j->user()->with('profile')->first();
                $target = optional($usr->profile)->no_wa
                    ?? optional($usr->profile)->nomor_telepon
                    ?? null;
            }

            // 2) Lewat dosen pada mata kuliah
            if (!$target && $j->mataKuliah && $j->mataKuliah->dosen) {
                $dosen = $j->mataKuliah->dosen;
                $target = optional($dosen->profile)->no_wa
                    ?? optional($dosen->profile)->nomor_telepon
                    ?? null;
            }

            // 3) Fallback: id_user dari mata kuliah
            if (!$target && $j->mataKuliah && $j->mataKuliah->id_user) {
                $u = Modeluser::with('profile')->find($j->mataKuliah->id_user);
                $target = optional($u->profile)->no_wa
                    ?? optional($u->profile)->nomor_telepon
                    ?? null;
            }

            if (!$target) {
                $skippedNoNumber++;
                Log::warning("Tidak ada nomor WA untuk jadwal | id_jadwal={$id}");
                continue;
            }

            // --- Format waktu ---
            try {
                $jamMulai = Carbon::parse($j->jam_mulai);
                $jamMulaiText = $jamMulai->format('H:i');
            } catch (\Throwable $e) {
                $jamMulaiText = (string) $j->jam_mulai;
            }

            try {
    $jamSelesai = Carbon::parse($j->jam_selesai);
    $jamSelesaiText = $jamSelesai->format('H:i');
} catch (\Throwable $e) {
    $jamSelesaiText = (string) $j->jam_selesai;
}

            // --- Data jadwal ---
            $namaMatkul = $j->mataKuliah->nama_matkul ?? '-';
            $namaRuang  = $j->ruang->nama_ruang ?? '-';
           $gedungText = $j->ruang->gedung->nama_gedung ?? '-';

            // --- Template pesan ---
           $text = "📌 *PENGINGAT KULIAH*\n"
    . "────────────────────\n"
    . "📚 *Mata Kuliah:* {$namaMatkul}\n"
    . "📅 *Hari:* {$j->hari}\n"
    . "⏰ *Jam Mulai:* {$jamMulaiText}\n - {$jamSelesaiText}\n"
    . "🏫 *Ruang:* {$namaRuang}\n"
    . "🏢 *Gedung:* {$gedungText}\n"
    . "────────────────────\n"
    . "⏳ Pengingat dikirim *{$sendOffsetMinutes} menit* sebelum kelas dimulai.\n"
    . "💡 Jangan terlambat ya!";

            // --- Kirim pesan ---
            $result = $this->wa->send($target, $text);

            Log::info("WA attempt (jadwal)", [
                'id_jadwal' => $id,
                'target'    => $target,
                'http_code' => $result['http_code'] ?? null,
                'error'     => $result['error'] ?? null,
            ]);

            $http = $result['http_code'] ?? null;
            $hasError = !empty($result['error']);

            if ($http >= 200 && $http < 300 && !$hasError) {
                Cache::put($cacheKey, true, 120);
                $sent++;
            }

        } catch (\Throwable $e) {
            Log::error("Gagal kirim jadwal id={$j->getKey()}: {$e->getMessage()}");
        }
    }

});



            $this->info("=== SELESAI ===");
            $this->info("Terkirim           : {$sent}");
            $this->info("Skip tanpa nomor   : {$skippedNoNumber}");
            $this->info("Skip duplikasi     : {$skippedRecently}");

        } finally {
            try {
                $lock->release();
            } catch (\Throwable $e) {
                Log::warning("Gagal melepaskan lock: " . $e->getMessage());
            }
        }

        return 0;
    }
}
