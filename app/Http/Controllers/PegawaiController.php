<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Auth;


class PegawaiController extends Controller
{
    public function index(){

        return view('backend.pegawai.index');
    }

    public function data(Request $request)
    {
        $keyword = $request->keyword;

        $query = DB::table('users')
                ->select(
                    'users.name',
                    'users.role',
                    'users.email',
                    'users.created_at', 
                    'users.id'
                )
                ->where('role', 'Pegawai');

        // Jika keyword ada
        if ($keyword) {
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%$keyword%")
                ->orWhere('email', 'like', "%$keyword%")
                ->orWhere('role', 'like', "%$keyword%");
            });
        }

        // 🔐 ROLE PEGAWAI → hanya dirinya sendiri
        if(Auth::user()->role == 'Pegawai'){

            $user = $query->where('id', Auth::id())->get();

        }

        
        // 🏢 ROLE OPD → pegawai dalam unit kerja yang sama
        elseif (Auth::user()->role == 'OPD') {

            $idUnitKerja = Auth::user()->id_unit_kerja_pandu; //107

            $user = $query->leftjoin('lokasi_kerja_users', 'lokasi_kerja_users.id_user', '=', 'users.id')
                    // ->leftjoin('lokasi_kerja_users', 'lokasi_kerja_users.id_lokasi_kerja', '=', 'lokasi_kerjas.id')
                    ->leftjoin('lokasi_kerjas', 'lokasi_kerjas.id', '=', 'lokasi_kerja_users.id_lokasi_kerja')
                    ->where('lokasi_kerjas.id_pandu', $idUnitKerja)->get();
        }

        
        // ROLE UNTUK ADMIN
        else{

            $user = $query->get();
        }


        return response()->json(['data' => $user]);
    }
}
