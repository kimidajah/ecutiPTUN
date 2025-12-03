<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('nip')->nullable()->after('email');
            $table->string('jabatan')->nullable()->after('nip');
            $table->string('gol_ruang')->nullable()->after('jabatan');
            $table->string('unit_kerja')->nullable()->after('gol_ruang');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['nip','jabatan','gol_ruang','unit_kerja']);
        });
    }
};
