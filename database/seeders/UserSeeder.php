<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Jalankan seeder untuk membuat user awal.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Arkaan',
            'email' => 'arkaan@example.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'tanggal_masuk' => '2022-01-15',
            'saldo_cuti_tahunan' => 12,
            'saldo_cuti_sakit' => 0,
            'saldo_cuti_bersalin' => 90,
            'saldo_cuti_penting' => 12,
            'saldo_cuti_besar' => 60,
            'no_wa' => '62895343165306',
        ]);

        User::create([
            'name' => 'Hilman',
            'email' => 'hilman@example.com',
            'password' => Hash::make('hr123'),
            'role' => 'sub_kepegawaian',
            'tanggal_masuk' => '2021-06-01',
            'saldo_cuti_tahunan' => 12,
            'saldo_cuti_sakit' => 0,
            'saldo_cuti_bersalin' => 90,
            'saldo_cuti_penting' => 12,
            'saldo_cuti_besar' => 60,
            'no_wa' => '6283844452722',
        ]);

        User::create([
            'name' => 'Syamil',
            'email' => 'syamil@example.com',
            'password' => Hash::make('pimpinan123'),
            'role' => 'pimpinan',
            'tanggal_masuk' => '2020-03-10',
            'saldo_cuti_tahunan' => 12,
            'saldo_cuti_sakit' => 0,
            'saldo_cuti_bersalin' => 90,
            'saldo_cuti_penting' => 12,
            'saldo_cuti_besar' => 60,
            'no_wa' => '628957072604',
        ]);

        User::create([
            'name' => 'Andi',
            'email' => 'andi@example.com',
            'password' => Hash::make('pegawai123'),
            'role' => 'pegawai',
            'tanggal_masuk' => '2022-08-20',
            'saldo_cuti_tahunan' => 12,
            'saldo_cuti_sakit' => 0,
            'saldo_cuti_bersalin' => 90,
            'saldo_cuti_penting' => 12,
            'saldo_cuti_besar' => 60,
            'no_wa' => '62895343165306',
        ]);

        User::create([
            'name' => 'Hakim Utama',
            'email' => 'hakim@ptun.go.id',
            'password' => Hash::make('password123'),
            'role' => 'hakim',
            'nip' => '197512081998021001',
            'jabatan' => 'Hakim',
            'gol_ruang' => 'IV/b',
            'tanggal_masuk' => '1998-02-10',
            'saldo_cuti_tahunan' => 12,
            'saldo_cuti_sakit' => 0,
            'saldo_cuti_bersalin' => 90,
            'saldo_cuti_penting' => 12,
            'saldo_cuti_besar' => 60,
            'no_wa' => '081234567890',
        ]);

        User::create([
            'name' => 'Ketua Pengadilan',
            'email' => 'ketua@ptun.go.id',
            'password' => Hash::make('password123'),
            'role' => 'ketua',
            'nip' => '196705152000121001',
            'jabatan' => 'Ketua',
            'gol_ruang' => 'IV/c',
            'tanggal_masuk' => '2000-12-15',
            'saldo_cuti_tahunan' => 12,
            'saldo_cuti_sakit' => 0,
            'saldo_cuti_bersalin' => 90,
            'saldo_cuti_penting' => 12,
            'saldo_cuti_besar' => 60,
            'no_wa' => '081234567891',
        ]);
    }
}
