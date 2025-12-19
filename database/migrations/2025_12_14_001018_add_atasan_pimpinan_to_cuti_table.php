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
            $table->foreignId('atasan_id')->nullable()->constrained('users')->onDelete('set null')->comment('Atasan langsung yang dipilih HR');
            $table->foreignId('pimpinan_id')->nullable()->constrained('users')->onDelete('set null')->comment('Pimpinan yang dipilih HR');
            $table->enum('kategori_atasan', ['PLH', 'Pejabat Definitif'])->nullable()->comment('Kategori PLH atau Pejabat Definitif untuk atasan');
            $table->enum('kategori_pimpinan', ['PLH', 'Pejabat Definitif'])->nullable()->comment('Kategori PLH atau Pejabat Definitif untuk pimpinan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cuti', function (Blueprint $table) {
            $table->dropConstrainedForeignId('atasan_id');
            $table->dropConstrainedForeignId('pimpinan_id');
            $table->dropColumn(['kategori_atasan', 'kategori_pimpinan']);
        });
    }
};
