<?php

namespace App\Http\Controllers;

use App\Helpers\WAHelper;
use App\Helpers\FormatHelper;
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

        // 🔔 Notif ke User
        WAHelper::send(
            $cuti->user->no_wa,
            FormatHelper::notifPegawaiApprovedHR($cuti)
        );

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

        // 🔔 Notif Pegawai
        WAHelper::send(
            $cuti->user->no_wa,
            FormatHelper::notifPegawaiRejected($cuti)
        );

        return back()->with('success', 'Cuti ditolak oleh HR.');
    }

    // ===========================
    // NOTIF UNTUK KETUA (HELPER)
    // ===========================
    private function sendToKetuaForPegawai($cuti)
    {
        // ambil ketua sesuai id yang ada di user pegawai
        $ketuaId = $cuti->user->ketua_id;
        if (!$ketuaId) return;

        $ketua = User::find($ketuaId);
        if (!$ketua) return;

        Log::info("[HR -> Ketua] Mengirim WA ke ketua: {$ketua->name}");

        WAHelper::send(
            $ketua->no_wa,
            FormatHelper::notifKetua($cuti)
        );
    }

    // ===========================
    // NOTIF UNTUK PIMPINAN (HELPER) - UNTUK HAKIM
    // ===========================
    private function sendToPimpinanForHakim($cuti)
    {
        // ambil pimpinan sesuai id yang ada di cuti / user
        $pimpinanId = $cuti->user->pimpinan_id;
        if (!$pimpinanId) return;

        $pimpinan = User::find($pimpinanId);
        if (!$pimpinan) return;

        Log::info("[HR -> Pimpinan] Mengirim WA ke pimpinan: {$pimpinan->name}");

        WAHelper::send(
            $pimpinan->no_wa,
            FormatHelper::notifPimpinan($cuti)
        );
    }

    // ===========================
    // NOTIF UNTUK PIMPINAN (HELPER) - EXISTING
    // ===========================

}
