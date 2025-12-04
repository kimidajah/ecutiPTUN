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
            // Ubah enum untuk menambah role hakim dan ketua
            $table->dropColumn('role');
            $table->enum('role', ['pegawai', 'hakim', 'sub_kepegawaian', 'ketua', 'pimpinan', 'admin'])->default('pegawai');
            
            // Tambah kolom ketua_id untuk relasi ke ketua
            $table->foreignId('ketua_id')->nullable()->constrained('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('ketua_id');
            $table->dropColumn('role');
            $table->enum('role', ['pegawai', 'sub_kepegawaian', 'pimpinan', 'admin'])->default('pegawai');
        });
    }
};
