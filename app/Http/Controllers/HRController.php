<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Cuti;
use App\Models\Pegawai;
use Illuminate\Http\Request;

class HRController extends Controller
{


    // ===========================
    // HOME HR (opsional)
    // ===========================
    public function index()
    {
        return view('hr.dashboard');
    }

    // ===========================
    // PERMINTAAN CUTI - INDEX
    // ===========================
    public function cutiIndex()
    {
        $dataCuti = Cuti::with('pegawai')
                        ->orderBy('created_at', 'desc')
                        ->get();

        return view('hr.cuti.index', compact('dataCuti'));
    }

    // ===========================
    // PERMINTAAN CUTI - SHOW
    // ===========================
    public function cutiShow($id)
    {
        $cuti = Cuti::with('pegawai')->findOrFail($id);

        return view('hr.permintaanCuti.show', compact('cuti'));
    }

    // ===========================
    // APPROVE CUTI
    // ===========================
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

    // ===========================
    // REJECT CUTI
    // ===========================
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
}
