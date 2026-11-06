<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Modelreset extends Model
{
    use HasFactory;

    protected $table = 'reset'; // nama tabel
    protected $fillable = [
        'email',
        'username',
        'no_hp',
    ];

    // Kalau tabel punya id auto increment, tidak perlu atur primaryKey & incrementing
    public $timestamps = false; // jika tabel tidak punya created_at & updated_at
}
