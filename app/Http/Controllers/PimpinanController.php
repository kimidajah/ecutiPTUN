<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cuti;

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
        $dataCuti = Cuti::with('pegawai')
                        ->orderBy('created_at', 'desc')
                        ->get();

        return view('pimpinan.cuti.index', compact('dataCuti'));
    }

    // =========================
    // Detail Pengajuan Cuti
    // =========================
    public function cutiShow($id)
    {
        $cuti = Cuti::with('pegawai')->findOrFail($id);

        return view('pimpinan.cuti.show', compact('cuti'));
    }

    // =========================
    // Approve Cuti
    // =========================
    public function cutiApprove($id)
    {
        $cuti = Cuti::findOrFail($id);

        if ($cuti->status !== 'pending') {
            return back()->with('error', 'Pengajuan sudah diproses sebelumnya.');
        }

        $cuti->status = 'approved';
        $cuti->save();

        return back()->with('success', 'Pengajuan cuti berhasil disetujui.');
    }

    // =========================
    // Reject Cuti
    // =========================
    public function cutiReject($id)
    {
        $cuti = Cuti::findOrFail($id);

        if ($cuti->status !== 'pending') {
            return back()->with('error', 'Pengajuan sudah diproses sebelumnya.');
        }

        $cuti->status = 'rejected';
        $cuti->save();

        return back()->with('success', 'Pengajuan cuti berhasil ditolak.');
    }

    // =========================
    // Halaman tambahan (opsional)
    // =========================
    public function aturanCuti()
    {
        return view('pimpinan.aturan-cuti');
    }

    public function userKaryawan()
    {
        return view('pimpinan.user-karyawan');
    }
}
