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
        $command = intval($message); // string "1" / "2" atau int 1/2 sama saja

        $user = User::where('no_wa', $from)->first();
        if (!$user) {
            return response()->json(['status' => 'unknown_number']);
        }

        if ($user->role === 'hr') {
            $cuti = Cuti::where('status', 'menunggu')->latest()->first();
        } elseif ($user->role === 'pimpinan') {
            $cuti = Cuti::where('status', 'disetujui_hr')->latest()->first();
        } else {
            return response()->json(['status' => 'forbidden_role']);
        }

        if (!$cuti) {
            return response()->json(['status' => 'no_pending_cuti']);
        }

        // ==========================
        // SETUJU (1)
        // ==========================
        if ($command === 1) {
            return $this->handleApprove($user, $cuti);
        }

        // ==========================
        // TOLAK (2)
        // ==========================
        if ($command === 2) {
            return $this->handleReject($user, $cuti);
        }

        return response()->json(['status' => 'invalid_command']);
    }

    private function handleApprove($user, $cuti)
    {
        if ($user->role === 'hr') {
            $cuti->status = 'disetujui_hr';
            $cuti->save();

            // Notif Pegawai
            WAHelper::send($cuti->user->no_wa, FormatHelper::notifPegawaiApproved($cuti));

            // Notif Pimpinan
            $this->notifPimpinan($cuti);

            // Feedback ke HR sendiri
            WAHelper::send($user->no_wa, "✅ Anda menyetujui cuti ini.");

            return response()->json(['status' => 'disetujui_hr']);
        }

        if ($user->role === 'pimpinan') {
            $cuti->status = 'disetujui_pimpinan';
            $cuti->save();

            WAHelper::send($cuti->user->no_wa, FormatHelper::notifPegawaiApproved($cuti));

            // Feedback ke Pimpinan sendiri
            WAHelper::send($user->no_wa, "✅ Anda menyetujui cuti ini.");

            return response()->json(['status' => 'disetujui_pimpinan']);
        }
    }

    private function handleReject($user, $cuti)
    {
        $cuti->status = 'ditolak';
        $cuti->save();

        // Notif Pegawai
        WAHelper::send($cuti->user->no_wa, FormatHelper::notifPegawaiRejected($cuti));

        // Feedback ke HR / Pimpinan sendiri
        WAHelper::send($user->no_wa, "❌ Anda menolak cuti ini.");

        return response()->json(['status' => 'ditolak']);
    }

    private function notifPimpinan($cuti)
    {
        $pimpinan = User::where('role', 'pimpinan')->first();
        if (!$pimpinan) return;

        $pesan = FormatHelper::notifPimpinan($cuti);

        WAHelper::send($pimpinan->no_wa, $pesan);
    }
}
