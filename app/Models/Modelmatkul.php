<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Modelmatkul extends Model
{
    use HasFactory;

    protected $table = 'mata_kuliah'; // nama tabel
    protected $primaryKey = 'id_matkul'; // primary key

    // kolom yang bisa diisi lewat mass assignment
    protected $fillable = [
        'nama_matkul',
        'sks',
        'kode_matkul',
        'id_user',
        'nama_dosen',
    ];

    // relasi ke dosen (users dengan role 'dosen')
    public function dosen()
    {
        return $this->belongsTo(Modeluser::class, 'id_user', 'id_user');
    }

    // jika ingin menambahkan jadwal yang terkait
    public function jadwals()
    {
        return $this->hasMany(Modeljadwal::class, 'matkul', 'id_matkul');
    }
}