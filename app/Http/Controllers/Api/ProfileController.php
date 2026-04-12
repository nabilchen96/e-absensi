<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DetailUser;
use App\Models\LokasiKerjaUser;
use Illuminate\Support\Facades\Validator;
use DB;
use Auth;

class ProfileController extends Controller
{
    public function index(Request $request)
    {
        $idUser = $request->id_pandu;
        $email = $request->email;

        $query = DB::table('users')
                ->leftjoin('detail_users', 'detail_users.user_id', '=', 'users.id')
                ->leftjoin('lokasi_kerjas', 'lokasi_kerjas.id', '=', 'detail_users.satuan_kerja')
                ->select(
                    'users.id',
                    'users.id_pandu',
                    'users.name',
                    'users.email',
                    'detail_users.jenis_kelamin',
                    'detail_users.tempat_lahir',
                    'detail_users.tanggal_lahir',
                    'detail_users.nip',
                    'detail_users.jenis_asn',
                    'detail_users.jabatan',
                    'detail_users.pangkat',
                    'detail_users.instansi_kerja',
                    'detail_users.satuan_kerja',
                    'detail_users.created_at',
                    'detail_users.updated_at'
                )
                ->where('users.role', 'Pegawai');

        // Filter Nama User
        if (!empty($idUser)) {
            $query->where('users.id_pandu', $idUser);
        }

        // Filter Email user
        if (!empty($email)) {
            $query->where('users.email', $email);
        }

        if(Auth::user()->role == 'Admin'){
            $query = $query->get();
        }
        
        // 🏢 ROLE OPD → pegawai dalam unit kerja yang sama
        elseif(Auth::user()->role == 'OPD'){

            $idUnitKerja = Auth::user()->id_unit_kerja_pandu; //107

            $query = $query->leftjoin('lokasi_kerja_users', 'lokasi_kerja_users.id_user', '=', 'users.id')
                    // ->leftjoin('lokasi_kerja_users', 'lokasi_kerja_users.id_lokasi_kerja', '=', 'lokasi_kerjas.id')
                    ->leftjoin('lokasi_kerjas', 'lokasi_kerjas.id', '=', 'lokasi_kerja_users.id_lokasi_kerja')
                    ->where('lokasi_kerjas.id_pandu', $idUnitKerja)->get();
        }
        
        elseif(Auth::user()->role == 'Pegawai'){
            $query = $query->where('users.id', Auth::id())->get();
        }


        return response()->json($query);
    }

    public function myProfile(Request $request)
    {
        $query = DB::table('users')
                ->leftjoin('detail_users', 'detail_users.user_id', '=', 'users.id')
                ->leftjoin('lokasi_kerjas', 'lokasi_kerjas.id', '=', 'detail_users.satuan_kerja')
                ->select(
                    'users.id_pandu',
                    'users.name',
                    'users.email',
                    'users.role',
                    'detail_users.jenis_kelamin',
                    'detail_users.tempat_lahir',
                    'detail_users.tanggal_lahir',
                    'detail_users.nip',
                    'detail_users.jenis_asn',
                    'detail_users.jabatan',
                    'detail_users.pangkat',
                    'detail_users.instansi_kerja',
                    'detail_users.satuan_kerja',
                    'detail_users.created_at',
                    'detail_users.updated_at'
                )
                ->where('users.id_pandu', Auth()->user()->id_pandu);

        $query->orderBy('users.name', 'asc');

        return response()->json($query->get());
    }
}
