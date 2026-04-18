<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

use App\Models\User;
use App\Models\DetailUser;

class SyncBukanPegawaiJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $page = 1;
        $perPage = 10;

        while (true) {

            $response = Http::withToken('2y10Q71xkKprwaAZLzn0vhVnuepOFZoauSQokJqhJhPLlWpETCX8daNwa')
                ->timeout(60)              // ⬅️ naikkan timeout
                ->retry(3, 2000)            // ⬅️ retry 3x, delay 2 detik
                ->get('https://pandu.bengkuluutarakab.go.id/api/data-bukan-pegawai', [
                    'page' => $page,
                    'per_page' => $perPage,
                ])
                ->json();

            $users = $response['data'] ?? [];

            // ❗ Jika API mengembalikan kosong → selesai
            if (count($users) === 0) {
                break;
            }

            foreach ($users as $u) {

                $user = User::updateOrCreate(
                    ['email' => $u['email']],
                    [
                        'name' => $u['name'],
                        'email' => $u['email'],
                        'password' => bcrypt('default123'),
                        'id_pandu'  => $u['id'],
                        'role'  => $u['role'],
                        'id_unit_kerja_pandu' => $u['id_unit_kerja'],
                        'id_skpd_pandu' => $u['id_skpd'] ?? $u['id_skpd_unit_kerja']
                    ]
                );
            }

            $page++; // lanjut ke halaman berikutnya
            sleep(1); // ⬅️ penting
        }
    }
}
