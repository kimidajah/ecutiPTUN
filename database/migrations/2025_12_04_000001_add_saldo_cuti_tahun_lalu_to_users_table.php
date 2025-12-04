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
            // Saldo cuti tahun lalu yang belum dipakai
            $table->integer('saldo_cuti_tahun_lalu')->default(0)->after('saldo_cuti_tahunan')->comment('Saldo cuti tahun lalu yang belum dipakai');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('saldo_cuti_tahun_lalu');
        });
    }
};
