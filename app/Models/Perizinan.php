<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Perizinan extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'tanggal',
        'jenis',
        'keterangan',
        'file',
        'status',
        'id_pengajuan'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
