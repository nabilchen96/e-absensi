<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LKH;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use DB;
use Auth;

class LkhController extends Controller
{
    public function index(){

        return view('backend.lkh.index');

    }

    public function data(Request $request)
    {
        $idUser         = $request->id_pandu;
        $tanggalDari    = $request->tanggal_dari;
        $tanggalSampai  = $request->tanggal_sampai;

        $query = DB::table('l_k_h_s')
            ->leftjoin('users', 'users.id', '=', 'l_k_h_s.user_id')
            ->leftjoin('detail_users', 'detail_users.user_id', '=', 'users.id')
            ->select(
                'users.id_pandu',
                'users.name as user_name',
                'detail_users.nip',
                'l_k_h_s.*',
            )
            ->orderBy('users.name', 'ASC')
            ->orderBy('tanggal', 'DESC');

        // Filter Nama User
        if (!empty($idUser)) {
            $query->where('users.id_pandu', $idUser);
        }

        // Filter tanggal dari
        if (!empty($tanggalDari)) {
            $query->whereDate('tanggal', '>=', $tanggalDari);
        }

        // Filter tanggal sampai
        if (!empty($tanggalSampai)) {
            $query->whereDate('tanggal', '<=', $tanggalSampai);
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
        
        else{
            $query = $query->where('users.id', Auth::id())->get();
        }

        return response()->json(['data' => $query]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // 'id_pandu' => 'required',
            'waktu' => 'required',
            'tanggal' => 'required',
            'uraian_kegiatan' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'responCode' => 0,
                'respon' => $validator->errors(),
            ]);
        }
        
        DB::beginTransaction();

        try {

            
            //GET DATA USER
            // $user = DB::table('users')->where('id_pandu', $request->id_pandu)->first();

            LKH::create([
                'user_id' => Auth::id(),
                'tanggal' => $request->tanggal,
                'waktu' => $request->waktu,
                'uraian_kegiatan' => $request->uraian_kegiatan,
            ]);

            DB::commit();

            return response()->json([
                'responCode' => 1,
                'respon' => 'LKH Berhasil Disimpan!'
            ]);
            
        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'responCode' => 0,
                'respon'     => 'Gagal menyimpan Data LKH',
                'error'        => $e->getMessage()

            ], 500);
        }
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'waktu' => 'required',
            'tanggal' => 'required',
            'uraian_kegiatan' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'responCode' => 0,
                'respon' => $validator->errors(),
            ]);
        }

        DB::beginTransaction();

        try {

            // ✅ WAJIB: urutkan berdasarkan tanggal
            $data = LKH::where('id', $request->id)
                ->orderBy('tanggal')
                ->get();

            if ($data->isEmpty()) {
                return response()->json([
                    'responCode' => 0,
                    'respon' => 'Data tidak ditemukan'
                ]);
            }

            $data = LKH::find($request->id);
            $data->update($request->all());

            DB::commit();

            return response()->json([
                'responCode' => 1,
                'respon' => 'Data LKH berhasil diupdate'
            ]);

        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'responCode' => 0,
                'respon' => 'Gagal update cuti',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function delete(Request $request)
    {
        LKH::find($request->id)->delete();

        return response()->json([
            'responCode' => 1,
            'respon' => 'LKH berhasil dihapus'
        ]);
    }
}
