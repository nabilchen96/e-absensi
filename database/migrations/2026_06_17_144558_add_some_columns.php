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
        Schema::table('rekap_absensi_details', function (Blueprint $table) {
            $table->string('total_absensi_masuk')->nullable();
            $table->string('total_absensi_pulang')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rekap_absensi_details', function (Blueprint $table) {
            //
        });
    }
};
