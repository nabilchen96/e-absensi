<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RekapAbsensiBatch;
use DB;
use Auth;
use App\Jobs\GenerateRekapAbsensiJob;

class LaporanRekapController extends Controller
{

    public function index(){
        return view('backend.laporan.rekap.index');
    }

    public function data(Request $request){

        $keyword = $request->keyword;

        $query = DB::table('rekap_absensi_batches')
                    ->join('lokasi_kerjas', 'lokasi_kerjas.id_pandu', '=', 'rekap_absensi_batches.id_unit_kerja_pandu')
                    ->select(
                        'rekap_absensi_batches.*',
                        'lokasi_kerjas.lokasi_kerja'
                    )->where('lokasi_kerjas.id_pandu', $keyword);

        if(Auth::user()->role == 'Admin'){


            $query = $query->get();
        

        // 🏢 ROLE OPD → pegawai dalam unit kerja yang sama
        }elseif (Auth::user()->role == 'OPD') {

            $idUnitKerja = Auth::user()->id_unit_kerja_pandu;

            $query = $query->where('id_unit_kerja_pandu', $idUnitKerja)->get();
        }

        

        return response()->json(['data' => $query]);
    }

    public function detail(){

        $rekap = DB::table('rekap_absensi_batches')
                 ->join('lokasi_kerjas', 'lokasi_kerjas.id_pandu', '=', 'rekap_absensi_batches.id_unit_kerja_pandu')
                 ->where('rekap_absensi_batches.id', Request('batch_id'))
                 ->first();

        $data = DB::table('rekap_absensi_details')
                ->join('users', 'users.id', '=', 'rekap_absensi_details.user_id')
                ->join('detail_users', 'detail_users.user_id', '=', 'users.id')
                ->select(
                    'rekap_absensi_details.*',
                    'users.name',
                    'detail_users.nip'
                )
                ->where('batch_id', Request('batch_id'))
                ->get();

        return view('backend.laporan.rekap.detail', [
            'data' => $data,
            'rekap' => $rekap
        ]);
    }

    public function generate(Request $request){

        $batchId = DB::table('rekap_absensi_batches')->insertGetId([
            'judul_laporan'         => $request->judul_laporan,
            'tanggal_awal'          => $request->tanggal_awal,
            'tanggal_akhir'         => $request->tanggal_akhir,
            'id_unit_kerja_pandu'   => $request->id_unit_kerja_pandu,
            'status'                => 'pending',
            'created_at'            => now(),

        ]);

        dispatch(new GenerateRekapAbsensiJob($batchId));

        return response()->json([
            'message' => 'Rekap sedang diproses',
            'batch_id' => $batchId
        ]);
    }
}
