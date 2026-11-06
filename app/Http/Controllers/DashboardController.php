<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; 
use App\Models\Modeljadwal;
use App\Models\Modelgedung;
use App\Models\Modelpengingat;
use App\Models\Modeluser;
use App\Models\Modelmatkul;
use App\Models\Modelvisit;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // Pintu tunggal untuk dashboard — arahkan sesuai role
    public function index()
    {
        $user = Auth::user();

        switch ($user->role) {
            case 'admin':
                return redirect()->route('dashboard.admin');
            case 'dosen':
                return redirect()->route('dashboard.dosen');
            default:
                return redirect()->route('dashboard.user');
        }
    }

    // Jika kamu juga punya method-method spesifik:
  public function admin()
{
    // Hitung jumlah tiap role
 $userRegistrations = Modeluser::where('role', 'user')
    ->where('status', 'approved')
    ->count();

 $dosenRegistrations = Modeluser::where('role', 'dosen')
    ->where('status', 'approved')
    ->count();
    $adminRegistrations = Modeluser::where('role', 'admin')->count();
    $approvedRegistrations = Modeluser::where('status', 'pending')->count();

    // Doughnut chart data (opsional, bisa langsung pakai $userRegistrations dll)
    $roleData = [
        'admin' => $adminRegistrations,
        'dosen' => $dosenRegistrations,
        'user'  => $userRegistrations,
    ];

    // Hitung unique visitor hari ini
    $uniqueVisitor = DB::table('visitors')
        ->whereDate('visited_at', now())
        ->distinct('ip_address')
        ->count('ip_address');

    // Ambil unique visitor per hari selama 7 hari terakhir
    $dates = [];
    $visitorsData = [];

    for ($i = 6; $i >= 0; $i--) {
        $date = Carbon::today()->subDays($i)->format('Y-m-d');
        $dates[] = $date;

        $count = DB::table('visitors')
            ->whereDate('visited_at', $date)
            ->distinct('ip_address')
            ->count('ip_address');

        $visitorsData[] = $count;
    }
 
    return view('admin.dashboard.index', [
        'userRegistrations'  => $userRegistrations,
        'dosenRegistrations' => $dosenRegistrations,
        'adminRegistrations' => $adminRegistrations,
        'uniqueVisitor'      => $uniqueVisitor,
        'roleData'           => $roleData,
        'dates'              => $dates,
        'visitorsData'       => $visitorsData,
        'approvedRegistrations'=> $approvedRegistrations,
    ]);
}


   public function dosen()
{
    $user = Auth::user();
    if (! $user) {
        return redirect()->route('login');
    }

    $userId = $user->id_user ?? Auth::id();

    // jumlah semua pengingat aktif milik user
    $jumlahPengingat = Modelpengingat::where('id_user', $userId)
        ->where('is_active', 1)
        ->count();

    // pengingat mendatang: gunakan scope upcomingForUser yang sudah ada di model
    // scope akan mengembalikan pengingat mulai dari hari ini (hanya waktu >= now untuk hari ini) lalu hari berikutnya
    $pengingatMendatang = Modelpengingat::upcomingForUser($userId)
        ->limit(10) // opsional: batasi jumlah item yang ditampilkan
        ->get();

    // nama hari saat ini dalam bahasa Indonesia, e.g. "Senin"
    // pastikan locale 'id' tersedia di sistem
    $hariDatabase = Carbon::now()->locale('id')->translatedFormat('l');

    $jadwalHariIni = Modeljadwal::where('hari', $hariDatabase)
        ->whereHas('mataKuliah', function($q) use ($userId) {
            $q->where('id_user', $userId);
        })
        ->with(['mataKuliah', 'ruang.gedung'])
        ->orderBy('jam_mulai')
        ->get();

    $jumlahJadwal = $jadwalHariIni->count();
     $jumlahMatkul = Modelmatkul::where('id_user', $userId)->count();
    return view('dosen.dashboard.index', [
        'jadwalHariIni'      => $jadwalHariIni,
        'hariInText'         => $hariDatabase,
        'jumlahJadwal'       => $jumlahJadwal,
        'jumlahPengingat'    => $jumlahPengingat,
        'pengingatMendatang' => $pengingatMendatang,
        'jumlahMatkul'       => $jumlahMatkul,
    ]);
}


    public function user()
    {
        $user = Auth::user();
    if (! $user) {
        return redirect()->route('login');
    }

    $userId = $user->id_user ?? Auth::id();

    // jumlah semua pengingat aktif milik user
    $jumlahPengingat = Modelpengingat::where('id_user', $userId)
        ->where('is_active', 1)
        ->count();

    // pengingat mendatang: gunakan scope upcomingForUser yang sudah ada di model
    // scope akan mengembalikan pengingat mulai dari hari ini (hanya waktu >= now untuk hari ini) lalu hari berikutnya
    $pengingatMendatang = Modelpengingat::upcomingForUser($userId)
        ->limit(10) // opsional: batasi jumlah item yang ditampilkan
        ->get();

    // nama hari saat ini dalam bahasa Indonesia, e.g. "Senin"
    // pastikan locale 'id' tersedia di sistem
    $hariDatabase = Carbon::now()->locale('id')->translatedFormat('l');

    $jadwalHariIni = Modeljadwal::where('hari', $hariDatabase)
        ->whereHas('mataKuliah', function($q) use ($userId) {
            $q->where('id_user', $userId);
        })
        ->with(['mataKuliah', 'ruang'])
        ->orderBy('jam_mulai')
        ->get();

    $jumlahJadwal = $jadwalHariIni->count();
    $jumlahMatkul = Modelmatkul::where('id_user', $userId)->count();

    return view('mahasiswa.dashboard.index', [
        'jadwalHariIni'      => $jadwalHariIni,
        'hariInText'         => $hariDatabase,
        'jumlahJadwal'       => $jumlahJadwal,
        'jumlahPengingat'    => $jumlahPengingat,
        'pengingatMendatang' => $pengingatMendatang,
        'jumlahMatkul'       => $jumlahMatkul,
    ]);
    }
}