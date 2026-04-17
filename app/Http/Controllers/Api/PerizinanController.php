<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Perizinan;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Auth;
use Illuminate\Support\Str;

class PerizinanController extends Controller
{
    public function index(Request $request)
    {
        $idUser         = $request->id_pandu;
        $tanggalDari    = $request->tanggal_dari;
        $tanggalSampai  = $request->tanggal_sampai;

        $query = DB::table('perizinans')
            ->leftjoin('users', 'users.id', '=', 'perizinans.user_id')
            ->leftjoin('detail_users', 'detail_users.user_id', '=', 'users.id')
            ->select(
                'users.id_pandu',
                'users.name as user_name',
                'detail_users.nip',
                'perizinans.jenis',
                'perizinans.keterangan',
                'perizinans.file',
                'perizinans.status',
                'perizinans.id_pengajuan',
                'perizinans.user_id',
                DB::raw('MIN(perizinans.tanggal) as tanggal_awal'),
                DB::raw('MAX(perizinans.tanggal) as tanggal_akhir'),
            )
            ->whereIn('jenis', [
                'Perjalanan Dinas', 'Pekerjaan Diluar Kantor'
            ])
            ->groupBy(
                'perizinans.id_pengajuan'
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
        
        elseif(Auth::user()->role == 'Pegawai'){
            $query = $query->where('users.id', Auth::id())->get();
        }

        return response()->json($query);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_pandu' => 'required',
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

        $idPengajuan = 'PZN' . now()->format('YmdHis');

        DB::beginTransaction();

        try {

            // Looping tanggal
            $start = strtotime($request->tanggal_awal);
            $end   = strtotime($request->tanggal_akhir);

            //GET DATA USER
            $user = DB::table('users')->where('id_pandu', $request->id_pandu)->first();


            while ($start <= $end) {
                Perizinan::create([
                    'id_pengajuan' => $idPengajuan,
                    'user_id' => $user->id,
                    'tanggal' => date('Y-m-d', $start),
                    'jenis' => $request->jenis,
                    'keterangan' => $request->keterangan,
                    'file' => $fileName,
                ]);

                // Tambah 1 hari
                $start = strtotime("+1 day", $start);
            }

            DB::commit();

            return response()->json([
                'responCode' => 1,
                'respon' => 'Perizinan berhasil disimpan untuk rentang tanggal '.$request->tanggal_awal.' s/d '.$request->tanggal_akhir
            ]);
            
        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'responCode' => 0,
                'respon'     => 'Gagal menyimpan perizinan',
                'error'        => $e->getMessage()

            ], 500);
        }
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tanggal_awal' => 'required|date',
            'tanggal_akhir' => 'required|date|after_or_equal:tanggal_awal',
            'jenis' => 'required',
            'keterangan' => 'nullable',
            'file' => 'nullable|file|mimes:pdf|max:2048',
            'id_pengajuan' => 'required'
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
            $data = Perizinan::where('id_pengajuan', $request->id_pengajuan)
                ->orderBy('tanggal')
                ->get();

            if ($data->isEmpty()) {
                return response()->json([
                    'responCode' => 0,
                    'respon' => 'Data tidak ditemukan'
                ]);
            }

            // File lama
            $fileName = $data->first()->file;

            if ($request->hasFile('file')) {
                $fileName = $request->file('file')->store('perizinan', 'public');
            }

            // Generate tanggal baru
            $start = strtotime($request->tanggal_awal);
            $end   = strtotime($request->tanggal_akhir);

            $tanggalBaru = [];
            while ($start <= $end) {
                $tanggalBaru[] = date('Y-m-d', $start);
                $start = strtotime("+1 day", $start);
            }

            $jumlahLama = $data->count();
            $jumlahBaru = count($tanggalBaru);

            // ✅ 1. Update data yang sudah ada
            foreach ($data as $index => $row) {
                if (isset($tanggalBaru[$index])) {
                    $row->tanggal = $tanggalBaru[$index];
                    $row->jenis = $request->jenis;
                    $row->keterangan = $request->keterangan;
                    $row->file = $fileName;
                    $row->save();
                }
            }

            // ✅ 2. Jika tanggal baru LEBIH BANYAK → tambah
            if ($jumlahBaru > $jumlahLama) {
                for ($i = $jumlahLama; $i < $jumlahBaru; $i++) {
                    Perizinan::create([
                        'id_pengajuan' => $request->id_pengajuan,
                        'user_id' => $data->first()->user_id,
                        'tanggal' => $tanggalBaru[$i],
                        'jenis' => $request->jenis,
                        'keterangan' => $request->keterangan,
                        'file' => $fileName,
                    ]);
                }
            }

            // ✅ 3. Jika tanggal baru LEBIH SEDIKIT → hapus sisanya
            if ($jumlahBaru < $jumlahLama) {
                $idsToDelete = $data->slice($jumlahBaru)->pluck('id');
                Perizinan::whereIn('id', $idsToDelete)->delete();
            }

            DB::commit();

            return response()->json([
                'responCode' => 1,
                'respon' => 'Data perizinan berhasil diupdate'
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

    public function updateStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required',
            'id' => 'nullable|integer',
            'id_pengajuan' => 'nullable',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'responCode' => 0,
                'respon' => $validator->errors(),
            ]);
        }

        // Jika tidak kirim id dan id_pengajuan
        if (!$request->id && !$request->id_pengajuan) {
            return response()->json([
                'responCode' => 0,
                'respon' => 'ID atau ID Pengajuan wajib diisi',
            ]);
        }

        // =========================
        // UPDATE BERDASARKAN ID
        // =========================
        if ($request->id) {
            $izin = Perizinan::find($request->id);

            if (!$izin) {
                return response()->json([
                    'responCode' => 0,
                    'respon' => 'Data perizinan tidak ditemukan',
                ]);
            }

            // jika ubah semua dari id → pakai id_pengajuan
            if ($request->has('ubah_semua') && $request->ubah_semua) {

                Perizinan::where('id_pengajuan', $izin->id_pengajuan)
                    ->update([
                        'status' => $request->status
                    ]);

            } else {
                $izin->update([
                    'status' => $request->status
                ]);
            }
        }

        // =========================
        // UPDATE BERDASARKAN ID_PENGAJUAN
        // =========================
        if ($request->id_pengajuan) {

            $data = Perizinan::where('id_pengajuan', $request->id_pengajuan)->get();

            if ($data->isEmpty()) {
                return response()->json([
                    'responCode' => 0,
                    'respon' => 'Data perizinan (id_pengajuan) tidak ditemukan',
                ]);
            }

            Perizinan::where('id_pengajuan', $request->id_pengajuan)
                ->update([
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
        // Ambil semua data
        $izinList = Perizinan::where('id_pengajuan', $request->id_pengajuan)->get();

        if ($izinList->isEmpty()) {
            return response()->json([
                'responCode' => 1,
                'respon' => 'Data perizinan tidak ditemukan',
            ]);
        }

        // Loop untuk hapus file & data
        foreach ($izinList as $izin) {
            if ($izin->file && Storage::disk('public')->exists($izin->file)) {
                Storage::disk('public')->delete($izin->file);
            }

            $izin->delete();
        }

        return response()->json([
            'responCode' => 1,
            'respon' => 'Perizinan berhasil dihapus'
        ]);
    }
}
