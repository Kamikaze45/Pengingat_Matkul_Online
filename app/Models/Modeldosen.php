<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Dosen extends Model
{
    use HasFactory;

    protected $table = 'dosen'; // nama tabel

    protected $primaryKey = 'id_dosen'; // primary key

    protected $fillable = [
        'nama_dosen',
        'nip'
    ];
}
