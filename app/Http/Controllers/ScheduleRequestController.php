<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Schedule;
use App\Models\ScheduleRequest;
use App\Models\User;
use App\Models\Shift;
use Illuminate\Support\Facades\Validator;
use DB;
use Auth;

class ScheduleRequestController extends Controller
{
    public function index(){
        return view('backend.schedule_request.index');
    }

    public function data(Request $request)
    {
        $keyword        = $request->keyword;
        $idUser         = $request->id_user;
        $tanggalDari    = $request->tanggal_dari;
        $tanggalSampai  = $request->tanggal_sampai;

        $query = DB::table('schedule_requests')
            ->join('users', 'schedule_requests.id_user', '=', 'users.id')
            ->leftjoin('schedules', 'schedules.id_schedule_request', '=', 'schedule_requests.id')
            ->select(
                'schedule_requests.*',
                'users.name',
                 DB::raw('MIN(schedules.tanggal) as tanggal_awal'),
                 DB::raw('MAX(schedules.tanggal) as tanggal_akhir')
            )->groupBy(
                'schedule_requests.id',
                'schedule_requests.id_user',
                'schedule_requests.file',
                'schedule_requests.status',
                'schedule_requests.catatan',
                'schedule_requests.created_at',
                'schedule_requests.updated_at',
                'users.name'
            );

        // Filter Nama User
        if (!empty($idUser)) {
            $query->where('schedule_requests.id_user', $idUser);
        }

        // Filter Keyword
        if (!empty($keyword)) {
            $query->where(function ($q) use ($keyword) {
                $q->where('users.name', 'like', "%$keyword%");
            });
        }

        // 🌟 PENGURUTAN / GROUPING: Nama lalu Tanggal
        $query->orderBy('users.name', 'asc');

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

        elseif(Auth::user()->role == 'SKPD'){

            $idSkpd = Auth::user()->id_skpd_pandu;

            $query = $query
                ->leftJoin('lokasi_kerja_users', 'lokasi_kerja_users.id_user', '=', 'users.id')
                ->leftJoin('lokasi_kerjas', 'lokasi_kerjas.id', '=', 'lokasi_kerja_users.id_lokasi_kerja')
                ->whereIn('lokasi_kerjas.id_pandu', function ($q) use ($idSkpd) {
                    $q->select('id_unit_kerja_pandu')
                    ->from('users')
                    ->where('id_skpd_pandu', $idSkpd)
                    ->whereNotNull('id_unit_kerja_pandu');
                })
                ->get();
        }

        else{
            $query = $query->where('users.id', Auth::id())->get();
        }

        return response()->json([
            'data' => $query
        ]);
    }

    public function store(Request $request){

        $validator = Validator::make($request->all(), [
            'id_user' => 'required',
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
            $fileName = $request->file('file')->store('file_jadwal', 'public');
        }

        $data = ScheduleRequest::create([
            'id_user' => $request->id_user,
            'status' => 'Belum Diajukan',
            'file' => $fileName
        ]);

        return response()->json([
            'responCode' => 1,
            'respon' => 'Pengajuan jadwal berhasil dibuat',
            'data'  => $data
        ]);

    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_user' => 'required',
            'file' => 'nullable|file|mimes:pdf|max:2048'
        ]);

        // dd($request->all());

        if ($validator->fails()) {
            return response()->json([
                'responCode' => 0,
                'respon' => $validator->errors(),
            ]);
        }

        // Upload file
        $fileName = null;
        if ($request->hasFile('file')) {
            $fileName = $request->file('file')->store('file_jadwal', 'public');
        }

        $data = ScheduleRequest::find($request->id);
        $data->update([
            'id_user' => $request->id_user,
            'status' => 'Pengajuan',
            'file' => $fileName
        ]);

        return response()->json([
            'responCode' => 1,
            'respon' => 'Pengajuan jadwal berhasil diedit',
            'data'  => $data
        ]);

    }

    public function delete(Request $request){

        ScheduleRequest::find($request->id)->delete();

        return response()->json([
            'responCode' => 1,
            'respon' => 'Pengajuan Schedule berhasil dihapus'
        ]);
    }

    public function updateStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'status' => 'required',
        ]);

        // dd($request->all());

        if ($validator->fails()) {
            return response()->json([
                'responCode' => 0,
                'respon' => $validator->errors(),
            ]);
        }

        // Ambil data perizinan berdasarkan ID
        $jadwal = ScheduleRequest::findOrFail($request->id);
        $jadwal->update([
            'status' => $request->status,
            'catatan' => $request->catatan
        ]);

        return response()->json([
            'responCode' => 1,
            'respon' => 'Status Jadwak berhasil diperbarui'
        ]);
    }
}
