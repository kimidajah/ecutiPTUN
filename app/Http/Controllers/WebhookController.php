<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cuti;
use App\Models\User;
use App\Helpers\WAHelper;
use App\Helpers\FormatHelper;

class WebhookController extends Controller
{
    public function handleWA(Request $request)
    {
        $from    = $request->sender;
        $message = trim($request->text);
        $command = intval($message); // 1 atau 2

        // Cek user pengirim
        $user = User::where('no_wa', $from)->first();
        if (!$user) {
            return response()->json(['status' => 'unknown_number']);
        }

        // Ambil cuti berdasarkan role
        if ($user->role === 'hr') {
            // HR hanya memproses cuti dari pegawai yg dia handle
            $cuti = Cuti::where('hr_id', $user->id)
                ->where('status', 'menunggu')
                ->latest()->first();
        } elseif ($user->role === 'pimpinan') {
            // Pimpinan hanya memproses cuti dari pegawai unitnya
            $cuti = Cuti::where('pimpinan_id', $user->id)
                ->where('status', 'disetujui_hr')
                ->latest()->first();
        } else {
            return response()->json(['status' => 'forbidden_role']);
        }

        if (!$cuti) {
            return response()->json(['status' => 'no_pending_cuti']);
        }

        // Eksekusi perintah
        if ($command === 1) return $this->handleApprove($user, $cuti);
        if ($command === 2) return $this->handleReject($user, $cuti);

        return response()->json(['status' => 'invalid_command']);
    }



    private function handleApprove($user, $cuti)
    {
        // ======================
        // ROLE HR MENYETUJUI
        // ======================
        if ($user->role === 'hr') {

            $cuti->status = 'disetujui_hr';
            $cuti->hr_id = $user->id;  // pastikan tersimpan
            $cuti->save();

            // Notif ke pegawai
            WAHelper::send($cuti->user->no_wa, FormatHelper::notifPegawaiApprovedHR($cuti));

            // Kirim ke pimpinan yg sesuai user
            $this->notifPimpinan($cuti);

            WAHelper::send($user->no_wa, "✅ Anda menyetujui cuti ini.");
            return response()->json(['status' => 'disetujui_hr']);
        }

        // ======================
        // ROLE PIMPINAN MENYETUJUI
        // ======================
        if ($user->role === 'pimpinan') {

            $cuti->status = 'disetujui_pimpinan';
            $cuti->pimpinan_id = $user->id; // pastikan tersimpan
            $cuti->save();

            // Notif ke pegawai
            WAHelper::send($cuti->user->no_wa, FormatHelper::notifPegawaiApprovedPimpinan($cuti));

            WAHelper::send($user->no_wa, "✅ Anda menyetujui cuti ini.");
            return response()->json(['status' => 'disetujui_pimpinan']);
        }
    }



    private function handleReject($user, $cuti)
    {
        $cuti->status = 'ditolak';
        $cuti->save();

        // Notif pegawai
        WAHelper::send($cuti->user->no_wa, FormatHelper::notifPegawaiRejected($cuti));

        WAHelper::send($user->no_wa, "❌ Anda menolak cuti ini.");

        return response()->json(['status' => 'ditolak']);
    }



    // ==========================
    // NOTIF PIMPINAN SESUAI pegawai
    // ==========================
    private function notifPimpinan($cuti)
    {
        $pegawai = $cuti->user;

        // Ambil pimpinan yang benar
        $pimpinan = User::find($pegawai->pimpinan_id);

        if (!$pimpinan) {
            return;
        }

        $pesan = FormatHelper::notifPimpinan($cuti);

        WAHelper::send($pimpinan->no_wa, $pesan);
    }
}
