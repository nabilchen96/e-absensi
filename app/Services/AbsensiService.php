<?php

namespace App\Services;

use DB;
use DateTime;
use DatePeriod;
use DateInterval;

class AbsensiService
{
    public function hitungRange($userId, $start, $end)
    {
        $dates = [];
        $period = new DatePeriod(
            new DateTime($start),
            new DateInterval('P1D'),
            (new DateTime($end))->modify('+1 day')
        );

        foreach ($period as $dt) {
            $dates[] = $dt->format('Y-m-d');
        }

        // =========================
        // AMBIL ABSENSI
        // =========================
        $absensiRaw = DB::table('absensis')
            ->where('user_id', $userId)
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

        $totalDetikKerja = 0;
        $totalDetikTerlambat = 0;
        $totalHadir = 0;
        $totalShift = 0;

        foreach ($dates as $tanggal) {

            $schedules = DB::table('schedules as s')
                ->leftJoin('shifts as sh', 'sh.id', '=', 's.id_shift')
                ->where('s.id_user', $userId)
                ->where('s.tanggal', $tanggal)
                ->orderBy('sh.jam_masuk')
                ->get();

            if ($schedules->isEmpty()) continue;

            $totalShift += count($schedules);

            $absensis = array_merge(
                $absensiByDate[$tanggal] ?? [],
                $absensiByDate[date('Y-m-d', strtotime($tanggal . ' +1 day'))] ?? []
            );

            $usedScans = [];

            foreach ($schedules as $sch) {

                $shiftStart = new DateTime($tanggal . ' ' . $sch->jam_masuk);

                $scanMasukStart = new DateTime($tanggal . ' ' . $sch->mulai_scan_masuk);
                $scanMasukEnd   = new DateTime($tanggal . ' ' . $sch->akhir_scan_masuk);

                $scanPulangDate = $sch->mulai_scan_pulang < $sch->mulai_scan_masuk
                    ? date('Y-m-d', strtotime($tanggal . ' +1 day'))
                    : $tanggal;

                $scanPulangStart = new DateTime($scanPulangDate . ' ' . $sch->mulai_scan_pulang);
                $scanPulangEnd   = new DateTime($scanPulangDate . ' ' . $sch->akhir_scan_pulang);

                $scanMasuk = null;
                $scanPulang = null;

                // scan masuk
                foreach ($absensis as $i => $scan) {
                    if (in_array($i, $usedScans)) continue;
                    if ($scan >= $scanMasukStart && $scan <= $scanMasukEnd) {
                        $scanMasuk = $scan;
                        $usedScans[] = $i;
                        break;
                    }
                }

                // scan pulang
                foreach ($absensis as $i => $scan) {
                    if (in_array($i, $usedScans)) continue;
                    if ($scanMasuk && $scan <= $scanMasuk) continue;
                    if ($scan >= $scanPulangStart && $scan <= $scanPulangEnd) {
                        $scanPulang = $scan;
                        $usedScans[] = $i;
                        break;
                    }
                }

                // terlambat
                if ($scanMasuk && $scanMasuk > $shiftStart) {
                    $diff = $shiftStart->diff($scanMasuk);
                    $totalDetikTerlambat += ($diff->h * 3600) + ($diff->i * 60) + $diff->s;
                }

                // jam kerja
                if ($scanMasuk && $scanPulang && $scanPulang > $scanMasuk) {
                    $diff = $scanMasuk->diff($scanPulang);
                    $totalDetikKerja += ($diff->h * 3600) + ($diff->i * 60) + $diff->s;

                    $totalHadir++;
                }
            }
        }

        return [
            'detik_kerja'     => $totalDetikKerja,
            'detik_terlambat' => $totalDetikTerlambat,
            'total_hadir'     => $totalHadir,
            'total_shift'     => $totalShift,
        ];
    }
}