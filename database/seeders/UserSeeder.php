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
                'name' => 'Arkaan',
                'email' => 'arkaan@example.com',
                'password' => Hash::make('password123'),
                'role' => 'pegawai',
                'saldo_cuti_tahunan' => 12,
                'sisa_cuti' => 12,
            ],
            [
                'name' => 'Hilman',
                'email' => 'hilman@example.com',
                'password' => Hash::make('password123'),
                'role' => 'hr',
                'saldo_cuti_tahunan' => 12,
                'sisa_cuti' => 12,
            ],
            [
                'name' => 'Syamil',
                'email' => 'syamil@example.com',
                'password' => Hash::make('password123'),
                'role' => 'pimpinan',
                'saldo_cuti_tahunan' => 12,
                'sisa_cuti' => 12,
            ],
            [
                'name' => 'Admin',
                'email' => 'admin@example.com',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'saldo_cuti_tahunan' => 12,
                'sisa_cuti' => 12,
            ],
        ];

        foreach ($users as &$user) {
            $user['created_at'] = now();
            $user['updated_at'] = now();
        }

        DB::table('users')->insert($users);
    }
}
