<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PengaturanCuti;

class PengaturanCutiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Cuti Tahunan - 12 hari per tahun
        PengaturanCuti::create([
            'nama_pengaturan' => 'Cuti Tahunan',
            'jenis_cuti' => 'tahunan',
            'jumlah_cuti_per_tahun' => 12,
            'keterangan' => 'Cuti tahunan sebanyak 12 hari kerja per tahun untuk semua pegawai',
        ]);

        // Cuti Sakit - unlimited (sesuai surat dokter)
        PengaturanCuti::create([
            'nama_pengaturan' => 'Cuti Sakit',
            'jenis_cuti' => 'sakit',
            'jumlah_cuti_per_tahun' => 0, // 0 = unlimited
            'keterangan' => 'Cuti sakit tidak dibatasi, harus disertai surat keterangan dokter',
        ]);

        // Cuti Bersalin - 3 bulan (90 hari)
        PengaturanCuti::create([
            'nama_pengaturan' => 'Cuti Bersalin',
            'jenis_cuti' => 'bersalin',
            'jumlah_cuti_per_tahun' => 90,
            'keterangan' => 'Cuti bersalin selama 3 bulan (90 hari) untuk pegawai perempuan',
        ]);

        // Cuti Penting - 12 hari
        PengaturanCuti::create([
            'nama_pengaturan' => 'Cuti Penting',
            'jenis_cuti' => 'penting',
            'jumlah_cuti_per_tahun' => 12,
            'keterangan' => 'Cuti untuk keperluan penting seperti pernikahan, kematian keluarga, dll',
        ]);

        // Cuti Besar - 2 bulan (60 hari)
        PengaturanCuti::create([
            'nama_pengaturan' => 'Cuti Besar',
            'jenis_cuti' => 'besar',
            'jumlah_cuti_per_tahun' => 60,
            'keterangan' => 'Cuti besar selama 2 bulan (60 hari) setelah masa kerja tertentu',
        ]);
    }
}
