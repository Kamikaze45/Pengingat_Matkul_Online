<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Middleware\LogVisitor;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\GedungController;
use App\Http\Controllers\RuangController;
use App\Http\Controllers\PengingatController;
use App\Http\Controllers\JadwalController;
use App\Http\Controllers\MatkulController; 
use App\Http\Controllers\ResetController;


// Halaman awal
Route::get('/', function () {
    return view('welcome');
});

// Auth routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Dashboard routes: hanya bisa diakses user login
Route::middleware(['auth', LogVisitor::class])->group(function () {

    // Pintu tunggal dashboard, mengarahkan sesuai role
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Halaman khusus per role
    Route::get('/admin/dashboard', [DashboardController::class, 'admin'])->name('dashboard.admin');
    Route::get('/dosen/dashboard', [DashboardController::class, 'dosen'])->name('dashboard.dosen');
    Route::get('/mahasiswa/dashboard', [DashboardController::class, 'user'])->name('dashboard.user');
});

// User management routes untuk admin
Route::get('/admin/users', [UserManagementController::class, 'index'])->name('admin.users');
Route::post('/admin/users/approve/{id}', [UserManagementController::class, 'approve'])->name('admin.users.approve');
Route::post('/admin/users/pending/{id}', [UserManagementController::class, 'pending'])->name('admin.users.pending');

Route::delete('/admin/users/{id}', [UserManagementController::class, 'delete'])->name('admin.users.delete');
Route::post('/admin/users/{id_user}/reset-password', [UserManagementController::class, 'resetPassword'])
    ->name('admin.users.resetPassword');



Route::middleware(['auth'])->group(function () {
    Route::get('/jadwal', [ScheduleController::class, 'jadwal'])->name('schedule.jadwal');
    Route::get('/admin/jadwal', [ScheduleController::class, 'index'])->name('schedule.index');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'profile'])->name('profile.view');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    
});

Route::middleware(['auth'])->group(function () {
    Route::resource('gedung', GedungController::class)->except(['show']);
    Route::resource('ruang', RuangController::class)->except(['show']);
});

//pengingat routes
Route::middleware(['auth'])->group(function () {

    Route::get('/pengingat', [PengingatController::class, 'index'])->name('pengingat.index');
    Route::get('/pengingat/create', [PengingatController::class, 'create'])->name('pengingat.create');
    Route::post('/pengingat/store', [PengingatController::class, 'store'])->name('pengingat.store');

    Route::get('/pengingat/{id}/edit', [PengingatController::class, 'edit'])->name('pengingat.edit');
    Route::post('/pengingat/{id}', [PengingatController::class, 'update'])->name('pengingat.update');

    Route::post('/pengingat/toggle/{id}', [PengingatController::class, 'toggle'])->name('pengingat.toggle');

    Route::delete('/pengingat/{id}', [PengingatController::class, 'destroy'])->name('pengingat.destroy');

});

Route::middleware(['auth'])->group(function () {
    Route::get('/jadwal', [JadwalController::class, 'index'])->name('jadwal.index');
    Route::post('/jadwal', [JadwalController::class, 'store'])->name('jadwal.store');
    Route::delete('/jadwal/{id}', [JadwalController::class, 'destroy'])->name('jadwal.destroy');
    Route::put('/jadwal/{id}', [JadwalController::class, 'update'])->name('jadwal.update');
    Route::patch('/jadwal/{id}/toggle', [JadwalController::class, 'toggle'])->name('jadwal.toggle');



    Route::get('/matkul', [MatkulController::class, 'index'])->name('matkul.index');
    Route::post('/matkul', [MatkulController::class, 'store'])->name('matkul.store');
    Route::delete('/matkul/{id}', [MatkulController::class, 'destroy'])->name('matkul.destroy');    
    Route::put('/matkul/{id}', [App\Http\Controllers\MatkulController::class, 'update'])->name('matkul.update');

});

Route::get('/pengingat/kirim', [PengingatController::class, 'kirimPengingat'])
    ->middleware('auth'); // opsional, bisa pakai middleware admin

Route::post('/kirim-pengingat-now', [PengingatController::class, 'kirimNow'])
    ->name('kirim.now');

Route::get('/login/reset', [ResetController::class, 'showResetForm'])->name('password.reset');
Route::post('/login/reset', [ResetController::class, 'processReset'])->name('password.reset.post');

Route::get('/admin/reset', [ResetController::class, 'index'])->name('admin.reset.list');
Route::delete('/admin/reset/delete/{id}', [ResetController::class, 'delete'])->name('admin.reset.delete');
 Route::post('/reset/confirm/{id}', [ResetController::class, 'confirmReset'])->name('admin.reset.confirm');



//php artisan view:clear
//php artisan cache:clear
//php artisan config:clear
//php artisan route:clear