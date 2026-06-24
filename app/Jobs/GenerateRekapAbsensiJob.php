<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use DB;
use Auth;
use App\Services\AbsensiService;

class GenerateRekapAbsensiJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $batchId; // 🔥 WAJIB ADA

    public function __construct($batchId)
    {
        $this->batchId = $batchId; // 🔥 isi di constructor
    }

    public function handle(AbsensiService $service)
    {
        $batch = DB::table('rekap_absensi_batches')->where('id', $this->batchId)->first();

        DB::table('rekap_absensi_batches')
            ->where('id', $this->batchId)
            ->update(['status' => 'processing']);

        $idUnitKerja = $batch->id_unit_kerja_pandu;

        $users = DB::table('users')
            ->leftJoin('lokasi_kerja_users as lku', 'lku.id_user', '=', 'users.id')
            ->leftJoin('lokasi_kerjas as lk', 'lk.id', '=', 'lku.id_lokasi_kerja')
            ->where(function ($q) use ($idUnitKerja) {

                // untuk pegawai (pakai pivot)
                $q->where('lk.id_pandu', $idUnitKerja);

            })
            ->orWhere(function ($q) use ($idUnitKerja) {

                // untuk role OPD langsung
                $q->where('users.role', 'OPD')
                ->where('users.id_unit_kerja_pandu', $idUnitKerja);

            })
            ->select('users.id')
            ->distinct()
            ->pluck('users.id');

        foreach ($users as $userId) {

            // 🔥 Panggil function lama kamu (refactor jadi service)
            // $hasil = $service->hitungRange(
            //     $userId,
            //     $batch->tanggal_awal,
            //     $batch->tanggal_akhir
            // );

            // DB::table('rekap_absensi_details')->insert([
            //     'batch_id'          => $this->batchId,
            //     'user_id'           => $userId,
            //     'detik_kerja'       => $hasil['detik_kerja'],
            //     'detik_terlambat'   => $hasil['detik_terlambat'],
            //     // 'total_hadir'       => $hasil['total_hadir'],
            //     // 'total_jadwal'      => $hasil['total_shift'],
            //     'created_at'        => now(),
            // ]);

            $data = DB::table('absensis as a')
                ->select(
                    DB::raw('DATE(a.datetime) as tanggal'),
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
                ->where('a.user_id', $userId)
                ->whereBetween(
                    DB::raw('DATE(a.datetime)'),
                    [$batch->tanggal_awal, $batch->tanggal_akhir]
                )
                ->groupBy(
                    DB::raw('DATE(a.datetime)'),
                    'a.status_shift'
                )
                ->get();

            $totalJamKerja = 0;
            $totalTerlambat = 0;

            $jadwalMasuk = [
                'Reguler' => '07:30:00',
                'Sore'    => '16:00:00',
                'Malam'   => '23:00:00'
            ];

            foreach ($data as $row) {

                $terlambat = 0;

                if ($row->jam_scan_masuk) {

                    $jamMasuk = \Carbon\Carbon::parse($row->jam_scan_masuk);

                    $jadwal = \Carbon\Carbon::parse(
                        $row->tanggal.' '.$jadwalMasuk[$row->shift]
                    );

                    if ($jamMasuk->gt($jadwal)) {
                        $terlambat = $jamMasuk->diffInSeconds($jadwal);
                    }
                }

                $jamKerja = 0;

                if ($row->jam_scan_masuk && $row->jam_scan_pulang) {

                    $masuk = \Carbon\Carbon::parse($row->jam_scan_masuk);
                    $pulang = \Carbon\Carbon::parse($row->jam_scan_pulang);

                    if ($pulang->lt($masuk)) {
                        $pulang->addDay();
                    }

                    $jamKerja = $masuk->diffInSeconds($pulang);
                }

                $totalJamKerja += $jamKerja;
                $totalTerlambat += $terlambat;
            }

            $absensi = DB::table('absensis')
                ->where('status_absensi', 'Diterima')
                ->where('user_id', $userId)
                ->whereBetween(
                    DB::raw('DATE(datetime)'),
                    [$batch->tanggal_awal, $batch->tanggal_akhir]
                )
                ->selectRaw("
                    SUM(CASE WHEN jenis_absensi='Masuk' THEN 1 ELSE 0 END) as total_absensi_masuk,
                    SUM(CASE WHEN jenis_absensi='Pulang' THEN 1 ELSE 0 END) as total_absensi_pulang
                ")
                ->first();

            DB::table('rekap_absensi_details')->insert([
                'batch_id'             => $this->batchId,
                'user_id'              => $userId,
                'detik_kerja'          => $totalJamKerja,
                'detik_terlambat'      => $totalTerlambat,
                'total_absensi_masuk'  => $absensi->total_absensi_masuk ?? 0,
                'total_absensi_pulang' => $absensi->total_absensi_pulang ?? 0,
                'created_at'           => now(),
            ]);
        }

        DB::table('rekap_absensi_batches')
            ->where('id', $this->batchId)
            ->update(['status' => 'done']);
    }
}
