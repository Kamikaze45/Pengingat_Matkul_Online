<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Modelruang extends Model
{
    use HasFactory;

    protected $table = 'ruang';
    protected $primaryKey = 'id_ruang';

    protected $fillable = [
        'nama_ruang',
        'id_gedung',
        'kapasitas',
    ];

    // Relasi ke Gedung
    public function gedung()
    {
        return $this->belongsTo(Modelgedung::class, 'id_gedung', 'id_gedung');
    }

    // Relasi ke Jadwal
    public function jadwals()
    {
        return $this->hasMany(Modeljadwal::class, 'id_ruang', 'id_ruang');
    }
}
