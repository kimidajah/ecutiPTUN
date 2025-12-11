<?php

namespace App\Http\Controllers;

use App\Helpers\WAHelper;
use App\Helpers\FormatHelper;
use App\Helpers\WablasService;
use App\Models\Cuti;
use Illuminate\Http\Request;

class PimpinanController extends Controller
{
    // =========================
    // Dashboard
    // =========================
    public function dashboard()
    {
        return view('pimpinan.dashboard');
    }

    // =========================
    // Daftar Pengajuan Cuti
    // =========================
    public function cutiIndex()
    {
        // Tampilkan cuti yang sudah disetujui Ketua (pegawai) atau disetujui HR (hakim yang bypass ketua)
        $dataCuti = Cuti::with('user')
            ->where(function ($query) {
                // Pegawai cuti yang sudah disetujui Ketua
                $query->where('status', 'disetujui_ketua');
            })
            ->orWhere(function ($query) {
                // Hakim cuti yang disetujui HR (bypass ketua, langsung ke pimpinan)
                $query->where('status', 'disetujui_hr')
                    ->whereHas('user', function ($u) {
                        $u->where('role', 'hakim');
                    });
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return view('pimpinan.cuti.index', compact('dataCuti'));
    }

    // =========================
    // Detail Cuti
    // =========================
    public function cutiShow($id)
    {
        $cuti = Cuti::with('user')->findOrFail($id);

        return view('pimpinan.cuti.show', compact('cuti'));
    }

    // =========================
    // APPROVE CUTI OLEH PIMPINAN
    // =========================
    public function cutiApprove($id)
    {
        $cuti = Cuti::with('user')->findOrFail($id);

        // Cek apakah status sudah siap untuk disetujui pimpinan
        // Pegawai: harus disetujui_ketua, Hakim: harus disetujui_hr
        if ($cuti->user->role === 'hakim') {
            if ($cuti->status !== 'disetujui_hr') {
                return back()->with('error', 'Pengajuan hakim sudah diproses sebelumnya.');
            }
        } elseif ($cuti->user->role === 'pegawai') {
            if ($cuti->status !== 'disetujui_ketua') {
                return back()->with('error', 'Pengajuan pegawai belum disetujui ketua divisi.');
            }
        } else {
            return back()->with('error', 'Role pengguna tidak valid.');
        }

        // KURANGI SALDO CUTI sekarang (saat disetujui pimpinan)
        $user = $cuti->user;
        $jenisCuti = $cuti->jenis_cuti;
        $lamaCuti = $cuti->lama_cuti;

        // Kurangi saldo cuti (kecuali cuti sakit yang unlimited)
        if (!\App\Models\PengaturanCuti::isUnlimited($jenisCuti)) {
            if ($jenisCuti === 'tahunan') {
                $user->kurangiSaldoCutiTahunanDenganPrioritasTahunLalu($lamaCuti);
            } else {
                $user->kurangiSaldoCutiByJenis($jenisCuti, $lamaCuti);
            }
        }

        // Update status
        $cuti->status = 'disetujui_pimpinan';
        $cuti->save();

        // 🔔 Kirim notifikasi via Wablas
        if ($cuti->user->no_wa) {
            WablasService::sendMessage(
                $cuti->user->no_wa,
                "*✅ Pengajuan Cuti Disetujui*\n\n" .
                "Halo " . $cuti->user->name . ",\n\n" .
                "Pengajuan cuti *" . $cuti->jenis_cuti . "* Anda telah disetujui oleh Pimpinan.\n\n" .
                "📅 Tanggal: " . date('d/m/Y', strtotime($cuti->tanggal_mulai)) . " - " . 
                date('d/m/Y', strtotime($cuti->tanggal_selesai)) . "\n" .
                "⏱️ Durasi: " . $cuti->lama_cuti . " hari\n\n" .
                "Selamat menikmati cuti Anda!\n\n" .
                "_Sistem e-Cuti PTUN_"
            );
        }

        return back()->with('success', 'Pengajuan cuti berhasil disetujui pimpinan.');
    }

    // =========================
    // REJECT CUTI OLEH PIMPINAN
    // =========================
    public function cutiReject($id)
    {
        $cuti = Cuti::with('user')->findOrFail($id);

        // Cek apakah status sudah siap untuk ditolak pimpinan
        // Pegawai: harus disetujui_ketua, Hakim: harus disetujui_hr
        if ($cuti->user->role === 'hakim') {
            if ($cuti->status !== 'disetujui_hr') {
                return back()->with('error', 'Pengajuan hakim sudah diproses sebelumnya.');
            }
        } elseif ($cuti->user->role === 'pegawai') {
            if ($cuti->status !== 'disetujui_ketua') {
                return back()->with('error', 'Pengajuan pegawai belum disetujui ketua divisi.');
            }
        } else {
            return back()->with('error', 'Role pengguna tidak valid.');
        }

        $cuti->status = 'ditolak';
        $cuti->save();

        // 🔔 Kirim notifikasi via Wablas
        if ($cuti->user->no_wa) {
            WablasService::sendMessage(
                $cuti->user->no_wa,
                "*❌ Pengajuan Cuti Ditolak*\n\n" .
                "Halo " . $cuti->user->name . ",\n\n" .
                "Maaf, pengajuan cuti *" . $cuti->jenis_cuti . "* Anda telah ditolak oleh Pimpinan.\n\n" .
                "📅 Tanggal: " . date('d/m/Y', strtotime($cuti->tanggal_mulai)) . " - " . 
                date('d/m/Y', strtotime($cuti->tanggal_selesai)) . "\n" .
                "⏱️ Durasi: " . $cuti->lama_cuti . " hari\n\n" .
                "Silahkan hubungi HR untuk keterangan lebih lanjut.\n\n" .
                "_Sistem e-Cuti PTUN_"
            );
        }

        return back()->with('success', 'Pengajuan cuti berhasil ditolak pimpinan.');
    }

    /**
     * Helper untuk restore saldo cuti tahunan (menambah kembali)
     * Restore ke tahun ini dulu, jika penuh baru ke tahun lalu
     */
    private function restoreSaldoCutiTahunan(\App\Models\User $user, $jumlah)
    {
        $kapasitasTahunIni = 12; // Default kapasitas tahunan
        $spaceTahunIni = $kapasitasTahunIni - $user->saldo_cuti_tahunan;

        if ($spaceTahunIni >= $jumlah) {
            // Tambah ke tahun ini saja
            $user->saldo_cuti_tahunan += $jumlah;
        } else {
            // Isi tahun ini penuh, sisanya ke tahun lalu
            $user->saldo_cuti_tahunan = $kapasitasTahunIni;
            $sisa = $jumlah - $spaceTahunIni;
            $user->saldo_cuti_tahun_lalu += $sisa;
        }

        $user->save();
    }
}
