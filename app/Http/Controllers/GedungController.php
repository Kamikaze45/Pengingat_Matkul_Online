<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Modelgedung;

class GedungController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_gedung' => 'required|string|max:255|unique:gedung,nama_gedung',
        ]);

        Modelgedung::create($data);

        return redirect()->route('schedule.index', ['tab' => 'gedung'])
            ->with('success', 'Gedung berhasil ditambahkan.');
    }

    public function edit(Modelgedung $gedung)
    {
        // not used if edit handled via modal + JS, kept for API use
        return response()->json($gedung);
    }

    public function update(Request $request, Modelgedung $gedung)
    {
        $data = $request->validate([
            'nama_gedung' => 'required|string|max:255|unique:gedung,nama_gedung,' . $gedung->id_gedung . ',id_gedung',
        ]);

        $gedung->update($data);

        return redirect()->route('schedule.index', ['tab' => 'gedung'])
            ->with('success', 'Gedung berhasil diupdate.');
    }

    public function destroy(Modelgedung $gedung)
    {
        $gedung->delete();

        return redirect()->route('schedule.index', ['tab' => 'gedung'])
            ->with('success', 'Gedung berhasil dihapus.');
    }
}
