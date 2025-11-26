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

        // Update status
        $cuti->status = 'disetujui_hr';
        $cuti->save();

        Log::info("[HR APPROVE] Status cuti diupdate menjadi disetujui_hr");

        // 🔔 Notif ke Pegawai
        WAHelper::send(
            $cuti->user->no_wa,
            FormatHelper::notifPegawaiApprovedHR($cuti)
        );

        // 🔔 Notif ke Pimpinan
        $this->sendToPimpinan($cuti);

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
    // NOTIF UNTUK PIMPINAN (HELPER)
    // ===========================
    private function sendToPimpinan($cuti)
    {
        $pimpinan = User::where('role', 'pimpinan')->first();
        if (!$pimpinan) return;

        Log::info("[HR -> Pimpinan] Mengirim WA ke pimpinan");

        WAHelper::send(
            $pimpinan->no_wa,
            FormatHelper::notifPimpinan($cuti)
        );
    }
}
