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
        // Gunakan transaction supaya konsisten.
        DB::transaction(function () {
            $now = now();

            // Helper: insert new user atau update jika email sudah ada, lalu kembalikan id.
            $getOrCreateUserId = function (array $data) use ($now) {
                $data['created_at'] = $now;
                $data['updated_at'] = $now;

                $existing = DB::table('users')->where('email', $data['email'])->first();

                if ($existing) {
                    // Update supaya seeded data tetap sinkron (mis. password berubah saat pengembangan)
                    DB::table('users')->where('id', $existing->id)->update($data);
                    return $existing->id;
                }

                return DB::table('users')->insertGetId($data);
            };

            // Masukkan user yang akan menjadi referensi (admin, pimpinan, hr)
            $adminId = $getOrCreateUserId([
                'name' => 'Arkaan',
                'email' => 'arkaan@example.com',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'saldo_cuti_tahunan' => 12,
                'sisa_cuti' => 12,
                'no_wa' => '62895343165306',
            ]);

            $pimpinanId = $getOrCreateUserId([
                'name' => 'Syamil',
                'email' => 'syamil@example.com',
                'password' => Hash::make('pimpinan123'),
                'role' => 'pimpinan',
                'saldo_cuti_tahunan' => 12,
                'sisa_cuti' => 12,
                'no_wa' => '628957072604',
            ]);

            $hrId = $getOrCreateUserId([
                'name' => 'Andi',
                'email' => 'andi@example.com',
                'password' => Hash::make('pegawai123'),
                'role' => 'hr',
                'saldo_cuti_tahunan' => 12,
                'sisa_cuti' => 12,
                'no_wa' => '62895343165306',
            ]);

            // Sekarang masukkan pegawai (Hilman) dan pasang hr_id & pimpinan_id sesuai permintaan.
            $hilmanData = [
                'name' => 'Hilman',
                'email' => 'hilman@example.com',
                'password' => Hash::make('hr123'),
                'role' => 'pegawai',
                'saldo_cuti_tahunan' => 12,
                'sisa_cuti' => 12,
                'no_wa' => '6283844452722',
                'hr_id' => $hrId,          // Andi
                'pimpinan_id' => $pimpinanId, // Syamil
                'created_at' => $now,
                'updated_at' => $now,
            ];

            $existingHilman = DB::table('users')->where('email', $hilmanData['email'])->first();
            if ($existingHilman) {
                DB::table('users')->where('id', $existingHilman->id)->update($hilmanData);
            } else {
                DB::table('users')->insert($hilmanData);
            }

            // (Optional) Jika mau memasukkan user lain sekaligus, tambahkan di sini.
        });
    }
}
