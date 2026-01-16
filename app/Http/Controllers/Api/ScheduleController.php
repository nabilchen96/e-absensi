<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;

class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        $keyword        = $request->keyword;
        $idUser         = $request->id_user;
        $tanggalDari    = $request->tanggal_dari;
        $tanggalSampai  = $request->tanggal_sampai;

        $query = DB::table('schedules')
            ->join('users', 'schedules.id_user', '=', 'users.id')
            ->join('shifts', 'schedules.id_shift', '=', 'shifts.id')
            ->select(
                'schedules.id',
                'schedules.id_user',
                'schedules.id_shift',
                'users.name as user_name',
                'shifts.nama_shift as shift_name',
                'shifts.jam_masuk',
                'shifts.jam_pulang',
                'tanggal',
                'users.id_pandu'
            );

        // Filter Nama User
        if (!empty($idUser)) {
            $query->where('users.id_pandu', $idUser);
        }

        // Filter Keyword
        if (!empty($keyword)) {
            $query->where(function ($q) use ($keyword) {
                $q->where('users.name', 'like', "%$keyword%")
                ->orWhere('shifts.nama_shift', 'like', "%$keyword%");
            });
        }

        // Filter tanggal dari
        if (!empty($tanggalDari)) {
            $query->whereDate('tanggal', '>=', $tanggalDari);
        }

        // Filter tanggal sampai
        if (!empty($tanggalSampai)) {
            $query->whereDate('tanggal', '<=', $tanggalSampai);
        }

        // Default: sembunyikan tanggal yang sudah lewat (jika tidak ada filter)
        $punyaFilter = $keyword || $idUser || $tanggalDari || $tanggalSampai;
        if (!$punyaFilter) {
            $query->whereDate('tanggal', '>=', date('Y-m-d'));
        }

        // 🌟 PENGURUTAN / GROUPING: Nama lalu Tanggal
        $query->orderBy('users.name', 'asc')
            ->orderBy('tanggal', 'asc');

        return response()->json($query->get());
    }
}
