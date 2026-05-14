<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use DatePeriod;
use DateTime;
use DateInterval;

class LaporanShiftController extends Controller
{

    public function index(){

        return view('backend.laporan.shift.index');

    }

    function formatJam($detik)
    {
        $jam = floor($detik / 3600);
        $menit = floor(($detik % 3600) / 60);
        $detik = $detik % 60;

        return sprintf('%02d:%02d:%02d', $jam, $menit, $detik);
    }

    public function data(Request $request){

        $start   = $request->start_date; // 2026-01-10
        $end     = $request->end_date;   // 2026-01-15
        $id_user = $request->id_user;

        $dates = [];
        $period = new DatePeriod(
            new DateTime($start),
            new DateInterval('P1D'),
            (new DateTime($end))->modify('+1 day')
        );

        foreach ($period as $dt) {
            $dates[] = $dt->format('Y-m-d');
        }

        $absensiRaw = DB::table('absensis')
            ->where('user_id', $id_user)
            ->whereBetween('datetime', [
                $start . ' 00:00:00',
                date('Y-m-d', strtotime($end . ' +1 day')) . ' 23:59:59'
            ])
            ->orderBy('datetime')
            ->get()
            ->map(fn($a) => new DateTime($a->datetime));

        $absensiByDate = [];

        foreach ($absensiRaw as $dt) {
            $key = $dt->format('Y-m-d');
            $absensiByDate[$key][] = $dt;
        }

        
        $result = [];
        
        $totalDetikKerja = 0;
        $totalDetikTerlambat = 0;
        $totalHadir = 0;
        
        foreach ($dates as $tanggal) {

            // schedule hari ini
            $schedules = DB::table('schedules as s')
                ->leftjoin('shifts as sh', 'sh.id', '=', 's.id_shift')
                ->leftjoin('users', 'users.id', '=', 's.id_user')
                ->join('schedule_requests', 'schedule_requests.id', '=', 's.id_schedule_request')
                ->where('s.id_user', $id_user)
                ->where('s.tanggal', $tanggal)
                ->where('schedule_requests.status', 'Disetujui')
                ->orderBy('sh.jam_masuk')
                ->get();

            if ($schedules->isEmpty()) continue;

            // ambil absensi hari ini + besok (untuk shift malam)
            $absensis = array_merge(
                $absensiByDate[$tanggal] ?? [],
                $absensiByDate[date('Y-m-d', strtotime($tanggal . ' +1 day'))] ?? []
            );

            $usedScans = [];

            foreach ($schedules as $index => $sch) {

                // =========================
                // HITUNG WINDOW WAKTU
                // =========================
                $shiftStart = new DateTime($tanggal . ' ' . $sch->jam_masuk);

                $shiftEndDate = $sch->jam_pulang < $sch->jam_masuk
                    ? date('Y-m-d', strtotime($tanggal . ' +1 day'))
                    : $tanggal;

                $shiftEnd = new DateTime($shiftEndDate . ' ' . $sch->jam_pulang);

                // window scan masuk
                $scanMasukStart = new DateTime($tanggal . ' ' . $sch->mulai_scan_masuk);
                $scanMasukEnd   = new DateTime($tanggal . ' ' . $sch->akhir_scan_masuk);

                // window scan pulang
                $scanPulangDate = $sch->mulai_scan_pulang < $sch->mulai_scan_masuk
                    ? date('Y-m-d', strtotime($tanggal . ' +1 day'))
                    : $tanggal;

                $scanPulangStart = new DateTime($scanPulangDate . ' ' . $sch->mulai_scan_pulang);
                $scanPulangEnd   = new DateTime($scanPulangDate . ' ' . $sch->akhir_scan_pulang);

                $scanMasuk  = null;
                $scanPulang = null;

                // =========================
                // SCAN MASUK
                // =========================
                foreach ($absensis as $i => $scan) {
                    if (in_array($i, $usedScans)) continue;
                    if ($scan >= $scanMasukStart && $scan <= $scanMasukEnd) {
                        $scanMasuk = $scan;
                        $usedScans[] = $i;
                        break;
                    }
                }

                // =========================
                // SCAN PULANG
                // =========================
                foreach ($absensis as $i => $scan) {
                    if (in_array($i, $usedScans)) continue;
                    if ($scanMasuk && $scan <= $scanMasuk) continue;
                    if ($scan >= $scanPulangStart && $scan <= $scanPulangEnd) {
                        $scanPulang = $scan;
                        $usedScans[] = $i;
                        break;
                    }
                }

                $nextSchedule = $schedules[$index + 1] ?? null;

                $maxCheckoutTime = null;

                if ($nextSchedule) {

                    $nextShiftDate = $nextSchedule->tanggal;

                    $maxCheckoutTime = new DateTime(
                        $nextShiftDate . ' ' . $nextSchedule->jam_masuk
                    );
                }

                // =========================
                // FALLBACK 4 JAM
                // =========================
                if (!$scanPulang && $scanMasuk) {

                    foreach ($absensis as $i => $scan) {

                        if (in_array($i, $usedScans)) continue;

                        // harus setelah scan masuk
                        if ($scan <= $scanMasuk) continue;

                        $selisihDetik = $scan->getTimestamp() - $scanMasuk->getTimestamp();

                        // minimal 4 jam dan maksimal 12 jam
                        // if ($selisihDetik >= (4 * 3600)) {

                        //     $scanPulang = $scan;
                        //     $usedScans[] = $i;

                        //     break;
                        // }

                        if ($selisihDetik >= (4 * 3600) && $selisihDetik <= (12 * 3600)) {

                            // jangan ambil scan milik shift berikutnya
                            if ($maxCheckoutTime && $scan >= $maxCheckoutTime) {
                                continue;
                            }

                            $scanPulang = $scan;
                            $usedScans[] = $i;

                            break;
                        }
                    }
                }


                // =========================
                // HITUNG TERLAMBAT
                // =========================
                $terlambat = "00:00:00";

                if ($scanMasuk) {
                    if ($scanMasuk > $shiftStart) {
                        $diff = $shiftStart->diff($scanMasuk);
                        $terlambat = $diff->format('%H:%I:%S');
                    }
                }

                // =========================
                // HITUNG TOTAL JAM
                // =========================
                $totalJam = "00:00:00";

                if ($scanMasuk && $scanPulang) {
                    if ($scanPulang > $scanMasuk) {
                        $diff = $scanMasuk->diff($scanPulang);
                        $totalJam = $diff->format('%H:%I:%S');
                    }
                }

                // =========================
                // KETERANGAN ABSENSI
                // =========================
                $keterangan = 'Tidak Hadir';

                if ($scanMasuk && $scanPulang) {
                    $keterangan = 'Hadir';
                } elseif ($scanMasuk && !$scanPulang) {
                    $keterangan = 'Tidak absen pulang';
                } elseif (!$scanMasuk && $scanPulang) {
                    $keterangan = 'Tidak absen masuk';
                }



                // =========================
                // SIMPAN HASIL
                // =========================
                $result[] = [
                    'tanggal'      => $tanggal,
                    'user'         => $sch->name,
                    'shift'        => $sch->nama_shift,
                    'scan_masuk'   => $scanMasuk ? $scanMasuk->format('H:i:s') : '',
                    'scan_pulang'  => $scanPulang ? $scanPulang->format('H:i:s') : '',
                    'terlambat'    => $terlambat,
                    'total_jam'    => $totalJam,
                    'keterangan'   => $keterangan,
                ];
            }
        }

        foreach ($result as $row) {

            // TOTAL JAM KERJA
            if (!empty($row['total_jam']) && $row['total_jam'] != "00:00:00") {
                list($h, $m, $s) = explode(':', $row['total_jam']);
                $totalDetikKerja += ($h * 3600) + ($m * 60) + $s;
            }

            // TOTAL TERLAMBAT
            if (!empty($row['terlambat']) && $row['terlambat'] != "00:00:00") {
                list($h, $m, $s) = explode(':', $row['terlambat']);
                $totalDetikTerlambat += ($h * 3600) + ($m * 60) + $s;
            }

            // TOTAL HADIR
            if (!empty($row['scan_masuk']) && !empty($row['scan_pulang'])) {
                $totalHadir++;
            }
        }

        $totalShift = count($result);

        // dd($result);
        return response()->json([
            'data' => $result,
            'summary' => [
                'total_jam_kerja'   => $this->formatJam($totalDetikKerja),
                'total_terlambat'   => $this->formatJam($totalDetikTerlambat),
                'total_hadir'       => $totalHadir,
                'total_shift'       => $totalShift,
            ]
        ]);
    }
}
