<?php

namespace App\Http\Controllers;

use App\Helpers\WAHelper;
use App\Helpers\FormatHelper;
use App\Helpers\WablasService;
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

        // 🔔 Kirim notifikasi ke pegawai via Wablas
        if ($cuti->user->no_wa) {
            WablasService::sendMessage(
                $cuti->user->no_wa,
                "*✅ Pengajuan Cuti Disetujui Atasan*\n\n" .
                "Halo " . $cuti->user->name . ",\n\n" .
                "Pengajuan cuti *" . $cuti->jenis_cuti . "* Anda telah disetujui oleh Atasan Langsung.\n\n" .
                "📅 Tanggal: " . date('d/m/Y', strtotime($cuti->tanggal_mulai)) . " - " . 
                date('d/m/Y', strtotime($cuti->tanggal_selesai)) . "\n" .
                "⏱️ Durasi: " . $cuti->lama_cuti . " hari\n\n" .
                "Pengajuan Anda sedang dalam tahap review Pimpinan.\n\n" .
                "_Sistem e-Cuti PTUN_"
            );
        }

        // 🔔 Kirim notifikasi ke pimpinan yang dipilih HR
        $pimpinanId = $cuti->pimpinan_id;
        if ($pimpinanId) {
            $pimpinan = \App\Models\User::find($pimpinanId);
            if ($pimpinan && $pimpinan->no_wa) {
                WablasService::sendMessage(
                    $pimpinan->no_wa,
                    FormatHelper::notifPimpinan($cuti)
                );
            }
        }

        return back()->with('success', 'Pengajuan cuti berhasil disetujui atasan.');
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

        // 🔔 Kirim notifikasi ke pegawai via Wablas
        if ($cuti->user->no_wa) {
            WablasService::sendMessage(
                $cuti->user->no_wa,
                "*❌ Pengajuan Cuti Ditolak*\n\n" .
                "Halo " . $cuti->user->name . ",\n\n" .
                "Maaf, pengajuan cuti *" . $cuti->jenis_cuti . "* Anda telah ditolak oleh Ketua Divisi.\n\n" .
                "📅 Tanggal: " . date('d/m/Y', strtotime($cuti->tanggal_mulai)) . " - " . 
                date('d/m/Y', strtotime($cuti->tanggal_selesai)) . "\n" .
                "⏱️ Durasi: " . $cuti->lama_cuti . " hari\n\n" .
                "Silahkan hubungi Ketua Divisi atau HR untuk penjelasan lebih lanjut.\n\n" .
                "_Sistem e-Cuti PTUN_"
            );
        }

        return back()->with('success', 'Pengajuan cuti berhasil ditolak ketua divisi.');
    }
}
