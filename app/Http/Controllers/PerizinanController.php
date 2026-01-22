<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Perizinan;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Auth;
use Illuminate\Support\Str;


class PerizinanController extends Controller
{
    public function index()
    {
        return view('backend.perizinan.index');
    }

    public function data(Request $request)
    {
        $keyword = $request->keyword;

        $query = DB::table('perizinans')
            ->leftjoin('users', 'users.id', '=', 'perizinans.user_id')
            ->select(
                'perizinans.*',
                'users.name as user_name'
            )
            ->whereIn('jenis', [
                'Perjalanan Dinas', 'Pekerjaan Diluar Kantor'
            ])
            ->orderBy('users.name', 'ASC')
            ->orderBy('tanggal', 'DESC');

        if ($keyword) {
            $query->where(function ($q) use ($keyword) {
                $q->where('users.name', 'like', "%$keyword%")
                  ->orWhere('jenis', 'like', "%$keyword%");
            });
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
            'user_id' => 'required',
            'tanggal_awal' => 'required|date',
            'tanggal_akhir' => 'required|date|after_or_equal:tanggal_awal',
            'jenis' => 'required',
            'keterangan' => 'nullable',
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
            $fileName = $request->file('file')->store('perizinan', 'public');
        }

        $idPengajuan = 'PZN-' . now()->format('YmdHis') . '-' . Str::upper(Str::random(5));

        // Looping tanggal
        $start = strtotime($request->tanggal_awal);
        $end   = strtotime($request->tanggal_akhir);

        while ($start <= $end) {
            Perizinan::create([
                'id_pengajuan' => $idPengajuan,
                'user_id' => $request->user_id,
                'tanggal' => date('Y-m-d', $start),
                'jenis' => $request->jenis,
                'keterangan' => $request->keterangan,
                'file' => $fileName,
            ]);

            // Tambah 1 hari
            $start = strtotime("+1 day", $start);
        }

        return response()->json([
            'responCode' => 1,
            'respon' => 'Perizinan berhasil disimpan untuk rentang tanggal'
        ]);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'user_id' => 'required',
            'tanggal' => 'required|date',
            'file' => 'nullable|file|mimes:pdf|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'responCode' => 0,
                'respon' => $validator->errors(),
            ]);
        }

        $izin = Perizinan::find($request->id);

        // Upload file baru jika ada
        $fileName = $izin->file;

        if ($request->hasFile('file')) {
            if ($fileName) {
                Storage::disk('public')->delete($fileName);
            }

            $fileName = $request->file('file')->store('perizinan', 'public');
        }

        $izin->update([
            'user_id' => $request->user_id,
            'tanggal' => $request->tanggal,
            'jenis' => $request->jenis,
            'keterangan' => $request->keterangan,
            'file' => $fileName,
        ]);

        return response()->json([
            'responCode' => 1,
            'respon' => 'Perizinan berhasil diperbarui'
        ]);
    }

    public function updateStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'status' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'responCode' => 0,
                'respon' => $validator->errors(),
            ]);
        }

        // Ambil data perizinan berdasarkan ID
        $izin = Perizinan::findOrFail($request->id);

        // Jika checkbox "ubah semua" dicentang
        if ($request->has('ubah_semua')) {

            Perizinan::where('id_pengajuan', $izin->id_pengajuan)
                ->update([
                    'status' => $request->status
                ]);

        } else {
            // Update satu data saja
            $izin->update([
                'status' => $request->status
            ]);
        }

        return response()->json([
            'responCode' => 1,
            'respon' => 'Status Perizinan berhasil diperbarui'
        ]);
    }

    public function delete(Request $request)
    {
        $izin = Perizinan::find($request->id);

        if ($izin->file) {
            Storage::disk('public')->delete($izin->file);
        }

        $izin->delete();

        return response()->json([
            'responCode' => 1,
            'respon' => 'Perizinan berhasil dihapus'
        ]);
    }
}
