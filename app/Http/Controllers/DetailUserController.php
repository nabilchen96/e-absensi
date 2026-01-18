<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DetailUser;
use App\Models\LokasiKerjaUser;
use Illuminate\Support\Facades\Validator;
use DB;

class DetailUserController extends Controller
{
    public function index(){

        $data = DB::table('users')
                ->leftjoin('detail_users', 'detail_users.user_id', '=', 'users.id')
                ->leftjoin('lokasi_kerjas', 'lokasi_kerjas.id', '=', 'detail_users.satuan_kerja')
                ->where('users.id', Request('id'))
                ->select(
                    'users.id as id_user',
                    'users.name',
                    'users.email',
                    'detail_users.*',
                    'lokasi_kerjas.lokasi_kerja'
                )
                ->first();

        $lokasiKerjaUser = DB::table('lokasi_kerja_users')
                            ->leftjoin('users', 'users.id', '=', 'lokasi_kerja_users.id_user')
                            ->leftjoin('lokasi_kerjas', 'lokasi_kerjas.id', '=', 'lokasi_kerja_users.id_lokasi_kerja')
                            ->select(
                                'lokasi_kerjas.lokasi_kerja',
                                'lokasi_kerjas.latitude',
                                'lokasi_kerjas.longitude', 
                                'lokasi_kerja_users.id'
                            )
                            ->get();

        return view('backend.detail_user.index', [
            'data' => $data, 
            'lokasi' => $lokasiKerjaUser
        ]);
    }

    public function store(Request $request)
    {

        try {

            // 1. Validasi basic
            $request->validate([
                'email' => 'required|email',
                'name' => 'required|string',
            ]);

            // 5. Simpan ke detail_users
            DetailUser::updateOrCreate(
                ['user_id' => $request->id],  // where
                [
                    'user_id'         => $request->id,
                    'jenis_kelamin'   => $request->jenis_kelamin,
                    'tempat_lahir'    => $request->tempat_lahir,
                    'tanggal_lahir'   => $request->tanggal_lahir,
                    'nip'             => $request->nip,
                    'jenis_asn'       => $request->jenis_asn,
                    'jabatan'         => $request->jabatan,
                    'instansi_kerja'  => $request->instansi_kerja,
                    'satuan_kerja'    => $request->satuan_kerja,
                ]
            );

            return redirect()->back()->with('success', 'Data berhasil disimpan');

        } catch (\Exception $e) {

            return back()->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }

    public function storeLokasiKerjaUser(Request $request)
    {

        try {

            // 5. Simpan ke detail_users
            LokasiKerjaUser::create(
                [
                    'id_user' => $request->id_user,
                    'id_lokasi_kerja' => $request->id_lokasi_kerja
                ]
            );

            return redirect()->back()->with('success', 'Data berhasil disimpan');

        } catch (\Exception $e) {

            return back()->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }

    public function deleteLokasiKerjaUser(Request $request)
    {
        // dd($request->all());

        LokasiKerjaUser::find($request->id)->delete();

        return response()->json([
            'responCode' => 1,
            'respon' => 'Lokasi Kerja User berhasil dihapus'
        ]);
    }
}
