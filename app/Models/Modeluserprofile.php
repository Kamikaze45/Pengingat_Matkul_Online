<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $user_id
 * @property string|null $foto_profil
 * @property string|null $alamat
 * @property string|null $nomor_telepon
 * @property \Illuminate\Support\Carbon|null $tanggal_lahir
 * @property string|null $bio
 * @property string|null $media_sosial
 * @property string|null $jenis_kelamin
 */
class Modeluserprofile extends Model
{
    use HasFactory;

    // Nama tabel
    protected $table = 'user_profiles';

    // Primary key
    protected $primaryKey = 'id_profile';

    // Auto increment
    public $incrementing = true;

    // Tipe primary key
    protected $keyType = 'int';

    // Mass assignable fields
    protected $fillable = [
        'user_id',
        'nip',
        'alamat',
        'nomor_telepon',
        'tanggal_lahir',
        'foto_profil',
        'bio',
        'media_sosial',
        'jenis_kelamin',
    ];

    // Casting fields
    protected $casts = [
        'tanggal_lahir' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relasi belongsTo ke Modeluser
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(Modeluser::class, 'user_id', 'id_user');
    }
}
