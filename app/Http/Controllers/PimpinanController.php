<?php

namespace App\Http\Controllers;

use App\Helpers\WAHelper;
use App\Helpers\FormatHelper;
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

        // Update status
        $cuti->status = 'disetujui_pimpinan';
        $cuti->save();

        // 🔔 Kirim notifikasi ke pegawai (FormatHelper)
        WAHelper::send(
            $cuti->user->no_wa,
            FormatHelper::notifPegawaiApprovedPimpinan($cuti)
        );

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

        // 🔔 Kirim notifikasi ke pegawai
        WAHelper::send(
            $cuti->user->no_wa,
            FormatHelper::notifPegawaiRejected($cuti)
        );

        return back()->with('success', 'Pengajuan cuti berhasil ditolak pimpinan.');
    }
}
