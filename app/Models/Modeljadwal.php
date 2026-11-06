<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Modeljadwal extends Model
{
    use HasFactory;

    protected $table = 'jadwal'; // nama tabel
    protected $primaryKey = 'id_jadwal'; // primary key

    protected $fillable = [
        'jam_mulai',
        'jam_selesai',
        'hari',
        'matkul',
        'id_ruang',
        'kelas',
        'is_active',
    ];



    // Relasi ke MataKuliah
    public function mataKuliah()
    {
        return $this->belongsTo(Modelmatkul::class, 'matkul', 'id_matkul');
    }

    // Relasi ke Ruang
    public function ruang()
    {
        return $this->belongsTo(Modelruang::class, 'id_ruang', 'id_ruang');
    }
}