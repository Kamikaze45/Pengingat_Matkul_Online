<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property-read \App\Models\Modeluserprofile|null $profile
 * @method HasOne profile()
 */
class Modeluser extends Authenticatable
{
    use HasFactory;

    // Nama tabel
    protected $table = 'userr';

    // Primary key
    protected $primaryKey = 'id_user';

    // Auto increment
    public $incrementing = true;

    // Tipe primary key
    protected $keyType = 'int';

    // Mass assignable fields
    protected $fillable = [
        'nama_user',
        'email',
        'password',
        'role',
        'status',
    ];

    // Hidden fields (misal password)
    protected $hidden = [
        'password',
    ];

    // Casting fields
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relasi hasOne ke Modeluserprofile
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function profile(): HasOne
    {
        return $this->hasOne(Modeluserprofile::class, 'user_id', 'id_user');
    }

    public function pengingat()
{
    return $this->hasMany(Modelpengingat::class, 'id_user', 'id_user');
}

}
