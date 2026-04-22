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

        // $users = DB::table('users')
        //     ->where('opd_id', $batch->opd_id)
        //     ->pluck('id');

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
            $hasil = $service->hitungRange(
                $userId,
                $batch->tanggal_awal,
                $batch->tanggal_akhir
            );

            DB::table('rekap_absensi_details')->insert([
                'batch_id'          => $this->batchId,
                'user_id'           => $userId,
                'detik_kerja'       => $hasil['detik_kerja'],
                'detik_terlambat'   => $hasil['detik_terlambat'],
                'total_hadir'       => $hasil['total_hadir'],
                'total_jadwal'      => $hasil['total_shift'],
                'created_at'        => now(),
            ]);
        }

        DB::table('rekap_absensi_batches')
            ->where('id', $this->batchId)
            ->update(['status' => 'done']);
    }
}
