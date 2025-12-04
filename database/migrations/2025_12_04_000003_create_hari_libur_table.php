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
        Schema::create('hari_libur', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->string('nama_hari_libur');
            $table->string('keterangan')->nullable();
            $table->integer('tahun');
            $table->timestamps();
            
            // Index untuk pencarian cepat
            $table->unique(['tanggal', 'tahun']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hari_libur');
    }
};
