<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Jalankan seeder.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'name' => 'Arkaan',
                'email' => 'arkaan@example.com',
                'password' => Hash::make('password123'),
                'role' => 'pegawai',
                'sisa_cuti' => 12,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Hilman',
                'email' => 'hilman@example.com',
                'password' => Hash::make('password123'),
                'role' => 'hr',
                'sisa_cuti' => 12,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Syamil',
                'email' => 'syamil@example.com',
                'password' => Hash::make('password123'),
                'role' => 'pimpinan',
                'sisa_cuti' => 12,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Admin',
                'email' => 'admin@example.com',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'sisa_cuti' => 12,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
