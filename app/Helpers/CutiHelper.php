<?php

namespace App\Helpers;

use App\Models\Cuti;

class CutiHelper
{
    public static function calculateDays($mulai, $selesai)
    {
        $start = \Carbon\Carbon::parse($mulai);
        $end   = \Carbon\Carbon::parse($selesai);

        return $start->diffInDays($end) + 1;
    }

    public static function sisaCutiTahunan($userId)
    {
        $totalCuti = Cuti::where('user_id', $userId)
            ->where('jenis_cuti', 'Cuti Tahunan')
            ->whereYear('tanggal_mulai', now()->year)
            ->sum('lama_cuti');

        $saldoTahunan = 12; // jika default 12 hari

        return $saldoTahunan - $totalCuti;
    }
}
