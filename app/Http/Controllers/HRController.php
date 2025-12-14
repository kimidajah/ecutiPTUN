<?php

namespace App\Http\Controllers;

use App\Helpers\WAHelper;
use App\Helpers\FormatHelper;
use App\Helpers\WablasService;
use App\Models\Cuti;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class HRController extends Controller
{
    // ===========================
    // Dashboard HR
    // ===========================
    public function index()
    {
        return view('hr.dashboard');
    }

    // ===========================
    // List Permintaan Cuti
    // ===========================
    public function cutiIndex()
    {
        $dataCuti = Cuti::with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('hr.cuti.index', compact('dataCuti'));
    }

    // ===========================
    // Detail Cuti
    // ===========================
    public function cutiShow($id)
    {
        $cuti = Cuti::with('user')->findOrFail($id);
        return view('hr.permintaanCuti.show', compact('cuti'));
    }

    // ===========================
    // APPROVE CUTI (HR)
    // ===========================
    public function cutiApprove(Request $request, $id)
    {
        Log::info("[HR APPROVE] Input diterima", [
            'cuti_id' => $id,
            'input'   => $request->all()
        ]);

        $cuti = Cuti::with('user')->findOrFail($id);

        if ($cuti->status !== 'menunggu') {
            return back()->with('error', 'Pengajuan sudah diproses sebelumnya.');
        }

        // Tentukan flow berdasarkan role user
        $userRole = $cuti->user->role;

        if ($userRole === 'hakim') {
            // Flow untuk Hakim: HR -> Pimpinan
            $cuti->status = 'disetujui_hr';
            $this->sendToPimpinanForHakim($cuti);
        } elseif ($userRole === 'pegawai') {
            // Flow untuk Pegawai: HR -> Ketua -> Pimpinan
            $cuti->status = 'disetujui_hr';
            $this->sendToKetuaForPegawai($cuti);
        }

        $cuti->save();

        Log::info("[HR APPROVE] Status cuti diupdate menjadi disetujui_hr");

        // 🔔 Notif ke User via Wablas
        if ($cuti->user->no_wa) {
            WablasService::sendMessage(
                $cuti->user->no_wa,
                "*✅ Pengajuan Cuti Disetujui HR*\n\n" .
                "Halo " . $cuti->user->name . ",\n\n" .
                "Pengajuan cuti *" . $cuti->jenis_cuti . "* Anda telah disetujui oleh Sub Kepegawaian (HR).\n\n" .
                "📅 Tanggal: " . date('d/m/Y', strtotime($cuti->tanggal_mulai)) . " - " . 
                date('d/m/Y', strtotime($cuti->tanggal_selesai)) . "\n" .
                "⏱️ Durasi: " . $cuti->lama_cuti . " hari\n\n" .
                "Pengajuan Anda sedang dalam tahap review berikutnya.\n\n" .
                "_Sistem e-Cuti PTUN_"
            );
        }

        return back()->with('success', 'Cuti disetujui oleh HR.');
    }

    // ===========================
    // REJECT CUTI (HR)
    // ===========================
    public function cutiReject(Request $request, $id)
    {
        Log::info("[HR REJECT] Input diterima", [
            'cuti_id' => $id,
            'input'   => $request->all()
        ]);

        $cuti = Cuti::with('user')->findOrFail($id);

        if ($cuti->status !== 'menunggu') {
            return back()->with('error', 'Pengajuan sudah diproses sebelumnya.');
        }

        $cuti->status = 'ditolak';
        $cuti->save();

        Log::info("[HR REJECT] Status cuti diupdate menjadi ditolak");

        // 🔔 Notif Pegawai via Wablas
        if ($cuti->user->no_wa) {
            WablasService::sendMessage(
                $cuti->user->no_wa,
                "*❌ Pengajuan Cuti Ditolak*\n\n" .
                "Halo " . $cuti->user->name . ",\n\n" .
                "Maaf, pengajuan cuti *" . $cuti->jenis_cuti . "* Anda telah ditolak oleh Sub Kepegawaian (HR).\n\n" .
                "📅 Tanggal: " . date('d/m/Y', strtotime($cuti->tanggal_mulai)) . " - " . 
                date('d/m/Y', strtotime($cuti->tanggal_selesai)) . "\n" .
                "⏱️ Durasi: " . $cuti->lama_cuti . " hari\n\n" .
                "Silahkan hubungi HR untuk penjelasan lebih lanjut.\n\n" .
                "_Sistem e-Cuti PTUN_"
            );
        }

        return back()->with('success', 'Cuti ditolak oleh HR.');
    }

    // ===========================
    // NOTIF UNTUK KETUA (HELPER)
    // ===========================
    private function sendToKetuaForPegawai($cuti)
    {
        // Ambil atasan yang dipilih HR dari Cuti
        $atasanId = $cuti->atasan_id;
        if (!$atasanId) return;

        $atasan = \App\Models\User::find($atasanId);
        if (!$atasan || !$atasan->no_wa) return;

        Log::info("[HR -> Atasan] Mengirim WA ke atasan: {$atasan->name}");

        WablasService::sendMessage(
            $atasan->no_wa,
            FormatHelper::notifKetua($cuti)
        );
    }

    // ===========================
    // NOTIF UNTUK PIMPINAN (HELPER) - UNTUK HAKIM
    // ===========================
    private function sendToPimpinanForHakim($cuti)
    {
        // Ambil pimpinan yang dipilih HR dari Cuti
        $pimpinanId = $cuti->pimpinan_id;
        if (!$pimpinanId) return;

        $pimpinan = User::find($pimpinanId);
        if (!$pimpinan || !$pimpinan->no_wa) return;

        Log::info("[HR -> Pimpinan] Mengirim WA ke pimpinan: {$pimpinan->name}");

        WablasService::sendMessage(
            $pimpinan->no_wa,
            FormatHelper::notifPimpinan($cuti)
        );
    }

    // ===========================
    // NOTIF UNTUK PIMPINAN (HELPER) - EXISTING
    // ===========================

    // ===========================
    // SET ATASAN DAN PIMPINAN (HR)
    // ===========================
    public function setAtasanPimpinan(Request $request, $id)
    {
        $request->validate([
            'atasan_id' => 'required|exists:users,id',
            'pimpinan_id' => 'required|exists:users,id',
            'kategori_atasan' => 'required|in:PLT,Non-PLT',
            'kategori_pimpinan' => 'required|in:PLT,Non-PLT',
        ]);

        $cuti = Cuti::findOrFail($id);

        // Hanya bisa set atasan/pimpinan saat status menunggu
        if ($cuti->status !== 'menunggu') {
            return back()->with('error', 'Pengajuan sudah diproses.');
        }

        $userRole = $cuti->user->role;

        // Pegawai: perlu atasan dan pimpinan
        if ($userRole === 'pegawai') {
            $cuti->atasan_id = $request->atasan_id;
            $cuti->pimpinan_id = $request->pimpinan_id;
            $cuti->kategori_atasan = $request->kategori_atasan;
            $cuti->kategori_pimpinan = $request->kategori_pimpinan;
        }
        // Hakim: hanya perlu pimpinan
        elseif ($userRole === 'hakim') {
            $cuti->pimpinan_id = $request->pimpinan_id;
            $cuti->kategori_pimpinan = $request->kategori_pimpinan;
        }

        $cuti->save();

        return back()->with('success', 'Atasan dan Pimpinan berhasil dipilih.');
    }

}
