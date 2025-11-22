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
    // Total semua pengajuan cuti
    $totalCuti = Cuti::count();

    // Pending = yang sudah disetujui HR tapi belum diproses pimpinan
    $pendingCuti = Cuti::where('status', 'disetujui_hr')->count();

    // Disetujui = cuti yang sudah disetujui pimpinan
    $approvedCuti = Cuti::where('status', 'disetujui_pimpinan')->count();

    // Ditolak = cuti yang ditolak pimpinan
    $rejectedCuti = Cuti::where('status', 'ditolak')->count();

    // alasan
    $alasan = Cuti::select('alasan')->distinct()->get();

    // Kirim data ke view
    return view('pimpinan.dashboard', compact(
        'totalCuti',
        'pendingCuti',
        'approvedCuti',
        'rejectedCuti'
    ));
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
    // Detail Pengajuan Cuti
    // =========================
    public function cutiShow($id)
    {
        $cuti = Cuti::with('user')->findOrFail($id);

        return view('pimpinan.cuti.show', compact('cuti'));
    }

    // =========================
    // Approve Cuti
    // =========================
    public function cutiApprove($id)
    {
        $cuti = Cuti::findOrFail($id);

        if ($cuti->status !== 'disetujui_hr') {
            return back()->with('error', 'Pengajuan sudah diproses sebelumnya.');
        }

        $cuti->status = 'disetujui_pimpinan';
        $cuti->save();

        return back()->with('success', 'Pengajuan cuti berhasil disetujui.');
    }

    // =========================
    // Reject Cuti
    // =========================
    public function cutiReject($id)
    {
        $cuti = Cuti::findOrFail($id);

        if ($cuti->status !== 'disetujui_hr') {
            return back()->with('error', 'Pengajuan sudah diproses sebelumnya.');
        }

        $cuti->status = 'ditolak';
        $cuti->save();

        return back()->with('success', 'Pengajuan cuti berhasil ditolak.');
    }
}
