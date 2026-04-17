<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LKH extends Model
{
    use HasFactory;

    protected $fillable = [
        'uraian_kegiatan',
        'tanggal',
        'waktu',
        'user_id'
    ];
}
