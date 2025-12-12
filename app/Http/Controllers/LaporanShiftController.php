<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;

class LaporanShiftController extends Controller
{
    // public function data(Request $request)
    // {
    //     $start = $request->start_date;
    //     $end   = $request->end_date;

    //     $rekap = DB::table('schedules as s')
    //         ->join('users as u', 'u.id', '=', 's.id_user')
    //         ->join('shifts as sh', 'sh.id', '=', 's.id_shift')

    //         ->leftJoin('absensis as a', function ($join) use ($start, $end) {
    //             $join->on('a.user_id', '=', 's.id_user')
    //                 ->whereBetween(DB::raw('DATE(a.datetime)'), [$start, $end]);
    //         })

    //         ->leftJoin('perizinans as p', function ($join) use ($start, $end) {
    //             $join->on('p.user_id', '=', 's.id_user')
    //                 ->whereBetween('p.tanggal', [$start, $end]);
    //         })

    //         ->select(
    //             'u.name as user',
    //             's.tanggal',
    //             'sh.nama_shift as shift',

    //             // Hitung jumlah scan di hari itu & waktu tunggal akan dibandingkan dgn midpoint
    //             DB::raw('COUNT(a.id) as cnt_absen'),

    //             // Scan Masuk: jika hanya 1 scan dan waktunya setelah midpoint -> anggap null (karena itu pulang)
    //             DB::raw("
    //                 CASE
    //                     WHEN COUNT(a.id) = 0 THEN NULL
    //                     WHEN COUNT(a.id) = 1
    //                         AND TIME_TO_SEC(TIME(MIN(a.datetime))) > (TIME_TO_SEC(sh.jam_masuk) + TIME_TO_SEC(sh.jam_pulang)) / 2
    //                         THEN NULL
    //                     ELSE MIN(a.datetime)
    //                 END as scan_masuk
    //             "),

    //             // Scan Pulang: jika hanya 1 scan dan waktunya sebelum atau sama midpoint -> anggap null (karena itu masuk)
    //             DB::raw("
    //                 CASE
    //                     WHEN COUNT(a.id) = 0 THEN NULL
    //                     WHEN COUNT(a.id) = 1
    //                         AND TIME_TO_SEC(TIME(MIN(a.datetime))) <= (TIME_TO_SEC(sh.jam_masuk) + TIME_TO_SEC(sh.jam_pulang)) / 2
    //                         THEN NULL
    //                     ELSE MAX(a.datetime)
    //                 END as scan_pulang
    //             "),

    //             // Terlambat:
    //             // - jika tidak absen => NULL
    //             // - jika hanya 1 scan dan itu dianggap sebagai pulang => terlambat = 01:00:00 (lupa masuk)
    //             // - jika ada masuk time > jam_masuk => selisihnya
    //             DB::raw("
    //                 CASE
    //                     WHEN COUNT(a.id) = 0 THEN NULL

    //                     WHEN COUNT(a.id) = 1
    //                         AND TIME_TO_SEC(TIME(MIN(a.datetime))) > (TIME_TO_SEC(sh.jam_masuk) + TIME_TO_SEC(sh.jam_pulang)) / 2
    //                         THEN '01:00:00'

    //                     WHEN MIN(a.datetime) IS NULL THEN NULL
    //                     WHEN TIME(MIN(a.datetime)) > sh.jam_masuk
    //                         THEN TIMEDIFF(TIME(MIN(a.datetime)), sh.jam_masuk)
    //                     ELSE '00:00:00'
    //                 END as terlambat
    //             "),

    //             // Pulang Cepat:
    //             // - jika tidak absen => NULL
    //             // - jika hanya 1 scan dan itu dianggap sebagai masuk => pulang_cepat = 01:00:00 (lupa pulang)
    //             // - jika ada pulang time < jam_pulang => selisihnya
    //             DB::raw("
    //                 CASE
    //                     WHEN COUNT(a.id) = 0 THEN NULL

    //                     WHEN COUNT(a.id) = 1
    //                         AND TIME_TO_SEC(TIME(MIN(a.datetime))) <= (TIME_TO_SEC(sh.jam_masuk) + TIME_TO_SEC(sh.jam_pulang)) / 2
    //                         THEN '01:00:00'

