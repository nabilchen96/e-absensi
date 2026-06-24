<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use DatePeriod;
use DateTime;
use DateInterval;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class LaporanShiftController extends Controller
{

    public function index(){

        return view('backend.laporan.shift.index');

    }

    public function data(Request $request){

        $start   = $request->start_date; // 2026-01-10
        $end     = $request->end_date;   // 2026-01-15
        $id_user = $request->id_user;

        $data = DB::table('absensis as a')
            ->join('users as u', 'u.id', '=', 'a.user_id')
            ->select(
                DB::raw('DATE(a.datetime) as tanggal'),
                'u.name as nama_pegawai',
                'a.status_shift as shift',

                DB::raw("
                    MIN(
                        CASE
                            WHEN a.jenis_absensi='Masuk'
                            THEN a.datetime
                        END
                    ) as jam_scan_masuk
                "),

                DB::raw("
                    MAX(
                        CASE
                            WHEN a.jenis_absensi='Pulang'
                            THEN a.datetime
                        END
                    ) as jam_scan_pulang
                ")
            )
            ->where('a.status_absensi', 'Diterima')

            // filter user
            ->when($id_user, function ($q) use ($id_user) {
                $q->where('a.user_id', $id_user);
            })

            // filter tanggal
            ->when($start && $end, function ($q) use ($start, $end) {
                $q->whereBetween(
                    DB::raw('DATE(a.datetime)'),
                    [$start, $end]
                );
            })

            ->groupBy(
                DB::raw('DATE(a.datetime)'),
                'a.user_id',
                'u.name',
                'a.status_shift'
            )
            ->orderByDesc('tanggal')
            ->get();

        $totalJamKerja = 0;
        $totalTerlambat = 0;

        $data = $data->map(function ($row) use (&$totalJamKerja, &$totalTerlambat) {

            $jadwalMasuk = [
                'Reguler' => '07:30:00',
                'Sore'    => '16:00:00',
                'Malam'   => '23:00:00'
            ];

            // hitung keterlambatan
            $terlambat = 0;

            if ($row->jam_scan_masuk) {

                $jamMasuk = Carbon::parse($row->jam_scan_masuk);

                $jadwal = Carbon::parse(
                    $row->tanggal . ' ' . $jadwalMasuk[$row->shift]
                );

                if ($jamMasuk->gt($jadwal)) {
                    $terlambat = $jamMasuk->diffInMinutes($jadwal);
                }
            }

            // hitung jam kerja
            $jamKerja = 0;

            if ($row->jam_scan_masuk && $row->jam_scan_pulang) {

                $masuk = Carbon::parse($row->jam_scan_masuk);
                $pulang = Carbon::parse($row->jam_scan_pulang);

                // shift malam
                if ($pulang->lt($masuk)) {
                    $pulang->addDay();
                }

                $jamKerja = $masuk->diffInMinutes($pulang);
            }

            $totalJamKerja += $jamKerja;
            $totalTerlambat += $terlambat;

            return [
                'tanggal' => $row->tanggal,
                'nama_pegawai' => $row->nama_pegawai,
                'shift' => $row->shift,
                'jam_scan_masuk' => $row->jam_scan_masuk,
                'jam_scan_pulang' => $row->jam_scan_pulang,
                'total_terlambat' => gmdate('H:i', $terlambat * 60),
                'total_jam_kerja' => gmdate('H:i', $jamKerja * 60)
            ];
        });

        $absensi = DB::table('absensis')
            ->where('status_absensi', 'Diterima')

            ->when($id_user, function ($q) use ($id_user) {
                $q->where('user_id', $id_user);
            })

            ->when($start && $end, function ($q) use ($start, $end) {
                $q->whereBetween(
                    DB::raw('DATE(datetime)'),
                    [$start, $end]
                );
            })

            ->selectRaw("
                SUM(CASE WHEN jenis_absensi = 'Masuk' THEN 1 ELSE 0 END) as total_absensi_masuk,
                SUM(CASE WHEN jenis_absensi = 'Pulang' THEN 1 ELSE 0 END) as total_absensi_pulang
            ")
            ->first();

        return response()->json([
            'widget' => [
                'total_durasi_jam_kerja' => gmdate('H:i', $totalJamKerja * 60),
                'total_durasi_terlambat' => gmdate('H:i', $totalTerlambat * 60),
                'total_absensi_masuk' => $absensi->total_absensi_masuk ?? 0,
                'total_absensi_pulang' => $absensi->total_absensi_pulang ?? 0
            ],

            'table' => $data
        ]);
    }

    public function opd(){

        return view('backend.laporan.shift_opd.index');

    }

    public function dataOpd(Request $request)
    {
        $id_lokasi_kerja = $request->id_lokasi_kerja;
        $start = $request->start;
        $end = $request->end;

        $data = DB::table('absensis as a')
            ->join('users as u', 'u.id', '=', 'a.user_id')
            ->join('detail_users as du', 'du.user_id', '=', 'u.id')
            ->join('lokasi_kerja_users as lku', 'lku.id_user', '=', 'u.id')
            ->select(
                'u.id',
                'u.name',
                'du.nip',
                DB::raw('DATE(a.datetime) as tanggal'),
                'a.status_shift'
            )
            ->where('a.status_absensi', 'Diterima')
            ->where('lku.id_lokasi_kerja', $id_lokasi_kerja)
            ->whereBetween(
                DB::raw('DATE(a.datetime)'),
                [$start, $end]
            )
            ->groupBy(
                'u.id',
                'u.name',
                DB::raw('DATE(a.datetime)'),
                'a.status_shift'
            )
            ->get();

        $mapShift = [
            'Reguler' => 'R',
            'Sore'    => 'S',
            'Malam'   => 'M'
        ];

        // daftar tanggal
        $tanggalList = [];

        foreach (CarbonPeriod::create($start, $end) as $tanggal) {
            $tanggalList[] = $tanggal->format('Y-m-d');
        }

        $result = [];

        foreach ($data as $row) {

            $tgl = $row->tanggal;

            if (!isset($result[$row->id])) {

                $result[$row->id] = [
                    'nama' => $row->name,
                    'nip' => $row->nip
                ];

                foreach ($tanggalList as $tanggal) {
                    $result[$row->id][$tanggal] = '';
                }
            }

            $singkatan = $mapShift[$row->status_shift];

            if ($result[$row->id][$tgl] == '') {
                $result[$row->id][$tgl] = $singkatan;
            } else {
                $result[$row->id][$tgl] .= ',' . $singkatan;
            }
        }

        return response()->json(array_values($result));
    }
}
