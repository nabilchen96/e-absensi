<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Schedule;
use App\Models\User;
use App\Models\Shift;
use Illuminate\Support\Facades\Validator;
use DB;
use Auth;

class ScheduleController extends Controller
{
    public function index(){

        return view('backend.schedule.index');
    }

    public function data(Request $request)
    {
        $keyword        = $request->keyword;
        $idUser         = $request->id_user;
        $tanggalDari    = $request->tanggal_dari;
        $tanggalSampai  = $request->tanggal_sampai;

        $query = DB::table('schedules')
            ->join('users', 'schedules.id_user', '=', 'users.id')
            ->join('shifts', 'schedules.id_shift', '=', 'shifts.id')
            ->select(
                'schedules.id',
                'schedules.id_user',
                'schedules.id_shift',
                'users.name as user_name',
                'shifts.nama_shift as shift_name',
                'shifts.jam_masuk',
                'shifts.jam_pulang',
                'schedules.status',
                'tanggal'
            );

        // Filter Nama User
        if (!empty($idUser)) {
            $query->where('schedules.id_user', $idUser);
        }

        // Filter Keyword
        if (!empty($keyword)) {
            $query->where(function ($q) use ($keyword) {
                $q->where('users.name', 'like', "%$keyword%")
                ->orWhere('shifts.nama_shift', 'like', "%$keyword%");
            });
        }

        // Filter tanggal dari
        if (!empty($tanggalDari)) {
            $query->whereDate('tanggal', '>=', $tanggalDari);
        }

        // Filter tanggal sampai
        if (!empty($tanggalSampai)) {
            $query->whereDate('tanggal', '<=', $tanggalSampai);
        }

        // Default: sembunyikan tanggal yang sudah lewat (jika tidak ada filter)
        $punyaFilter = $keyword || $idUser || $tanggalDari || $tanggalSampai;
        if (!$punyaFilter) {
            $query->whereDate('tanggal', '>=', date('Y-m-d'));
        }

        // 🌟 PENGURUTAN / GROUPING: Nama lalu Tanggal
        $query->orderBy('users.name', 'asc')
            ->orderBy('tanggal', 'asc');

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

        return response()->json([
            'data' => $query
        ]);
    }

    public function store(Request $request)
    {
        $val = Validator::make($request->all(), [
            'id_user' => 'required',
            'id_shift' => 'required',
            'tanggal_dari' => 'required|date',
            'tanggal_ke' => 'required|date',
        ]);

        if ($val->fails()) {
            return response()->json([
                'responCode' => 0,
                'respon' => $val->errors()
            ]);
        }

        // Looping tanggal
        $start = strtotime($request->tanggal_dari);
        $end   = strtotime($request->tanggal_ke);

        while ($start <= $end) {
            Schedule::create([
                'id_user' => $request->id_user,
                'id_shift' => $request->id_shift,
                'tanggal' => date('Y-m-d', $start),
                'status'  => 'Pengajuan'
            ]);

            // Tambah 1 hari
            $start = strtotime("+1 day", $start);
        }

        return response()->json([
            'responCode' => 1,
            'respon' => 'Schedule berhasil ditambahkan'
        ]);
    }

    public function update(Request $request)
    {
        $val = Validator::make($request->all(), [
            'id' => 'required',
            'id_user' => 'required',
            'id_shift' => 'required',
            'tanggal_dari' => 'required|date',
        ]);

        if ($val->fails()) {
            return response()->json([
                'responCode' => 0,
                'respon' => $val->errors()
            ]);
        }

        $data = Schedule::find($request->id);
        $data->update($request->all());

        return response()->json([
            'responCode' => 1,
            'respon' => 'Schedule berhasil diperbarui'
        ]);
    }

    public function delete(Request $request)
    {
        Schedule::find($request->id)->delete();

        return response()->json([
            'responCode' => 1,
            'respon' => 'Schedule berhasil dihapus'
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
        $jadwal = Schedule::findOrFail($request->id);
        $jadwal->update([
            'status' => $request->status
        ]);

        return response()->json([
            'responCode' => 1,
            'respon' => 'Status Jadwak berhasil diperbarui'
        ]);
    }

    // =========================
    // LIST USER UNTUK DROPDOWN
    // =========================
    public function listUser()
    {
        return User::select('id', 'name')->orderBy('name')->get();
    }

    // =========================
    // LIST SHIFT UNTUK DROPDOWN
    // =========================
    public function listShift()
    {
        return Shift::select('id', 'nama_shift')->orderBy('nama_shift')->get();
    }
}
