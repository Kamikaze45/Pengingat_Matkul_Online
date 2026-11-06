<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Modeluser;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // Tampilkan halaman login
    public function showLogin()
    {
        return view('auth.login');
    }

    // Proses login
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = Modeluser::where('email', $request->email)->first();

        // Cek user dan password
        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->with('error', 'Email atau password salah')->withInput(['email' => $request->email]);
        }

        // Cek status akun
        if ($user->status === 'pending') {
            return back()->with('error', 'Akun Anda masih menunggu persetujuan admin.');
        }

        if ($user->status === 'rejected') {
            return back()->with('error', 'Akun Anda telah ditolak. Hubungi admin.');
        }

        // Login berhasil
        Auth::login($user);
        $request->session()->regenerate();

        // Redirect sesuai role
        switch ($user->role) {
            case 'admin':
                return redirect()->route('dashboard.admin');
            case 'dosen':
                return redirect()->route('dashboard.dosen');
            default:
                return redirect()->route('dashboard.user');
        }
    }

    // Logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    // Halaman register
    public function showRegister()
    {
        return view('auth.register');
    }

    // Proses register
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:userr,email',
            'password' => 'required|confirmed|min:6',
             'role' => 'required|in:user,dosen',
        ]);

        Modeluser::create([
            'nama_user' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'status' => 'pending',
        ]);

        return redirect()->route('login')->with('success', 'Registrasi berhasil, akun menunggu persetujuan admin.');
    }
}