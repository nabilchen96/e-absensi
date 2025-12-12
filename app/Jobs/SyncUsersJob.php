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

class SyncUsersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle()
    {
        $page = 1;
        $perPage = 10;

        while (true) {

            $response = Http::withToken('2y10Q71xkKprwaAZLzn0vhVnuepOFZoauSQokJqhJhPLlWpETCX8daNwa')
                ->get('https://pandu.bengkuluutarakab.go.id/api/data-pegawai', [
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
                    ]
                );

                DetailUser::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'user_id'        => $user->id,
                        'nip'            => $u['nip'],
                        'jenis_kelamin'  => $u['jenis_kelamin'],
                        'tempat_lahir'   => $u['tempat_lahir'],
                        'tanggal_lahir'  => $u['tanggal_lahir'],
                        'jenis_asn'      => $u['status_pegawai'],
                        'jabatan'        => $u['jabatan'],
                        'pangkat'        => $u['pangkat'],
                        'instansi_kerja' => $u['instansi_kerja'],
                        'satuan_kerja'   => $u['satuan_kerja'],
                    ]
                );
            }

            $page++; // lanjut ke halaman berikutnya
        }
    }

}
