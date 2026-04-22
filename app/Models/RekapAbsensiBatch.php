<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RekapAbsensiBatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'judul_laporan',
        'tanggal_awal',
        'tanggal_akhir',
        'status',
        'id_unit_kerja_pandu'
    ];
}
