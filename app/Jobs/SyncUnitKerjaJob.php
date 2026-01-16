<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

// use App\Models\User;
use App\Models\LokasiKerja;

class SyncUnitKerjaJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $page = 1;
        $perPage = 50; // biasanya lebih besar lebih cepat

        while (true) {

            $response = Http::withToken('2y10Q71xkKprwaAZLzn0vhVnuepOFZoauSQokJqhJhPLlWpETCX8daNwa')
                ->timeout(60)              // ⬅️ naikkan timeout
                ->retry(3, 2000) 
                ->get('https://pandu.bengkuluutarakab.go.id/api/data-unit-kerja', [
                    'page' => $page,
                    'per_page' => $perPage,
                ]);

            if (!$response->successful()) {
                break; // stop jika API error
            }

            $data = $response->json()['data'] ?? [];

            if (count($data) === 0) {
                break; // tidak ada data lagi
            }

            foreach ($data as $row) {

                // sinkron juga ke lokasi_kerjas
                LokasiKerja::updateOrCreate(
                    [
                        'id_pandu' => $row['id'],
                    ],
                    [
                        'lokasi_kerja' => $row['unit_kerja'],
                        'latitude'  => $row['latitude'] ?? '0',
                        'longitude' => $row['longitude'] ?? '0',
                    ]
                );
                // if (!empty($row['latitude']) && !empty($row['longitude'])) {
                // }
            }

            $page++;
            sleep(1); // ⬅️ penting
        }
    }
}
