<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('hr_id')->nullable()->after('role');
            $table->unsignedBigInteger('pimpinan_id')->nullable()->after('hr_id');

            $table->foreign('hr_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('pimpinan_id')->references('id')->on('users')->nullOnDelete();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
