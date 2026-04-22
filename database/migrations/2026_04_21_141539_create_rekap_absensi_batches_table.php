<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('rekap_absensi_batches', function (Blueprint $table) {
            $table->id();

            $table->text('judul_laporan');
            $table->date('tanggal_awal');
            $table->date('tanggal_akhir');
            $table->unsignedBigInteger('id_unit_kerja_pandu'); //id_unit_kerja_pandu diambil dari tabel users

            $table->enum('status', ['pending','processing','done','failed'])->default('pending');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rekap_absensi_batches');
    }
};
