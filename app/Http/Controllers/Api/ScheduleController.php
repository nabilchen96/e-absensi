<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Validator;
use App\Models\Schedule;

class ScheduleController extends Controller
{
    public function index(Request $request)
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
                'tanggal',
                'users.id_pandu'
            );

        // Filter Nama User
        if (!empty($idUser)) {
            $query->where('users.id_pandu', $idUser);
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

        return response()->json($query->get());
    }

    public function store(Request $request)
    {
        $val = Validator::make($request->all(), [
            'id_pandu' => 'required',
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

        DB::beginTransaction();

        try {

            // Looping tanggal
            $start = strtotime($request->tanggal_dari);
            $end   = strtotime($request->tanggal_ke);

            //GET DATA USER
            $user = DB::table('users')->where('id_pandu', $request->id_pandu)->first();

            while ($start <= $end) {
                Schedule::updateOrCreate(
                    [
                        'id_user' => $user->id,
                        'id_shift' => $request->id_shift,
                        'tanggal' => date('Y-m-d', $start),
                    ],
                    [
                        'id_user' => $user->id,
                        'id_shift' => $request->id_shift,
                        'tanggal' => date('Y-m-d', $start),
                    ]
                );

                // Tambah 1 hari
                $start = strtotime("+1 day", $start);
            }

            DB::commit();

            return response()->json([
                'responCode' => 1,
                'respon' => 'Schedule berhasil ditambahkan'
            ]);

        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'responCode' => 0,
                'respon'     => 'Gagal menyimpan schedule',
                'error'        => $e->getMessage()

            ], 500);
        }
    }

    public function update(Request $request)
    {
        $val = Validator::make($request->all(), [
            'id' => 'required',
            'id_pandu' => 'required',
            'id_shift' => 'required',
            'tanggal_dari' => 'required|date',
        ]);

        if ($val->fails()) {
            return response()->json([
                'responCode' => 0,
                'respon' => $val->errors()
            ]);
        }

        //GET DATA USER
        $user = DB::table('users')->where('id_pandu', $request->id_pandu)->first();


        $data = Schedule::find($request->id);
        $data->update([
            'id_user' => $user->id,
            'id_shift' => $request->id_shift,
            'tanggal' => $request->tanggal_dari,
        ]);

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
}
