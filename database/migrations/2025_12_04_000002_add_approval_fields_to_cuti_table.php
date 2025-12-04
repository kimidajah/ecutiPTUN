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
        Schema::table('cuti', function (Blueprint $table) {
            // Tambah kolom untuk approval ketua
            $table->text('catatan_ketua')->nullable()->after('catatan_pimpinan');
            
            // Update status enum untuk menambah approval ketua
            $table->dropColumn('status');
            $table->enum('status', ['menunggu', 'disetujui_hr', 'disetujui_ketua', 'disetujui_pimpinan', 'ditolak'])
                ->default('menunggu');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cuti', function (Blueprint $table) {
            $table->dropColumn('catatan_ketua');
            $table->dropColumn('status');
            $table->enum('status', ['menunggu', 'disetujui_hr', 'disetujui_pimpinan', 'ditolak'])
                ->default('menunggu');
        });
    }
};
