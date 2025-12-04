<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\HariLibur;
use Illuminate\Support\Facades\Http;

class HariLiburServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Auto-load hari libur data untuk tahun sekarang dan 2 tahun ke depan
        try {
            $tahunSekarang = now()->year;
            
            // Load data untuk 3 tahun (sekarang, +1, +2)
            foreach (range($tahunSekarang, $tahunSekarang + 2) as $tahun) {
                $hariLiburTahun = HariLibur::where('tahun', $tahun)->count();
                
                // Jika belum ada data untuk tahun ini, fetch dari API
                if ($hariLiburTahun === 0) {
                    $this->loadHariLiburFromAPI($tahun);
                }
            }
        } catch (\Exception $e) {
            // Silent fail - jangan sampai error app loading
            \Log::warning('HariLiburServiceProvider: ' . $e->getMessage());
        }
    }
    
    /**
     * Load hari libur dari API untuk tahun tertentu
     */
    private function loadHariLiburFromAPI($year)
    {
        try {
            // Coba API utama
            $response = Http::timeout(5)->get("https://api.iku.gov.id/public/holiday?year=$year");
            
            if ($response->successful()) {
                $data = $response->json();
                if (is_array($data) && !empty($data)) {
                    foreach ($data as $holiday) {
                        $tanggal = $holiday['date'] ?? $holiday['tanggal'] ?? null;
                        $nama = $holiday['name'] ?? $holiday['nama'] ?? $holiday['holiday'] ?? 'Hari Libur';
                        
                        if ($tanggal) {
                            HariLibur::updateOrCreate(
                                ['tanggal' => $tanggal],
                                [
                                    'nama_hari_libur' => $nama,
                                    'keterangan' => $holiday['description'] ?? $holiday['keterangan'] ?? 'Hari Libur Nasional',
                                    'tahun' => $year,
                                ]
                            );
                        }
                    }
                    return;
                }
            }
        } catch (\Exception $e) {
            \Log::warning("API utama gagal untuk tahun $year: " . $e->getMessage());
        }
        
        // Fallback ke API alternatif
        try {
            $response = Http::timeout(5)->get("https://raw.githubusercontent.com/guangrei/hari-libur-indonesia/main/data/$year.json");
            
            if ($response->successful()) {
                $data = $response->json();
                if (is_array($data) && !empty($data)) {
                    foreach ($data as $holiday) {
                        HariLibur::updateOrCreate(
                            ['tanggal' => $holiday['tanggal']],
                            [
                                'nama_hari_libur' => $holiday['keterangan'],
                                'keterangan' => $holiday['keterangan'],
                                'tahun' => $year,
                            ]
                        );
                    }
                    return;
                }
            }
        } catch (\Exception $e) {
            \Log::warning("API alternatif gagal untuk tahun $year: " . $e->getMessage());
        }
        
        // Fallback ke data hardcoded jika API gagal
        $this->loadHariLiburFallback($year);
    }
    
    /**
     * Fallback ke hardcoded data jika API tidak tersedia
     */
    private function loadHariLiburFallback($year)
    {
        // Data hari libur nasional tetap untuk setiap tahun
        $hariLiburTetap = [
            "01-01" => "Tahun Baru",
            "05-01" => "Hari Buruh Internasional",
            "06-01" => "Hari Pancasila",
            "08-17" => "Hari Kemerdekaan Indonesia",
            "12-25" => "Hari Raya Natal",
        ];
        
        // Data hari libur berdasarkan kalender lunar (Hijriah)
        $hariLiburLunar = [
            2020 => [
                "01-24" => "Tahun Baru Hijriah 1441",
                "08-31" => "Mawlid Nabi Muhammad",
                "04-24" => "Isra dan Mi'raj",
                "05-24" => "Hari Raya Idul Fitri",
                "05-25" => "Hari Raya Idul Fitri (Cuti Bersama)",
                "07-30" => "Hari Raya Idul Adha",
                "03-25" => "Hari Raya Nyepi",
            ],
            2021 => [
                "08-09" => "Tahun Baru Hijriah 1443",
                "10-29" => "Mawlid Nabi Muhammad",
                "05-13" => "Isra dan Mi'raj",
                "05-13" => "Hari Raya Idul Fitri",
                "05-14" => "Hari Raya Idul Fitri (Cuti Bersama)",
                "07-20" => "Hari Raya Idul Adha",
                "03-14" => "Hari Raya Nyepi",
            ],
            2022 => [
                "07-30" => "Tahun Baru Hijriah 1444",
                "10-08" => "Mawlid Nabi Muhammad",
                "05-03" => "Isra dan Mi'raj",
                "05-02" => "Hari Raya Idul Fitri",
                "05-03" => "Hari Raya Idul Fitri (Cuti Bersama)",
                "07-09" => "Hari Raya Idul Adha",
                "03-03" => "Hari Raya Nyepi",
            ],
            2023 => [
                "07-19" => "Tahun Baru Hijriah 1445",
                "09-28" => "Mawlid Nabi Muhammad",
                "04-22" => "Isra dan Mi'raj",
                "04-21" => "Hari Raya Idul Fitri",
                "04-22" => "Hari Raya Idul Fitri (Cuti Bersama)",
                "06-29" => "Hari Raya Idul Adha",
                "03-22" => "Hari Raya Nyepi",
            ],
            2024 => [
                "07-07" => "Tahun Baru Hijriah 1446",
                "09-16" => "Mawlid Nabi Muhammad",
                "04-11" => "Isra dan Mi'raj",
                "04-10" => "Hari Raya Idul Fitri",
                "04-11" => "Hari Raya Idul Fitri (Cuti Bersama)",
                "06-17" => "Hari Raya Idul Adha",
                "03-28" => "Hari Raya Nyepi",
            ],
            2025 => [
                "12-30" => "Tahun Baru Hijriah 1447",
                "02-13" => "Mawlid Nabi Muhammad",
                "03-19" => "Isra dan Mi'raj",
                "03-31" => "Hari Raya Idul Fitri",
                "04-01" => "Hari Raya Idul Fitri (Cuti Bersama)",
                "04-10" => "Hari Raya Idul Adha",
                "04-29" => "Hari Raya Nyepi",
            ],
            2026 => [
                "06-27" => "Tahun Baru Hijriah",
                "09-05" => "Mawlid Nabi Muhammad",
                "03-23" => "Hari Raya Idul Fitri",
                "03-24" => "Hari Raya Idul Fitri (Cuti Bersama)",
                "03-31" => "Hari Raya Idul Adha",
                "04-19" => "Hari Raya Nyepi",
            ],
            2027 => [
                "06-16" => "Tahun Baru Hijriah",
                "08-26" => "Mawlid Nabi Muhammad",
                "03-13" => "Hari Raya Idul Fitri",
                "03-14" => "Hari Raya Idul Fitri (Cuti Bersama)",
                "03-21" => "Hari Raya Idul Adha",
                "04-09" => "Hari Raya Nyepi",
            ],
            2028 => [
                "06-04" => "Tahun Baru Hijriah",
                "08-15" => "Mawlid Nabi Muhammad",
                "03-02" => "Hari Raya Idul Fitri",
                "03-03" => "Hari Raya Idul Fitri (Cuti Bersama)",
                "03-09" => "Hari Raya Idul Adha",
                "03-28" => "Hari Raya Nyepi",
            ],
            2029 => [
                "05-25" => "Tahun Baru Hijriah",
                "08-04" => "Mawlid Nabi Muhammad",
                "02-19" => "Hari Raya Idul Fitri",
                "02-20" => "Hari Raya Idul Fitri (Cuti Bersama)",
                "02-27" => "Hari Raya Idul Adha",
                "03-17" => "Hari Raya Nyepi",
            ],
            2030 => [
                "05-15" => "Tahun Baru Hijriah",
                "07-24" => "Mawlid Nabi Muhammad",
                "02-09" => "Hari Raya Idul Fitri",
                "02-10" => "Hari Raya Idul Fitri (Cuti Bersama)",
                "02-17" => "Hari Raya Idul Adha",
                "03-07" => "Hari Raya Nyepi",
            ],
        ];
        
        // Insert hari libur tetap
        foreach ($hariLiburTetap as $tanggal => $nama) {
            $fullTanggal = "$year-$tanggal";
            HariLibur::updateOrCreate(
                ['tanggal' => $fullTanggal],
                [
                    'nama_hari_libur' => $nama,
                    'keterangan' => 'Hari Libur Nasional',
                    'tahun' => $year,
                ]
            );
        }
        
        // Insert hari libur lunar jika ada
        if (isset($hariLiburLunar[$year])) {
            foreach ($hariLiburLunar[$year] as $tanggal => $nama) {
                // Handle bulan Desember tahun sebelumnya
                if (str_starts_with($tanggal, "12-") && str_ends_with($tanggal, "30")) {
                    $fullTanggal = ($year - 1) . "-" . $tanggal;
                } else {
                    $fullTanggal = "$year-$tanggal";
                }
                
                HariLibur::updateOrCreate(
                    ['tanggal' => $fullTanggal],
                    [
                        'nama_hari_libur' => $nama,
                        'keterangan' => 'Hari Libur Nasional',
                        'tahun' => $year,
                    ]
                );
            }
        }
    }
}
