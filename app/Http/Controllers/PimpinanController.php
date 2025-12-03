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
        $dataCuti = Cuti::with('user')
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
        $cuti = Cuti::findOrFail($id);

        if ($cuti->status !== 'disetujui_hr') {
            return back()->with('error', 'Pengajuan sudah diproses sebelumnya.');
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
        $cuti = Cuti::findOrFail($id);

        if ($cuti->status !== 'disetujui_hr') {
            return back()->with('error', 'Pengajuan sudah diproses sebelumnya.');
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
