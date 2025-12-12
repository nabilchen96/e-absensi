<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_shift',
        'jam_masuk',
        'jam_pulang',
        'mulai_scan_masuk',
        'akhir_scan_masuk',
        'mulai_scan_pulang',
        'akhir_scan_pulang',
    ];
}
