<?php

namespace App\Http\Controllers;
use App\Models\Modeljadwal;
use App\Models\Modelmatkul;
use App\Models\Modelgedung;
use App\Models\Modelruang;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{

public function index(Request $request)
{
    $qParam = $request->query('q');           // global search
    $tab = $request->query('tab', 'gedung');
    $perPage = (int) $request->query('per', 12);

    // Jadwal: eager load relations (adjust relation names if different)
    $jadwals = Modeljadwal::with(['mataKuliah', 'mataKuliah.dosen', 'ruang', 'ruang.gedung'])
        ->when($qParam, function($query, $qParam) {
            // group the OR conditions so they don't break other where clauses
            $query->where(function($sub) use ($qParam) {
                $sub->whereHas('mataKuliah', function($q2) use ($qParam) {
                        $q2->where('nama_matkul', 'like', "%{$qParam}%")
                           ->orWhere('kode_matkul', 'like', "%{$qParam}%");
                    })
                    ->orWhereHas('ruang', function($q4) use ($qParam) {
                        $q4->where('nama_ruang', 'like', "%{$qParam}%");
                    })
                    ->orWhereHas('ruang.gedung', function($q5) use ($qParam) {
                        $q5->where('nama_gedung', 'like', "%{$qParam}%");
                    });
            });
        })
        // order hari in natural weekday order (adjust the list to your language)
        ->orderByRaw("FIELD(hari, 'senin','selasa','rabu','kamis','jumat','sabtu','minggu')")
        ->orderBy('jam_mulai')
        ->paginate($perPage, ['*'], 'jadwals_page')
        ->withQueryString();

    // Mata kuliah
    $matkuls = Modelmatkul::when($qParam, function($query) use ($qParam) {
            $query->where('nama_matkul', 'like', "%{$qParam}%")
                  ->orWhere('kode_matkul', 'like', "%{$qParam}%");
        })
        ->orderBy('nama_matkul')
        ->paginate($perPage, ['*'], 'matkuls_page')
        ->withQueryString();

    // Gedung
    $gedungs = Modelgedung::when($qParam, function($query) use ($qParam) {
            $query->where('nama_gedung', 'like', "%{$qParam}%");
        })
        ->orderBy('nama_gedung')
        ->paginate($perPage, ['*'], 'gedungs_page')
        ->withQueryString();

    // Ruang (eager load gedung)
    $ruangs = Modelruang::with('gedung')
        ->when($qParam, function($query) use ($qParam) {
            $query->where('nama_ruang', 'like', "%{$qParam}%")
                  ->orWhereHas('gedung', function($q) use ($qParam) {
                      $q->where('nama_gedung', 'like', "%{$qParam}%");
                  });
        })
        ->orderBy('nama_ruang')
        ->paginate($perPage, ['*'], 'ruangs_page')
        ->withQueryString();

    return view('admin.schedule.index', compact('jadwals','matkuls','gedungs','ruangs','tab','qParam','perPage'));
}

    public function jadwal()
    {
        // Data statik untuk mockup
       $schedules = [
    ['hari' => 'Senin', 'jam' => '07:00 - 08:40', 'mata_kuliah' => 'Matematika Diskrit', 'ruang' => 'Ruang 101', 'dosen' => 'Dr. Ahmad'],
    ['hari' => 'Senin', 'jam' => '09:00 - 10:40', 'mata_kuliah' => 'Algoritma dan Struktur Data', 'ruang' => 'Ruang 102', 'dosen' => 'Bu Siti'],
    ['hari' => 'Senin', 'jam' => '11:00 - 12:40', 'mata_kuliah' => 'Basis Data', 'ruang' => 'Ruang 103', 'dosen' => 'Pak Budi'],
    ['hari' => 'Selasa', 'jam' => '07:00 - 08:40', 'mata_kuliah' => 'Pemrograman Web', 'ruang' => 'Ruang 201', 'dosen' => 'Bu Ani'],
    ['hari' => 'Selasa', 'jam' => '09:00 - 10:40', 'mata_kuliah' => 'Sistem Operasi', 'ruang' => 'Ruang 202', 'dosen' => 'Dr. Rudi'],
    ['hari' => 'Rabu', 'jam' => '07:00 - 08:40', 'mata_kuliah' => 'Jaringan Komputer', 'ruang' => 'Ruang 301', 'dosen' => 'Pak Andi'],
    ['hari' => 'Rabu', 'jam' => '09:00 - 10:40', 'mata_kuliah' => 'Rekayasa Perangkat Lunak', 'ruang' => 'Ruang 302', 'dosen' => 'Bu Diah'],
    ['hari' => 'Kamis', 'jam' => '07:00 - 08:40', 'mata_kuliah' => 'Kecerdasan Buatan', 'ruang' => 'Ruang 401', 'dosen' => 'Dr. Sari'],
    ['hari' => 'Kamis', 'jam' => '09:00 - 10:40', 'mata_kuliah' => 'Analisis dan Desain Sistem', 'ruang' => 'Ruang 402', 'dosen' => 'Pak Budi'],
    ['hari' => 'Jumat', 'jam' => '07:00 - 08:40', 'mata_kuliah' => 'Manajemen Proyek TI', 'ruang' => 'Ruang 501', 'dosen' => 'Bu Rina'],
    ['hari' => 'Jumat', 'jam' => '09:00 - 10:40', 'mata_kuliah' => 'Keamanan Jaringan', 'ruang' => 'Ruang 502', 'dosen' => 'Pak Doni'],
];


        return view('mahasiswa.jadwal.index', compact('schedules'));
    }
}
