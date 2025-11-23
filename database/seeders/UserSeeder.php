<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Jalankan seeder untuk membuat user awal.
     */
    public function run(): void
    {
        $users = [
[
    'name' => 'Ikbal',
    'email' => 'ikbal@example.com',
    'password' => Hash::make('password123'),
    'role' => 'hr',
    'saldo_cuti_tahunan' => 12,
    'sisa_cuti' => 12,
    'no_wa' => '6288218996504',
],
[
    'name' => 'admin',
    'email' => 'admin@example.com',
    'password' => Hash::make('admin123'),
    'role' => 'admin',
    'saldo_cuti_tahunan' => 12,
    'sisa_cuti' => 12,
    'no_wa' => '62895343165306',
],
[
    'name' => 'Hilman',
    'email' => 'hilman@example.com',
    'password' => Hash::make('password123'),
    'role' => 'pegawai',
    'saldo_cuti_tahunan' => 12,
    'sisa_cuti' => 12,
    'no_wa' => '6283844452722',
],
[
    'name' => 'oji',
    'email' => 'oji@example.com',
    'password' => Hash::make('password123'),
    'role' => 'pimpinan',
    'saldo_cuti_tahunan' => 12,
    'sisa_cuti' => 12,
    'no_wa' => '6283823043268',
],
        ];

        foreach ($users as &$user) {
            $user['created_at'] = now();
            $user['updated_at'] = now();
        }

        DB::table('users')->insert($users);
    }
}
