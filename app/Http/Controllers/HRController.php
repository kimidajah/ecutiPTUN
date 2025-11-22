<?php

namespace App\Http\Controllers;

use App\Helpers\WhatsappHelper;
use App\Http\Controllers\Controller;
use App\Models\Cuti;
use App\Models\User;
use Illuminate\Http\Request;

class HRController extends Controller
{
    // ===========================
    // DASHBOARD HR
    // ===========================
    public function index()
    {
        return view('hr.dashboard');
    }

    // ===========================
    // LIST PERMINTAAN CUTI
    // ===========================
    public function cutiIndex()
    {
        $dataCuti = Cuti::with('user')
                        ->orderBy('created_at', 'desc')
                        ->get();

        return view('hr.cuti.index', compact('dataCuti'));
    }

    // ===========================
    // DETAIL CUTI
    // ===========================
    public function cutiShow($id)
    {
        $cuti = Cuti::with('user')->findOrFail($id);
        return view('hr.permintaanCuti.show', compact('cuti'));
    }

    // ===========================
    // APPROVE CUTI (HR)
    // ===========================
public function cutiApprove($id)
{
    $cuti = Cuti::with('user')->findOrFail($id);

    if ($cuti->status !== 'menunggu') {
        return back()->with('error', 'Pengajuan sudah diproses sebelumnya.');
    }

    // Update status HR
    $cuti->status = 'disetujui_hr';
    $cuti->save();

    // 🔔 Notif ke pegawai
    $this->sendWA(
        $cuti->user->no_wa,
        "Pengajuan cuti Anda telah disetujui HR dan menunggu persetujuan pimpinan."
    );

    // 🔔 Notif ke pimpinan
    $this->notifPimpinan($cuti);

    return back()->with('success', 'Cuti berhasil disetujui oleh HR.');
}


    // ====================================
    // NOTIF PIMPINAN
    // ====================================
private function notifPimpinan($cuti)
{
    $pimpinan = User::where('role', 'pimpinan')->first();
    if (!$pimpinan) return;

    $pesan =
        "📢 *Pengajuan Cuti Menunggu Persetujuan*\n" .
        "Pegawai: {$cuti->user->name}\n" .
        "Jenis: {$cuti->jenis_cuti}\n" .
        "Tanggal: {$cuti->tanggal_mulai} s/d {$cuti->tanggal_selesai}\n\n" .
        "Balas *1* untuk SETUJU\n" .
        "Balas *2* untuk TOLAK";

    $this->sendWA($pimpinan->no_wa, $pesan);
}


    // ===========================
    // REJECT CUTI (HR)
    // ===========================
    public function cutiReject($id)
    {
        $cuti = Cuti::findOrFail($id);

        if ($cuti->status !== 'menunggu') {
            return back()->with('error', 'Pengajuan sudah diproses sebelumnya.');
        }

        $cuti->status = 'ditolak';
        $cuti->save();

        return back()->with('success', 'Cuti telah ditolak oleh HR.');
    }

    // ===========================
    // FUNGSI SEND WA
    // ===========================
    private function sendWA($target, $message)
    {
        $url = env('WA_API_URL');
        $token = env('WA_API_TOKEN');

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => [
                'target' => $target,
                'message' => $message,
            ],
            CURLOPT_HTTPHEADER => [
                "Authorization: $token"
            ]
        ]);

        $response = curl_exec($curl);
        curl_close($curl);

        return $response;
    }


// public function setujui($id)
// {
//     $cuti = Cuti::findOrFail($id);
//     $cuti->status = 'Disetujui HR';
//     $cuti->save();

//     // === Kirim notif WA ke Pimpinan ===
//     $pimpinanWa = env('PIMPINAN_WA');

//     $message = "PERMOHONAN CUTI hrcontroller\n".
//                "-----------------------------\n".
//                "Nama: {$cuti->user->name}\n".
//                "Jenis Cuti: {$cuti->jenis_cuti}\n".
//                "Tanggal: {$cuti->tanggal_mulai} s/d {$cuti->tanggal_selesai}\n".
//                "Status: Disetujui HR\n\n".
//                "Mohon persetujuan lebih lanjut.";

//     WhatsappHelper::send($pimpinanWa, $message);

//     return back()->with('success', 'Cuti berhasil disetujui dan notifikasi WA terkirim ke pimpinan.');
// }


}
