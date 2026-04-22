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
        Schema::create('rekap_absensi_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('batch_id');

            $table->unsignedBigInteger('user_id');

            $table->integer('detik_kerja')->default(0);
            $table->integer('detik_terlambat')->default(0);

            $table->integer('total_hadir')->default(0);
            $table->integer('total_tidak_masuk')->default(0);
            $table->integer('total_tidak_pulang')->default(0);
            $table->integer('total_jadwal')->default(0);

            $table->timestamps();

            $table->foreign('batch_id')->references('id')->on('rekap_absensi_batches')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rekap_absensi_details');
    }
};
