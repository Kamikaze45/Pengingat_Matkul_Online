<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Modelgedung extends Model
{
    use HasFactory;

    protected $table = 'gedung';
    protected $primaryKey = 'id_gedung';

    protected $fillable = [
        'nama_gedung',
    ];

    // Relasi ke Ruang
    public function ruangs()
    {
        return $this->hasMany(Modelruang::class, 'id_gedung', 'id_gedung');
    }

    
}