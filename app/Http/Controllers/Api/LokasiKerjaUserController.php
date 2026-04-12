<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;

class LokasiKerjaUserController extends Controller
{
    public function index(){
        $data = DB::table('lokasi_kerja_users')
                ->leftjoin('users', 'users.id', '=', 'lokasi_kerja_users.id_user')
                ->leftjoin('lokasi_kerjas', 'lokasi_kerjas.id', '=', 'lokasi_kerja_users.id_lokasi_kerja')
                ->select(
                    'lokasi_kerjas.lokasi_kerja',
                    'lokasi_kerjas.latitude',
                    'lokasi_kerjas.longitude', 
                    'lokasi_kerja_users.id',
                    'users.name',
                    'users.id',
                    'users.id_pandu'
                );

        if(Auth::user()->role == 'Admin'){
            $data = $data->get();
        }
        
        // 🏢 ROLE OPD → pegawai dalam unit kerja yang sama
        // elseif(Auth::user()->role == 'OPD'){

        //     $idUnitKerja = Auth::user()->id_unit_kerja_pandu; //107

        //     $data = $data->leftjoin('lokasi_kerja_users', 'lokasi_kerja_users.id_user', '=', 'users.id')
        //             // ->leftjoin('lokasi_kerja_users', 'lokasi_kerja_users.id_lokasi_kerja', '=', 'lokasi_kerjas.id')
        //             ->leftjoin('lokasi_kerjas', 'lokasi_kerjas.id', '=', 'lokasi_kerja_users.id_lokasi_kerja')
        //             ->where('lokasi_kerjas.id_pandu', $idUnitKerja)->get();
        // }
        
        else{
            $data = $data->where('users.id', Auth::id())->get();
        }

        return response()->json($data);
    }
}