    //                     WHEN MAX(a.datetime) IS NULL THEN NULL
    //                     WHEN TIME(MAX(a.datetime)) < sh.jam_pulang
    //                         THEN TIMEDIFF(sh.jam_pulang, TIME(MAX(a.datetime)))
    //                     ELSE '00:00:00'
    //                 END as pulang_cepat
    //             "),

    //             // Total Jam:
    //             // - no absen => NULL
    //             // - single scan considered pulang => (scan_pulang - jam_masuk) - 1 jam
    //             // - single scan considered masuk  => (jam_pulang - scan_masuk) - 1 jam
    //             // - otherwise => MAX - MIN
    //             DB::raw("
    //                 CASE
    //                     WHEN COUNT(a.id) = 0 THEN NULL

    //                     WHEN COUNT(a.id) = 1
    //                         AND TIME_TO_SEC(TIME(MIN(a.datetime))) > (TIME_TO_SEC(sh.jam_masuk) + TIME_TO_SEC(sh.jam_pulang)) / 2
    //                         THEN SEC_TO_TIME(
    //                             TIME_TO_SEC(TIMEDIFF(TIME(MAX(a.datetime)), sh.jam_masuk)) - 3600
    //                         )

    //                     WHEN COUNT(a.id) = 1
    //                         AND TIME_TO_SEC(TIME(MIN(a.datetime))) <= (TIME_TO_SEC(sh.jam_masuk) + TIME_TO_SEC(sh.jam_pulang)) / 2
    //                         THEN SEC_TO_TIME(
    //                             TIME_TO_SEC(TIMEDIFF(sh.jam_pulang, TIME(MIN(a.datetime)))) - 3600
    //                         )

    //                     ELSE TIMEDIFF(MAX(a.datetime), MIN(a.datetime))
    //                 END as total_jam
    //             "),

    //             // Keterangan
    //             DB::raw("
    //                 CASE
    //                     WHEN p.id IS NOT NULL THEN 'izin'
    //                     WHEN COUNT(a.id) = 0 THEN 'tidak masuk'
    //                     WHEN COUNT(a.id) = 1 THEN 'masuk (absen tidak lengkap)'
    //                     ELSE 'masuk'
    //                 END as keterangan
    //             ")
    //         )

    //         ->whereBetween('s.tanggal', [$start, $end])
    //         ->groupBy(
    //             's.id', 'u.name', 's.tanggal',
    //             'sh.nama_shift', 'sh.jam_masuk', 'sh.jam_pulang',
    //             'p.id'
    //         )
    //         ->orderBy('s.tanggal', 'ASC')
    //         ->get();

    //     return response()->json($rekap);
    // }

    public function index(){
        return view('backend.laporan.shift.index');
    }

