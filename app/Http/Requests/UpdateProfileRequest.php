<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    // Otorisasi request (ubah ke true jika semua user boleh update)
    public function authorize(): bool
    {
        return true;
    }

    // Aturan validasi
    public function rules(): array
    {
        return [
            'alamat'        => 'nullable|string|max:255',
            'nomor_telepon' => 'nullable|string|max:20',
            'tanggal_lahir' => 'nullable|date|before:today',
            'foto_profil'   => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'bio'           => 'nullable|string',
            'media_sosial'  => 'nullable|string|max:255',
            'jenis_kelamin' => 'nullable|in:L,P',
        ];
    }

    // Pesan kustom (opsional)
    public function messages(): array
    {
        return [
            'tanggal_lahir.before' => 'Tanggal lahir harus sebelum hari ini.',
            'foto_profil.image'    => 'File harus berupa gambar.',
            'foto_profil.mimes'    => 'Format foto harus jpeg, png, jpg, atau gif.',
            'foto_profil.max'      => 'Ukuran foto maksimal 2MB.',
            'jenis_kelamin.in'     => 'Jenis kelamin harus L (Laki-laki) atau P (Perempuan).',
        ];
    }
}