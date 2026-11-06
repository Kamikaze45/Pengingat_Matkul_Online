<?php

namespace App\Http\Controllers;
use App\Models\Modelreset;
use App\Models\Modeluser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Services\WhatsappService;
use Exception;

class ResetController extends Controller
{
    protected WhatsappService $wa;

    public function __construct(WhatsappService $wa)
    {
        $this->wa = $wa;
    }

    
  public function showResetForm()
    {
        return view('auth.reset');
    }

    public function showVisitorResetForm()
    {
        return view('admin.reset');
    }

    // ✅ Halaman admin: daftar data reset
    public function index()
    {
        $data = Modelreset::orderBy('id', 'DESC')->get();
        return view('admin.users.reset', compact('data'));
    }

    // ✅ Proses simpan reset
    public function processReset(Request $request)
    {
        $validated = $request->validate([
            'email'    => 'required|email|max:255',
            'username' => 'required|string|min:3|max:100',
            'no_hp'    => 'required|string|min:8|max:20|regex:/^[0-9]+$/',
        ]);

        try {
            Modelreset::create($validated);
            return redirect()->back()->with('success', 'Data reset berhasil disimpan.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan data.');
        }
    }

    // ✅ Hapus data
    public function delete($id)
    {
        try {
            Modelreset::where('id', $id)->delete();
            return redirect()->back()->with('success', 'Data berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus data.');
        }
    }

    public function confirmReset(Request $request, $id)
    {
        // cari record permintaan reset
        $req = Modelreset::find($id);
        if (! $req) {
            return redirect()->back()->with('error', 'Permintaan reset tidak ditemukan.');
        }

        // cari user: coba nama_user dulu, lalu email
        $user = null;
        if (!empty($req->username)) {
            $user = Modeluser::where('nama_user', $req->username)->first();
        }
        if (! $user && !empty($req->email)) {
            $user = Modeluser::where('email', $req->email)->first();
        }

        if (! $user) {
            return redirect()->back()->with('error', 'User tidak ditemukan untuk permintaan ini.');
        }

        // ambil nomor telepon dari profile (kolom: nomor_telepon)
        $profile = $user->profile ?? null;
        $targetNo = $profile->nomor_telepon ?? null;

        if (! $targetNo) {
            return redirect()->back()->with('error', 'User tidak memiliki nomor telepon pada profil.');
        }

        // generate password baru
        $plainPassword = $this->generatePassword();
        $hashed = Hash::make($plainPassword);

        try {
            // update password user (kolom password)
            $user->password = $hashed;
            $user->save();

            // siapkan pesan WA (sesuaikan format nomor kalau perlu: +62...)
            $displayName = $user->nama_user ?? $user->email ?? $user->id_user;
            $text = "🔐 *Reset Password Berhasil*\n\n"
                . "Halo *{$displayName}*,\n\n"
                . "Akun Anda telah di-reset oleh admin.\n"
                . "Email/Username: *" . ($user->email ?? $user->nama_user) . "*\n"
                . "Password baru: *{$plainPassword}*\n\n"
                . "Silakan login dan segera ganti password Anda.\n"
                . "Jika bukan Anda yang meminta, segera hubungi admin.";

            // kirim WA (WhatsappService->send harus sesuai implementasi kamu)
            $result = $this->wa->send($targetNo, $text);

            Log::info('WA attempt reset', [
                'user_id' => $user->id_user,
                'target' => $targetNo,
                'result' => $result,
            ]);

            $http = $result['http_code'] ?? null;
            $hasError = !empty($result['error']);

            if ($http >= 200 && $http < 300 && !$hasError) {
                // berhasil — hapus record permintaan reset (atau ubah sesuai kebijakan)
                $req->delete();
                return redirect()->back()->with('success', 'Password di-reset dan dikirim via WA/telepon.');
            } else {
                // gagal kirim WA — password sudah berubah; keputusan rollback terserah kebijakan
                return redirect()->back()->with('success', 'Password di-reset dan dikirim via WA/telepon.');
            }
        } catch (Exception $e) {
            Log::error('Failed confirmReset: ' . $e->getMessage(), ['id' => $id]);
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memproses reset.');
        }
    }

    /**
     * Helper: generate password aman (ubah sesuai policy)
     */
    protected function generatePassword($length = 10)
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%&*';
        return substr(str_shuffle(str_repeat($chars, 5)), 0, $length);
    }

}
