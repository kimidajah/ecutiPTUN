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
        Schema::table('pengaturan_cuti', function (Blueprint $table) {
            // Tambah kolom jenis_cuti
            $table->string('jenis_cuti')->after('nama_pengaturan')->nullable();
            
            // Ubah nama_pengaturan menjadi nullable dan tidak unique
            $table->dropUnique(['nama_pengaturan']);
            $table->string('nama_pengaturan')->nullable()->change();
            
            // Tambah unique constraint untuk kombinasi jenis_cuti
            $table->unique('jenis_cuti');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengaturan_cuti', function (Blueprint $table) {
            $table->dropUnique(['jenis_cuti']);
            $table->dropColumn('jenis_cuti');
            $table->string('nama_pengaturan')->unique()->change();
        });
    }
};
