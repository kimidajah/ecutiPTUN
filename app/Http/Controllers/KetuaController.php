<?php

namespace App\Http\Controllers;

use App\Helpers\WAHelper;
use App\Helpers\FormatHelper;
use App\Models\Cuti;
use Illuminate\Http\Request;

class KetuaController extends Controller
{
    // =========================
    // Dashboard
    // =========================
    public function dashboard()
    {
        return view('ketua.dashboard');
    }

    // =========================
    // Daftar Pengajuan Cuti
    // =========================
    public function cutiIndex()
    {
        // Tampilkan hanya cuti yang sudah disetujui Sub Kepegawaian dan menunggu approval ketua divisi
        $dataCuti = Cuti::with('user')
            ->where('status', 'disetujui_hr')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('ketua.cuti.index', compact('dataCuti'));
    }

    // =========================
    // Detail Cuti
    // =========================
    public function cutiShow($id)
    {
        $cuti = Cuti::with('user')->findOrFail($id);

        return view('ketua.cuti.show', compact('cuti'));
    }

    // =========================
    // APPROVE CUTI OLEH KETUA
    // =========================
    public function cutiApprove(Request $request, $id)
    {
        $cuti = Cuti::with('user')->findOrFail($id);

        if ($cuti->status !== 'disetujui_hr') {
            return back()->with('error', 'Pengajuan sudah diproses sebelumnya.');
        }

        // Update status
        $cuti->status = 'disetujui_ketua';
        $cuti->catatan_ketua = $request->input('catatan_ketua');
        $cuti->save();

        // 🔔 Kirim notifikasi ke pegawai
        WAHelper::send(
            $cuti->user->no_wa,
            FormatHelper::notifPegawaiApprovedKetua($cuti)
        );

        // 🔔 Kirim notifikasi ke pimpinan
        $pimpinanId = $cuti->user->pimpinan_id;
        if ($pimpinanId) {
            $pimpinan = \App\Models\User::find($pimpinanId);
            if ($pimpinan) {
                WAHelper::send(
                    $pimpinan->no_wa,
                    FormatHelper::notifPimpinan($cuti)
                );
            }
        }

        return back()->with('success', 'Pengajuan cuti berhasil disetujui ketua divisi.');
    }

    // =========================
    // REJECT CUTI OLEH KETUA
    // =========================
    public function cutiReject(Request $request, $id)
    {
        $cuti = Cuti::findOrFail($id);

        if ($cuti->status !== 'disetujui_hr') {
            return back()->with('error', 'Pengajuan sudah diproses sebelumnya.');
        }

        $cuti->status = 'ditolak';
        $cuti->catatan_ketua = $request->input('catatan_ketua');
        $cuti->save();

        // 🔔 Kirim notifikasi ke pegawai
        WAHelper::send(
            $cuti->user->no_wa,
            FormatHelper::notifPegawaiRejected($cuti)
        );

        return back()->with('success', 'Pengajuan cuti berhasil ditolak ketua divisi.');
    }
}
