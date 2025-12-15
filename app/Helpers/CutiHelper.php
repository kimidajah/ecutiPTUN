<?php

namespace App\Helpers;

use App\Models\Cuti;
use App\Models\HariLibur;
use App\Models\PengaturanCuti;
use App\Models\User;
use Carbon\Carbon;

class CutiHelper
{
    public static function calculateDays($mulai, $selesai)
    {
        $start = \Carbon\Carbon::parse($mulai);
        $end   = \Carbon\Carbon::parse($selesai);

        return $start->diffInDays($end) + 1;
    }

    /**
     * Get sisa cuti berdasarkan jenis cuti
     */
    public static function sisaCutiByJenis($userId, $jenisCuti)
    {
        $user = User::find($userId);
        
        if (!$user) {
            return 0;
        }
        
        // Ambil saldo dari user
        return $user->getSaldoCutiByJenis($jenisCuti);
    }

    /**
     * Sisa cuti tahunan (backward compatibility)
     */
    public static function sisaCutiTahunan($userId)
    {
        return self::sisaCutiByJenis($userId, 'tahunan');
    }

    /**
     * Validasi apakah cuti bisa diajukan berdasarkan saldo
     */
    public static function validateCutiSaldo($userId, $jenisCuti, $lamaCuti)
    {
        $sisaSaldo = self::sisaCutiByJenis($userId, $jenisCuti);
        
        if ($lamaCuti > $sisaSaldo) {
            return [
                'valid' => false,
                'message' => "Saldo cuti tidak mencukupi. Sisa saldo: {$sisaSaldo} hari, dibutuhkan: {$lamaCuti} hari"
            ];
        }
        
        return [
            'valid' => true,
            'message' => "Saldo mencukupi. Sisa saldo: {$sisaSaldo} hari"
        ];
    }

    /**
     * Hitung jumlah hari cuti (mengecualikan hari libur nasional)
     * Tidak mengecualikan weekend agar cuti tetap dihitung
     * 
     * @param Carbon\Carbon|string $tanggalMulai
     * @param Carbon\Carbon|string $tanggalSelesai
     * @return int
     */
    public static function hitungLamaCutiExcludeHariLibur($tanggalMulai, $tanggalSelesai)
    {
        $mulai = Carbon::parse($tanggalMulai);
        $selesai = Carbon::parse($tanggalSelesai);
        
        // Total hari dari tanggal mulai hingga selesai
        $totalHari = $mulai->diffInDays($selesai) + 1;
        
        // Hitung jumlah hari libur dalam range
        $hariLiburCount = HariLibur::whereBetween('tanggal', [
            $mulai->format('Y-m-d'),
            $selesai->format('Y-m-d')
        ])->count();
        
        // Total hari - hari libur
        return max(1, $totalHari - $hariLiburCount);
    }

    /**
     * Hitung jumlah hari kerja cuti (mengecualikan hari libur dan weekend)
     * 
     * @param Carbon\Carbon|string $tanggalMulai
     * @param Carbon\Carbon|string $tanggalSelesai
     * @return int
     */
    public static function hitungHariKerjaCuti($tanggalMulai, $tanggalSelesai)
    {
        $mulai = Carbon::parse($tanggalMulai);
        $selesai = Carbon::parse($tanggalSelesai);
        
        $hariKerja = 0;
        
        // Loop setiap hari dari tanggal mulai hingga selesai
        while ($mulai <= $selesai) {
            // Cek apakah hari itu adalah weekend (Sabtu=6, Minggu=0)
            $dayOfWeek = $mulai->dayOfWeek;
            $isWeekend = ($dayOfWeek == 0 || $dayOfWeek == 6);
            
            // Cek apakah hari itu adalah hari libur nasional
            $isHariLibur = HariLibur::where('tanggal', $mulai->format('Y-m-d'))
                ->exists();
            
            // Jika bukan weekend dan bukan hari libur, hitung sebagai hari kerja
            if (!$isWeekend && !$isHariLibur) {
                $hariKerja++;
            }
            
            $mulai->addDay();
        }
        
        return max(1, $hariKerja);
    }
}
