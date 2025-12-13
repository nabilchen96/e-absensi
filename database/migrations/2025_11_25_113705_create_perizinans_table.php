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
        Schema::create('perizinans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->date('tanggal');
            $table->enum('jenis', [
                'Perjalanan Dinas', 
                'Pekerjaan Diluar Kantor', 
                
                'Cuti Tahunan', 
                'Cuti Bersalin',
                'Cuti Alasan Penting', 
                'Cuti Sakit', 
                'Tugas Belajar', 
                'Cuti Diluar Tanggungan Negara', 
                'Cuti Besar'
            ]);
            $table->text('keterangan')->nullable();
            $table->string('file')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('perizinans');
    }
};
