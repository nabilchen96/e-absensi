<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pengumuman;
use Illuminate\Support\Facades\Validator;
use DB;

class PengumumanController extends Controller
{
    public function index()
    {
        return view('backend.pengumuman.index');
    }

    public function data(Request $request)
    {
        $keyword = $request->keyword;

        $query = DB::table('pengumumen');

        if ($keyword) {
            $query->where('pengumuman', 'like', "%$keyword%");
        }

        return response()->json(['data' => $query->get()]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pengumuman' => 'required',
            'file' => 'nullable|file|mimes:pdf|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'responCode' => 0,
                'respon' => $validator->errors(),
            ]);
        }

        // Upload file
        $fileName = null;
        if ($request->hasFile('file')) {
            $fileName = $request->file('file')->store('pengumuman', 'public');
        }

        Pengumuman::create([
            'file' => $fileName,
            'pengumuman' => $request->pengumuman
        ]);

        return response()->json([
            'responCode' => 1,
            'respon' => 'Shift berhasil disimpan'
        ]);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'pengumuman' => 'required',
            'file' => 'nullable|file|mimes:pdf|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'responCode' => 0,
                'respon' => $validator->errors(),
            ]);
        }

        // Upload file
        $fileName = null;
        if ($request->hasFile('file')) {
            $fileName = $request->file('file')->store('pengumuman', 'public');
        }

        $pengumuman = Pengumuman::find($request->id);
        Pengumuman::update([
            'file' => $fileName,
            'pengumuman' => $request->pengumuman
        ]);

        return response()->json([
            'responCode' => 1,
            'respon' => 'Shift berhasil diperbarui'
        ]);
    }

    public function delete(Request $request)
    {
        Pengumuman::find($request->id)->delete();

        return response()->json([
            'responCode' => 1,
            'respon' => 'Shift berhasil dihapus'
        ]);
    }
}
