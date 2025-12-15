<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengaturanCuti extends Model
{
    protected $table = 'pengaturan_cuti';
    
    protected $fillable = [
        'nama_pengaturan',
        'jenis_cuti',
        'jumlah_cuti_per_tahun',
        'keterangan',
    ];
    
    /**
     * Get jumlah cuti per tahun berdasarkan jenis cuti
     */
    public static function getJumlahCutiByJenis($jenisCuti)
    {
        $pengaturan = self::where('jenis_cuti', $jenisCuti)->first();
        
        if (!$pengaturan) {
            // Default values jika tidak ditemukan
            $defaults = [
                'tahunan' => 30,
                'sakit' => 14,
                'bersalin' => 90,
                'penting' => 12,
                'besar' => 60,
            ];
            
            return $defaults[$jenisCuti] ?? 0;
        }
        
        return $pengaturan->jumlah_cuti_per_tahun;
    }

    /**
     * Check apakah jenis cuti unlimited
     */
    public static function isUnlimited($jenisCuti)
    {
        return self::getJumlahCutiByJenis($jenisCuti) === 0;
    }
}
    