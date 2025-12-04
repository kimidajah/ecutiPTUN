<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// Schedule reset cuti tahunan setiap 1 Januari jam 00:00 WIB
Schedule::command('cuti:reset-tahunan')
    ->yearly()
    ->at('00:00')
    ->timezone('Asia/Jakarta');
