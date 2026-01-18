<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LokasiKerjaUser extends Model
{
    use HasFactory;

        protected $fillable = [
            'id_lokasi_kerja',
            'id_user'
        ];
}
