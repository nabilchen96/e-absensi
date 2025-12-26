<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;


class DashboardController extends Controller
{
    public function index(){

        $izin = DB::table('perizinans')
                ->leftjoin('users', 'users.id', '=', 'perizinans.user_id')
                ->leftjoin('detail_users', 'detail_users.user_id', '=', 'users.id')
                ->whereIn('jenis', [
                    'Perjalanan Dinas',
                    'Pekerjaan Diluar Kantor'
                ])
                ->whereDate('perizinans.created_at', Carbon::today())
                ->get();

        $cuti = DB::table('perizinans')
                ->leftjoin('users', 'users.id', '=', 'perizinans.user_id')
                ->leftjoin('detail_users', 'detail_users.user_id', '=', 'users.id')
                ->whereIn('jenis', [
                    'Cuti Tahunan',
                    'Cuti Bersalin',
                    'Cuti Alasan Penting',
                    'Cuti Sakit',
                    'Tugas Belajar',
                    'Cuti Diluar Tanggungan Negara',
                    'Cuti Besar'
                ])
                ->whereDate('perizinans.created_at', Carbon::today())
                ->get();

        return view('backend.dashboard.index', [
            'cuti' => $cuti, 
            'izin' => $izin,
        ]);
    }

    public function grafikAbsensi()
    {
        $today = date('Y-m-d');
        $tomorrow = date('Y-m-d', strtotime('+1 day'));

        // Group absensi per jam
        $data = DB::table('absensis')
            ->selectRaw("HOUR(datetime) as jam, COUNT(*) as jumlah")
            ->whereBetween(DB::raw('DATE(datetime)'), [$today, $tomorrow])
            ->groupBy(DB::raw('HOUR(datetime)'))
            ->orderBy(DB::raw('HOUR(datetime)'))
            ->get();

        $categories = [];
        $values = [];

        foreach ($data as $row) {
            // Tampilan X-Axis (jam)
            $categories[] = str_pad($row->jam, 2, '0', STR_PAD_LEFT) . ':00';

            // Y-axis = jumlah scan
            $values[] = $row->jumlah;
        }

        return response()->json([
            'data' => [
                'categories' => $categories,
                'values' => $values
            ]
        ]);
    }

    public function grafikIzinCuti()
    {
        $today = date('Y-m-d');

        // List lengkap jenis izin & cuti
        $jenisList = [
            'Perjalanan Dinas', 
            'Pekerjaan Diluar Kantor', 
            'Cuti Tahunan', 
            'Cuti Bersalin',
            'Cuti Alasan Penting', 
            'Cuti Sakit', 
            'Tugas Belajar', 
            'Cuti Diluar Tanggungan Negara', 
            'Cuti Besar'
        ];

        // Ambil jumlah per jenis
        $data = DB::table('perizinans')
            ->select('jenis', DB::raw('COUNT(*) as total'))
            ->where('tanggal', $today)
            ->groupBy('jenis')
            ->get()
            ->keyBy('jenis'); // supaya mudah diakses

        // Siapkan output lengkap 9 jenis (meskipun 0)
        $categories = [];
        $values = [];

        foreach ($jenisList as $jenis) {
            $categories[] = $jenis;
            $values[] = isset($data[$jenis]) ? $data[$jenis]->total : 0;
        }

        return response()->json([
            'data' => [
                'categories' => $categories,
                'values'     => $values
            ]
        ]);
    }

}
