<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pengumuman;
use Illuminate\Support\Facades\Validator;
use DB;

class PengumumanController extends Controller
{
    public function data(Request $request)
    {
        $query = DB::table('pengumumen')->orderBy('created_at', 'DESC');

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
            'respon' => 'Pengumuman berhasil disimpan'
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
        $pengumuman->update([
            'file' => $fileName ?? $pengumuman->file,
            'pengumuman' => $request->pengumuman
        ]);

        return response()->json([
            'responCode' => 1,
            'respon' => 'Pengumuman berhasil diperbarui'
        ]);
    }

    public function delete(Request $request)
    {
        $data = Pengumuman::find($request->id);

        if (!$data) {
            return response()->json([
                'responCode' => 1,
                'respon' => 'Data pengumuman tidak ditemukan',
            ]);
        }

        // Hapus data
        $data->delete();

        return response()->json([
            'responCode' => 1,
            'respon' => 'Pengumuman berhasil dihapus'
        ]);
    }
}
