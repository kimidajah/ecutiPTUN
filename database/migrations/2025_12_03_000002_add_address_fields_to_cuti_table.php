<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('cuti', function (Blueprint $table) {
            $table->string('alamat_selama_cuti')->nullable()->after('catatan_pimpinan');
            $table->string('telp_selama_cuti')->nullable()->after('alamat_selama_cuti');
        });
    }

    public function down(): void
    {
        Schema::table('cuti', function (Blueprint $table) {
            $table->dropColumn(['alamat_selama_cuti','telp_selama_cuti']);
        });
    }
};
