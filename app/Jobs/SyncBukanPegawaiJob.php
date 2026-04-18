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

            \Log::info("Sync page: $page");

            $response = Http::withToken('2y10Q71xkKprwaAZLzn0vhVnuepOFZoauSQokJqhJhPLlWpETCX8daNwa')
                ->timeout(60)              // ⬅️ naikkan timeout
                ->retry(3, 2000)            // ⬅️ retry 3x, delay 2 detik
                ->get('https://pandu.bengkuluutarakab.go.id/api/data-bukan-pegawai', [
                    'page' => $page,
                    'per_page' => $perPage,
                ]);

            if (!$response->successful()) {
                \Log::error('API ERROR', ['body' => $response->body()]);
                break;
            }

            $data = $response->json();
            $users = $data['data'] ?? [];

            if (empty($users)) {
                break;
            }

            foreach ($users as $u) {

                if ($u['role'] === 'Pegawai') {
                    continue;
                }

                $user = User::firstOrNew([
                    'id_pandu' => $u['id'], // ✅ lebih aman dari email
                ]);

                $user->name = $u['name'];
                $user->email = $u['email'];
                $user->role = $u['role'];
                $user->id_unit_kerja_pandu = $u['id_unit_kerja'];
                $user->id_skpd_pandu = $u['id_skpd'] ?? $u['id_skpd_unit_kerja'];

                // ✅ hanya set password kalau user baru
                if (!$user->exists) {
                    $user->password = bcrypt('default123');
                }

                $user->save();

            }

            $page++; // lanjut ke halaman berikutnya
            sleep(1); // ⬅️ penting
        }
    }
}
