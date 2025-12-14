<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\PengaturanCuti;
use Carbon\Carbon;

class ResetCutiTahunan extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cuti:reset-tahunan';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset saldo semua jenis cuti untuk semua user setiap tahun';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Get all users kecuali admin
        $users = User::whereIn('role', ['pegawai', 'hakim', 'sub_kepegawaian', 'ketua', 'pimpinan'])->get();
        
        $updated = 0;
        foreach ($users as $user) {
            // Hitung saldo cuti berdasarkan tanggal masuk
            $tanggalMasuk = $user->tanggal_masuk ? Carbon::parse($user->tanggal_masuk) : now()->subYears(2);
            $lamaKerja = $tanggalMasuk->diffInMonths(now());
            
            // Jika sudah lebih dari 1 tahun, dapat full cuti
            $penuhCuti = $lamaKerja >= 12;
            
            // Reset Cuti Tahunan
            $jumlahTahunan = PengaturanCuti::getJumlahCutiByJenis('tahunan');
            $user->saldo_cuti_tahunan = $penuhCuti ? $jumlahTahunan : floor(($lamaKerja / 12) * $jumlahTahunan);
            
            // Reset Cuti Sakit (14 hari)
            $jumlahSakit = PengaturanCuti::getJumlahCutiByJenis('sakit');
            $user->saldo_cuti_sakit = $jumlahSakit;
            
            // Reset Cuti Bersalin (90 hari untuk perempuan)
            $jumlahBersalin = PengaturanCuti::getJumlahCutiByJenis('bersalin');
            $user->saldo_cuti_bersalin = $jumlahBersalin;
            
            // Reset Cuti Penting (12 hari)
            $jumlahPenting = PengaturanCuti::getJumlahCutiByJenis('penting');
            $user->saldo_cuti_penting = $penuhCuti ? $jumlahPenting : floor(($lamaKerja / 12) * $jumlahPenting);
            
            // Reset Cuti Besar (60 hari)
            $jumlahBesar = PengaturanCuti::getJumlahCutiByJenis('besar');
            $user->saldo_cuti_besar = $jumlahBesar;
            
            // Update sisa_cuti (backward compatibility)
            $user->sisa_cuti = $user->saldo_cuti_tahunan;
            
            $user->save();
            $updated++;
        }
        
        $this->info("Berhasil reset saldo cuti untuk {$updated} user.");
        $this->info("- Cuti Tahunan: " . PengaturanCuti::getJumlahCutiByJenis('tahunan') . " hari");
        $this->info("- Cuti Sakit: 14 hari (dengan surat dokter)");
        $this->info("- Cuti Bersalin: " . PengaturanCuti::getJumlahCutiByJenis('bersalin') . " hari");
        $this->info("- Cuti Penting: " . PengaturanCuti::getJumlahCutiByJenis('penting') . " hari");
        $this->info("- Cuti Besar: " . PengaturanCuti::getJumlahCutiByJenis('besar') . " hari");
        
        return Command::SUCCESS;
    }
}
