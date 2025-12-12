<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;

class DashboardController extends Controller
{
    public function index(){

        $izin = DB::table('perizinans')
                ->leftjoin('users', 'users.id', '=', 'perizinans.user_id')
                ->where('jenis', [
                    'Perjalanan Dinas',
                    'Pekerjaan Diluar Kantor'
                ])
                ->get();

        $cuti = DB::table('perizinans')
                ->leftjoin('users', 'users.id', '=', 'perizinans.user_id')
                ->where('jenis', [
                    'Cuti Tahunan',
                    'Cuti Bersalin',
                    'Cuti Alasan Penting',
                    'Cuti Sakit',
                    'Tugas Belajar',
                    'Cuti Diluar Tanggungan Negara',
                    'Cuti Besar'
                ])
                ->get();

        return view('backend.dashboard.index', [
            'cuti' => $cuti, 
            'izin' => $izin
        ]);
    }
}
