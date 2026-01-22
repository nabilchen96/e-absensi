<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LokasiKerja;
use Illuminate\Support\Facades\Validator;
use DB;
use Auth;

class LokasiKerjaController extends Controller
{
    public function index()
    {
        return view('backend.lokasi_kerja.index');
    }

    public function data(Request $request)
    {
        $keyword = $request->keyword;

        $query = DB::table('lokasi_kerjas');

        if ($keyword) {
            $query = $query->where('lokasi_kerja', 'like', "%$keyword%");
        }

        if(Auth::user()->role == 'Admin'){
            $query = $query->get();
        }

        elseif(Auth::user()->role == 'OPD'){
            
            $id_unit_kerja_pandu = Auth::user()->id_unit_kerja_pandu;
            
            $query = $query->where('id_pandu', $id_unit_kerja_pandu)->get();
        }



        return response()->json(['data' => $query]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'lokasi_kerja' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'responCode' => 0,
                'respon' => $validator->errors(),
            ]);
        }

        LokasiKerja::create($request->all());

        return response()->json([
            'responCode' => 1,
            'respon' => 'Lokasi Kerja berhasil disimpan'
        ]);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'lokasi_kerja' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'responCode' => 0,
                'respon' => $validator->errors(),
            ]);
        }

        $lokasi = LokasiKerja::find($request->id);
        $lokasi->update($request->all());

        return response()->json([
            'responCode' => 1,
            'respon' => 'Lokasi Kerja berhasil diperbarui'
        ]);
    }

    public function delete(Request $request)
    {
        LokasiKerja::find($request->id)->delete();

        return response()->json([
            'responCode' => 1,
            'respon' => 'Lokasi Kerja berhasil dihapus'
        ]);
    }
}
