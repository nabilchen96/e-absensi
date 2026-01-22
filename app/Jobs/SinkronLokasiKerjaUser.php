<?php

namespace App\Jobs;

use App\Models\DetailUser;
use App\Models\LokasiKerja;
use App\Models\LokasiKerjaUser;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class SinkronLokasiKerjaUser implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 120; // 2 menit
    public $tries = 3;

    public function handle(): void
    {
        DB::beginTransaction();

        try {
            // Ambil semua detail user yang punya satuan kerja
            DetailUser::whereNotNull('satuan_kerja')
                ->whereNotNull('user_id')
                ->chunk(200, function ($detailUsers) {

                    foreach ($detailUsers as $detail) {

                        // Cari lokasi kerja yang sama (case insensitive)
                        $lokasi = LokasiKerja::whereRaw(
                            'LOWER(lokasi_kerja) = ?',
                            [strtolower(trim($detail->satuan_kerja))]
                        )->first();

                        if (!$lokasi) {
                            continue; // skip kalau tidak ditemukan
                        }

                        // Insert atau update agar tidak dobel
                        LokasiKerjaUser::updateOrCreate(
                            [
                                'id_user' => $detail->user_id,
                            ],
                            [
                                'id_lokasi_kerja' => $lokasi->id,
                            ]
                        );
                    }
                });

            DB::commit();

        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
