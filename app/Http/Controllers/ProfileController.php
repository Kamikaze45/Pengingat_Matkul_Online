<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfileRequest;
use App\Models\Modeluser;
use App\Models\Modeluserprofile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;


class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
public function profile(): View
{
    $user = Auth::user();
    $profile = $user->profile ?? new Modeluserprofile(['user_id' => $user->id_user ?? $user->id]);

    $role = strtolower(trim($user->role ?? 'user'));

    switch ($role) {
        case 'admin':
            $view = 'admin.adminprofile.view';
            break;
        case 'dosen':
            $view = 'dosen.dosenprofile.view';
            break;
        default:
            $view = 'mahasiswa.userprofile.view';
    }

    return view($view, compact('user', 'profile', 'role'));
}

    /**
     * Tampilkan halaman edit profil.
     *
     * @return View
     */
   public function edit(): View
{
    $user = Auth::user();

    // pastikan ada instance profile agar view aman
    $profile = $user->profile ?? new Modeluserprofile(['user_id' => $user->id_user ?? $user->id]);

    // normalisasi role
    $role = strtolower(trim($user->role ?? 'user'));

    // pilih view edit berdasarkan role
    switch ($role) {
            case 'admin':
                $view = 'admin.adminprofile.edit';   // resources/views/profile/admin.blade.php
                break;
            case 'dosen':
                $view = 'dosen.dosenprofile.edit';   // resources/views/profile/dosen.blade.php
                break;
            default:
                $view = 'mahasiswa.userprofile.edit';    // resources/views/profile/user.blade.php
        }

    return view($view, compact('user', 'profile', 'role'));
}

    /**
     * Update data profil user.
     *
     * @param  UpdateProfileRequest  $request
     * @return RedirectResponse
     */
    public function update(UpdateProfileRequest $request): RedirectResponse
{
    $user = Modeluser::findOrFail(Auth::id());

    DB::beginTransaction();

    try {

        /**
         * ======================
         * UPDATE PASSWORD (OPSIONAL)
         * ======================
         */
        if ($request->filled('password')) {

            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors([
                    'current_password' => 'Password saat ini tidak sesuai.'
                ])->withInput();
            }

            $user->password = Hash::make($request->password);
            $user->save();
        }

        /**
         * ======================
         * UPDATE PROFIL
         * ======================
         */
        $profile = Modeluserprofile::firstOrNew([
            'user_id' => $user->id_user
        ]);

        if ($request->hasFile('foto_profil')) {
            $file = $request->file('foto_profil');
            $path = $file->store('profile_photos', 'public');

            if ($profile->foto_profil && Storage::disk('public')->exists($profile->foto_profil)) {
                Storage::disk('public')->delete($profile->foto_profil);
            }

            $profile->foto_profil = $path;
        }

        $profile->nip           = $request->nip;
        $profile->nomor_telepon = $request->nomor_telepon;
        $profile->alamat        = $request->alamat;
        $profile->bio           = $request->bio;
        $profile->media_sosial  = $request->media_sosial;
        $profile->jenis_kelamin = $request->jenis_kelamin;
        $profile->user_id       = $user->id_user;

        if ($request->filled('tanggal_lahir')) {
            $profile->tanggal_lahir = $request->tanggal_lahir;
        }

        $profile->save();

        DB::commit();

        return redirect()
            ->route('profile.view')
            ->with('success', 'Profil & password berhasil diperbarui');

    } catch (\Throwable $e) {

        DB::rollBack();

        return back()->withErrors([
            'error' => 'Terjadi kesalahan saat menyimpan data'
        ]);
    }
}
}
