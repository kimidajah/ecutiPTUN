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
        Schema::table('users', function (Blueprint $table) {
            // Tambah kolom saldo per jenis cuti
            $table->integer('saldo_cuti_sakit')->default(0)->after('saldo_cuti_tahunan');
            $table->integer('saldo_cuti_bersalin')->default(90)->after('saldo_cuti_sakit'); // 3 bulan = 90 hari
            $table->integer('saldo_cuti_penting')->default(12)->after('saldo_cuti_bersalin');
            $table->integer('saldo_cuti_besar')->default(60)->after('saldo_cuti_penting'); // 2 bulan = 60 hari
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['saldo_cuti_sakit', 'saldo_cuti_bersalin', 'saldo_cuti_penting', 'saldo_cuti_besar']);
        });
    }
};
