<?php

namespace App\Http\Controllers;

use App\Models\Modeljadwal;
use App\Models\Modelmatkul;
use App\Models\Modelruang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class JadwalController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Default: ambil jadwal yang terkait dengan mata kuliah milik user
        $jadwals = Modeljadwal::with(['mataKuliah', 'ruang'])
                    ->whereHas('mataKuliah', fn($q) => $q->where('id_user', $user->id_user))
                    ->get();
        $matkuls = Modelmatkul::where('id_user', $user->id_user)->get();
        $ruangs  = Modelruang::all();

        foreach ($jadwals as $jadwal) {
            $jadwal->warna = match($jadwal->hari ?? '') {
                'Senin'  => 'text-primary',
                'Selasa' => 'text-success',
                'Rabu'   => 'text-warning',
                'Kamis'  => 'text-info',
                'Jumat'  => 'text-danger',
                default  => 'text-dark',
            };
        }

        
        $role = strtolower($user->role ?? 'user');

        if ($role === 'dosen') {
            return view('dosen.jadwal.jadwal_saya', compact('jadwals','matkuls','ruangs'));
        }
        return view('mahasiswa.jadwal.jadwal_saya', compact('jadwals','matkuls','ruangs'));
    }

    public function store(Request $r)
    {
        $r->validate([
            'matkul' => 'required',
            'hari' => 'required',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required',
        ]);

        Modeljadwal::create($r->only('matkul','hari','jam_mulai','jam_selesai','id_ruang','kelas'));

        return redirect()->route('jadwal.index')->with('success', 'Jadwal berhasil ditambahkan.');
    }

    public function destroy($id)
    {
        Modeljadwal::destroy($id);
        return back()->with('success', 'Jadwal dihapus.');
    }

public function toggle($id)
{
    $jadwal = Modeljadwal::findOrFail($id);

    // toggle
    $jadwal->is_active = !$jadwal->is_active;
    $jadwal->save();

    return back()->with('success', 'Status jadwal berhasil diperbarui.');
}

    public function update(Request $r, $id)
{
    $r->validate([
        'matkul' => 'required',
        'hari' => 'required',
        'jam_mulai' => 'required',
        'jam_selesai' => 'required',
        'id_ruang' => 'required',
    
    ]);

    $jadwal = Modeljadwal::findOrFail($id);
    $jadwal->update($r->only('matkul','hari','jam_mulai','jam_selesai','id_ruang','kelas'));

    return redirect()->route('jadwal.index')->with('success', 'Jadwal berhasil diperbarui.');
}


    public function getJamMulaiAttribute($value)
{
    return Carbon::parse($value)->format('H:i');
}

public function getJamSelesaiAttribute($value)
{
    return Carbon::parse($value)->format('H:i');
}
}
