<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    use HasFactory;

    protected $fillable = [
        'foto',
        'latitude',
        'longitude',
        'user_id',
        'datetime',
        'jarak',
        'jenis_absensi',
        'status_shift',
        'alasan',
        'bukti',
        'status_absensi'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }
}
