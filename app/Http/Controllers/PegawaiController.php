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
                'users.id',
                'users.name',
                'users.role',
                'users.email',
                'users.created_at',
                DB::raw('GROUP_CONCAT(lokasi_kerjas.lokasi_kerja SEPARATOR ",<br> ") as lokasi_kerja')
            )
            ->leftJoin('lokasi_kerja_users', 'lokasi_kerja_users.id_user', '=', 'users.id')
            ->leftJoin('lokasi_kerjas', 'lokasi_kerjas.id', '=', 'lokasi_kerja_users.id_lokasi_kerja')
            ->where('users.role', 'Pegawai');

        // 🔍 Filter keyword
        if ($keyword) {
            $query->where(function ($q) use ($keyword) {
                $q->where('users.name', 'like', "%$keyword%")
                ->orWhere('users.email', 'like', "%$keyword%")
                ->orWhere('users.role', 'like', "%$keyword%");
            });
        }

        // 🔐 ROLE PEGAWAI
        if (Auth::user()->role == 'Pegawai') {
            $query->where('users.id', Auth::id());
        }

        // 🏢 ROLE OPD
        elseif (Auth::user()->role == 'OPD') {
            $idUnitKerja = Auth::user()->id_unit_kerja_pandu;

            $query->where('lokasi_kerjas.id_pandu', $idUnitKerja);
        }

        // 🏢 ROLE SKPD
        elseif (Auth::user()->role == 'SKPD') {
            $idSkpd = Auth::user()->id_skpd_pandu;

            $query->whereIn('lokasi_kerjas.id_pandu', function ($sub) use ($idSkpd) {
                $sub->select('id_unit_kerja_pandu')
                    ->from('users')
                    ->where('id_skpd_pandu', $idSkpd)
                    ->whereNotNull('id_unit_kerja_pandu');
            });
        }

        // ✅ PENTING: groupBy supaya tidak duplikat
        $user = $query->groupBy(
            'users.id',
            'users.name',
            'users.role',
            'users.email',
            'users.created_at'
        )->get();

        return response()->json(['data' => $user]);
    }
}
