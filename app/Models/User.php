<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'nip',
        'jabatan',
        'gol_ruang',
        'unit_kerja',
        'tanggal_masuk',
        'password',
        'role',
        'sisa_cuti',
        'saldo_cuti_tahunan',
        'saldo_cuti_tahun_lalu',
        'saldo_cuti_sakit',
        'saldo_cuti_bersalin',
        'saldo_cuti_penting',
        'saldo_cuti_besar',
        'no_wa',
        'hr_id',
        'ketua_id',
        'pimpinan_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Default value cuti
     */
    protected $attributes = [
        'saldo_cuti_tahunan' => 12,
        'saldo_cuti_sakit' => 0, // unlimited
        'saldo_cuti_bersalin' => 90,
        'saldo_cuti_penting' => 12,
        'saldo_cuti_besar' => 60,
        'sisa_cuti' => 12,
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }
    public function cuti()
    {
        return $this->hasMany(Cuti::class);
    }

    public function hr()
    {
        return $this->belongsTo(User::class, 'hr_id');
    }

    public function ketua()
    {
        return $this->belongsTo(User::class, 'ketua_id');
    }

    public function pimpinan()
    {
        return $this->belongsTo(User::class, 'pimpinan_id');
    }

    /**
     * Get saldo cuti berdasarkan jenis cuti
     */
    public function getSaldoCutiByJenis($jenisCuti)
    {
        $columnMap = [
            'tahunan' => 'saldo_cuti_tahunan',
            'sakit' => 'saldo_cuti_sakit',
            'bersalin' => 'saldo_cuti_bersalin',
            'penting' => 'saldo_cuti_penting',
            'besar' => 'saldo_cuti_besar',
        ];
        
        $column = $columnMap[$jenisCuti] ?? null;
        
        if (!$column) {
            return 0;
        }
        
        return $this->$column ?? 0;
    }

    /**
     * Update saldo cuti berdasarkan jenis cuti
     */
    public function updateSaldoCutiByJenis($jenisCuti, $jumlah)
    {
        $columnMap = [
            'tahunan' => 'saldo_cuti_tahunan',
            'sakit' => 'saldo_cuti_sakit',
            'bersalin' => 'saldo_cuti_bersalin',
            'penting' => 'saldo_cuti_penting',
            'besar' => 'saldo_cuti_besar',
        ];
        
        $column = $columnMap[$jenisCuti] ?? null;
        
        if ($column) {
            $this->$column = $jumlah;
            $this->save();
        }
    }

    /**
     * Kurangi saldo cuti berdasarkan jenis cuti
     */
    public function kurangiSaldoCutiByJenis($jenisCuti, $jumlah)
    {
        $saldoSekarang = $this->getSaldoCutiByJenis($jenisCuti);
        $this->updateSaldoCutiByJenis($jenisCuti, $saldoSekarang - $jumlah);
    }

    /**
     * Kurangi saldo cuti dengan prioritas tahun lalu dulu (untuk cuti tahunan)
     * Jika tahun lalu habis, baru kurangi tahun ini
     */
    public function kurangiSaldoCutiTahunanDenganPrioritasTahunLalu($jumlah)
    {
        $saldoTahunLalu = $this->saldo_cuti_tahun_lalu;
        $saldoTahunIni = $this->saldo_cuti_tahunan;

        if ($saldoTahunLalu >= $jumlah) {
            // Kurangi dari tahun lalu saja
            $this->saldo_cuti_tahun_lalu -= $jumlah;
        } else {
            // Kurangi sisa dari tahun lalu, dan sisanya dari tahun ini
            $sisa = $jumlah - $saldoTahunLalu;
            $this->saldo_cuti_tahun_lalu = 0;
            $this->saldo_cuti_tahunan -= $sisa;
        }

        $this->save();
    }

    /**
     * Tambah saldo cuti berdasarkan jenis cuti
     */
    public function tambahSaldoCutiByJenis($jenisCuti, $jumlah)
    {
        $saldoSekarang = $this->getSaldoCutiByJenis($jenisCuti);
        $this->updateSaldoCutiByJenis($jenisCuti, $saldoSekarang + $jumlah);
    }

}
