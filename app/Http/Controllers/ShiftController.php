<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shift;
use Illuminate\Support\Facades\Validator;
use DB;

class ShiftController extends Controller
{
    public function index()
    {
        return view('backend.shift.index');
    }

    public function data(Request $request)
    {
        $keyword = $request->keyword;

        $query = DB::table('shifts');

        if ($keyword) {
            $query->where('nama_shift', 'like', "%$keyword%");
        }

        return response()->json(['data' => $query->get()]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_shift' => 'required',
            'jam_masuk' => 'required',
            'jam_pulang' => 'required',
            'mulai_scan_masuk' => 'required',
            'akhir_scan_masuk' => 'required',
            'mulai_scan_pulang' => 'required',
            'akhir_scan_pulang' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'responCode' => 0,
                'respon' => $validator->errors(),
            ]);
        }

        Shift::create($request->all());

        return response()->json([
            'responCode' => 1,
            'respon' => 'Shift berhasil disimpan'
        ]);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'nama_shift' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'responCode' => 0,
                'respon' => $validator->errors(),
            ]);
        }

        $shift = Shift::find($request->id);
        $shift->update($request->all());

        return response()->json([
            'responCode' => 1,
            'respon' => 'Shift berhasil diperbarui'
        ]);
    }

    public function delete(Request $request)
    {
        Shift::find($request->id)->delete();

        return response()->json([
            'responCode' => 1,
            'respon' => 'Shift berhasil dihapus'
        ]);
    }
}
