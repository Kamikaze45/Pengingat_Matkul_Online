<?php

namespace App\Http\Controllers;

use App\Models\Modelmatkul;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MatkulController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $matkuls = Modelmatkul::where('id_user', Auth::user()->id_user)->get();
       $role = strtolower($user->role ?? 'user');

        if ($role === 'dosen') {
            return view('dosen.jadwal.matkul_index', compact('matkuls'));
        }
        return view('mahasiswa.jadwal.matkul_index', compact('matkuls'));
    }

    public function store(Request $r)
    {
        $r->validate([
            'nama_matkul' => 'required',
            'sks' => 'required|integer|min:1',
        ]);

        Modelmatkul::create([
            'nama_matkul' => $r->nama_matkul,
            'sks' => $r->sks,
            'kode_matkul' => $r->kode_matkul,
            'nama_dosen' => $r->nama_dosen,
            'id_user' => Auth::user()->id_user,
        ]);

        return back()->with('success', 'Mata kuliah berhasil ditambahkan.');
    }

    public function destroy($id)
    {
        Modelmatkul::destroy($id);
        return back()->with('success', 'Mata kuliah dihapus.');
    }

    public function update(Request $r, $id)
{
    $r->validate([
        'nama_matkul' => 'required|string|max:255',
        'sks' => 'required|integer|min:1',
        'kode_matkul' => 'nullable|string|max:100',
        'nama_dosen' => 'nullable|string|max:255',
    ]);

    $matkul = Modelmatkul::where('id_user', Auth::user()->id_user)
                         ->where('id_matkul', $id)
                         ->firstOrFail();

    $matkul->update([
        'nama_matkul' => $r->nama_matkul,
        'sks' => $r->sks,
        'kode_matkul' => $r->kode_matkul,
        'nama_dosen' => $r->nama_dosen,
    ]);

    return back()->with('success', 'Mata kuliah berhasil diperbarui.');
}

}
