<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Modelruang;
use App\Models\Modelgedung;

class RuangController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_ruang' => 'required|string|max:255',
            'id_gedung'  => 'required|exists:gedung,id_gedung',
            'kapasitas'  => 'nullable|integer|min:0',
        ]);

        Modelruang::create($data);

        return redirect()->route('schedule.index', ['tab' => 'ruang'])
            ->with('success', 'Ruang berhasil ditambahkan.');
    }

    public function edit(Modelruang $ruang)
    {
        return response()->json($ruang->load('gedung'));
    }

    public function update(Request $request, Modelruang $ruang)
    {
        $data = $request->validate([
            'nama_ruang' => 'required|string|max:255',
            'id_gedung'  => 'required|exists:gedung,id_gedung',
            'kapasitas'  => 'nullable|integer|min:0',
        ]);

        $ruang->update($data);

        return redirect()->route('schedule.index', ['tab' => 'ruang'])
            ->with('success', 'Ruang berhasil diupdate.');
    }

    public function destroy(Modelruang $ruang)
    {
        $ruang->delete();

        return redirect()->route('schedule.index', ['tab' => 'ruang'])
            ->with('success', 'Ruang berhasil dihapus.');
    }
}
