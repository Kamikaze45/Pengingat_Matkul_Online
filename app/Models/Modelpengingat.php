<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Modelpengingat extends Model
{
    use HasFactory;

    protected $table = 'pengingat';
    protected $primaryKey = 'id_pengingat';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'id_user',
        'judul',
        'pesan',
        'hari',   // expects ENUM: 'Senin','Selasa',...,'Minggu'
        'waktu',  // TIME (HH:MM:SS) nullable
        'is_active',
        'last_sent_at',
    ];

    protected $casts = [
        'hari'      => 'string',
        'waktu'     => 'datetime:H:i:s',
        'is_active' => 'boolean',
        'last_sent_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(Modeluser::class, 'id_user', 'id_user');
    }

    /**
     * Accessor: return Carbon instance for waktu (time) or null.
     * Use $model->waktu_carbon to get Carbon or null.
     */
    public function getWaktuCarbonAttribute()
    {
        $w = $this->attributes['waktu'] ?? null;
        if (empty($w)) {
            return null;
        }

        // Try common formats 'H:i:s' then 'H:i'
        try {
            return Carbon::createFromFormat('H:i:s', $w);
        } catch (\Throwable $e) {
            try {
                return Carbon::createFromFormat('H:i', $w);
            } catch (\Throwable $e) {
                return null;
            }
        }
    }

    /**
     * Accessor: human-readable nama hari (fallback '-').
     * Use $model->nama_hari
     */
    public function getNamaHariAttribute()
    {
        $map = [
            'Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu'
        ];
        $hari = $this->attributes['hari'] ?? null;
        if (!$hari) return '-';
        // return as-is if matches expected, otherwise try to map numeric
        if (in_array($hari, $map, true)) {
            return $hari;
        }
        // if someone stored numeric 1..7, map it:
        if (is_numeric($hari)) {
            $idx = (int)$hari - 1;
            return $map[$idx] ?? '-';
        }
        return $hari;
    }

    /**
     * Scope: only active reminders for given user
     */
    public function scopeActiveForUser($query, $userId)
    {
        return $query->where('id_user', $userId)->where('is_active', true);
    }

    /**
     * Scope: upcoming reminders for a user based on 'hari' (ENUM strings) and 'waktu'.
     *
     * Logic:
     * - include reminders whose hari is after today (in the same week)
     * - include reminders for today only if waktu IS NULL (all-day) OR waktu >= now()
     * - order starting from today and wrap to next week
     */
   public function scopeUpcomingForUser($query, $userId)
{
    $days = ['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu'];

    // Monday=1 .. Sunday=7
    $todayIso = now()->dayOfWeekIso; // 1..7
    $nowTime = now()->format('H:i:s');

    // rotate days so order starts from today
    $startIndex = $todayIso - 1; // convert 1..7 -> 0..6
    $orderDays = array_merge(
        array_slice($days, $startIndex),
        array_slice($days, 0, $startIndex)
    );

    // build FIELD(...) for ordering with correct sequence
    $fieldOrder = "FIELD(hari, " . implode(',', array_map(fn($d) => "'" . $d . "'", $orderDays)) . ")";

    return $query->where('id_user', $userId)
                 ->where('is_active', true)
                 ->where(function($q) use ($days, $todayIso, $nowTime) {
                     $todayName = $days[$todayIso - 1];

                     // include future days (hari != today)
                     $q->where('hari', '!=', $todayName)
                       // atau hari == today tapi waktu null / >= now
                       ->orWhere(function($q2) use ($todayName, $nowTime) {
                           $q2->where('hari', $todayName)
                              ->where(function($q3) use ($nowTime) {
                                  $q3->whereNull('waktu')
                                     ->orWhere('waktu', '>=', $nowTime);
                              });
                       });
                 })
                 ->whereIn('hari', $orderDays) // filter hanya hari yang valid
                 ->orderByRaw("$fieldOrder ASC, COALESCE(waktu, '00:00:00') ASC");
}

}