    public function data(Request $request)
    {
        $start      = $request->start_date;
        $end        = $request->end_date;
        $id_user    = $request->id_user;

        $data = DB::table('schedules as s')
            ->join('users as u', 'u.id', '=', 's.id_user')
            ->join('shifts as sh', 'sh.id', '=', 's.id_shift')
            ->selectRaw("
                u.name AS user,
                s.tanggal,
                sh.nama_shift AS shift,

                -- shift start
                CONCAT(s.tanggal, ' ', sh.jam_masuk) AS shift_start_dt,

                -- shift end (next day for night shift)
                CONCAT(
                    CASE 
                        WHEN sh.jam_pulang < sh.jam_masuk 
                            THEN DATE_ADD(s.tanggal, INTERVAL 1 DAY)
                        ELSE s.tanggal 
                    END,
                    ' ',
                    sh.jam_pulang
                ) AS shift_end_dt,

                -- scan masuk
                (
                    SELECT MIN(datetime)
                    FROM absensis 
                    WHERE user_id = s.id_user
                    AND datetime BETWEEN 
                        CONCAT(s.tanggal, ' ', sh.mulai_scan_masuk)
                    AND 
                        CONCAT(s.tanggal, ' ', sh.akhir_scan_masuk)
                ) AS scan_masuk,

                -- scan pulang
                (
                    SELECT MAX(datetime)
                    FROM absensis 
                    WHERE user_id = s.id_user
                    AND datetime BETWEEN 
                        CONCAT(
                            CASE 
                                WHEN sh.mulai_scan_pulang < sh.mulai_scan_masuk 
                                    THEN DATE_ADD(s.tanggal, INTERVAL 1 DAY)
                                ELSE s.tanggal 
                            END,
                            ' ',
                            sh.mulai_scan_pulang
                        )
                    AND 
                        CONCAT(
                            CASE 
                                WHEN sh.akhir_scan_pulang < sh.mulai_scan_masuk 
                                    THEN DATE_ADD(s.tanggal, INTERVAL 1 DAY)
                                ELSE s.tanggal 
                            END,
                            ' ',
                            sh.akhir_scan_pulang
                        )
                ) AS scan_pulang,

                -- izin
                (
                    SELECT COUNT(*) 
                    FROM perizinans p
                    WHERE p.user_id = s.id_user
                    AND p.tanggal = s.tanggal
                ) AS cnt_izin
            ")
            ->whereBetween('s.tanggal', [$start, $end])
            ->where('u.id', $id_user)
            ->orderBy('s.tanggal', 'ASC')
            ->get()
            ->map(function ($row) {

                // datetime base
                $shiftStart = new \DateTime($row->shift_start_dt);
                $shiftEnd   = new \DateTime($row->shift_end_dt);

                $scanMasuk  = $row->scan_masuk ? new \DateTime($row->scan_masuk) : null;
                $scanPulang = $row->scan_pulang ? new \DateTime($row->scan_pulang) : null;

                // default
                $terlambat    = "00:00:00";
                $pulangCepat  = "00:00:00";
                $totalJam     = "00:00:00";
                $keterangan   = "tidak masuk";

                // --- STATUS ---
                if ($scanMasuk || $scanPulang) {
                    $keterangan = "masuk";  // scan lebih kuat dari izin
                } elseif ($row->cnt_izin > 0) {
                    $keterangan = "izin";
                }

                // --- PERHITUNGAN JIKA ADA SCAN ---
                if ($scanMasuk || $scanPulang) {

                    // TERLAMBAT
                    if ($scanMasuk) {
                        if ($scanMasuk > $shiftStart) {
                            $late = $shiftStart->diff($scanMasuk);
                            $terlambat = $late->format("%H:%I:%S");
                        }
                    } else {
                        // lupa masuk
                        $terlambat = "01:00:00";
                    }

                    // PULANG CEPAT
                    if ($scanPulang) {
                        if ($scanPulang < $shiftEnd) {
                            $pc = $scanPulang->diff($shiftEnd);
                            $pulangCepat = $pc->format("%H:%I:%S");
                        }
                    } else {
                        // lupa pulang
                        $pulangCepat = "01:00:00";
                    }

                    // TOTAL JAM
                    if ($scanMasuk && $scanPulang) {

                        // normal
                        $dur = $scanMasuk->diff($scanPulang);
                        $totalJam = $dur->format("%H:%I:%S");

                    } elseif ($scanMasuk && !$scanPulang) {

                        // lupa pulang → hitung sampai shiftEnd - 1 jam
                        $endAdj = (clone $shiftEnd)->modify("-1 hour");
                        $dur = $scanMasuk->diff($endAdj);
                        $totalJam = $dur->format("%H:%I:%S");

                    } elseif (!$scanMasuk && $scanPulang) {

                        // lupa masuk → hitung dari shiftStart + 1 jam
                        $startAdj = (clone $shiftStart)->modify("+1 hour");
                        $dur = $startAdj->diff($scanPulang);
                        $totalJam = $dur->format("%H:%I:%S");
                    }
                }

                // set result
                $row->terlambat    = $terlambat;
                $row->pulang_cepat = $pulangCepat;
                $row->total_jam    = $totalJam;
                $row->keterangan   = $keterangan;

                return $row;
            });

        return response()->json(['data' => $data]);
    }


}
