<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Modelvisit extends Model
{
    use HasFactory;

    // Nama tabel
    protected $table = 'visitors';

    // Primary key
    protected $primaryKey = 'id';

    // Tipe primary key
    protected $keyType = 'bigint';

    // Auto increment
    public $incrementing = true;

    // Timestamps
    public $timestamps = false; // karena kita pakai visited_at manual

    // Fillable (kolom yang bisa diisi massal)
    protected $fillable = [
        'ip_address',
        'visited_at'
    ];
}
