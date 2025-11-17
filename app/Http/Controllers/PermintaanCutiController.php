<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cuti;
use App\Models\Pegawai;

class PermintaanCutiController extends Controller
{
    /**
     * Menampilkan daftar pengajuan cuti untuk HR
     */
    public function index()
    {
        $dataCuti = Cuti::with('pegawai')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('hr.permintaanCuti.index', compact('dataCuti'));
    }

    /**
     * Menampilkan detail pengajuan cuti
     */
    public function show($id)
    {
        $cuti = Cuti::with('pegawai')->findOrFail($id);

        return view('hr.permintaanCuti.show', compact('cuti'));
    }

    /**
     * Menyetujui permintaan cuti
     */
    public function approve($id)
    {
        $cuti = Cuti::findOrFail($id);

        // Tidak boleh approve jika sudah bukan pending
        if ($cuti->status !== 'pending') {
            return back()->with('error', 'Pengajuan sudah diproses sebelumnya.');
        }

        $cuti->status = 'approved';
        $cuti->save();

        return back()->with('success', 'Pengajuan cuti berhasil disetujui.');
    }

    /**
     * Menolak permintaan cuti
     */
    public function reject($id)
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
