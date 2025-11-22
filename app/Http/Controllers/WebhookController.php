<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cuti;
use App\Models\User;

class WebhookController extends Controller
{
    public function handleWA(Request $request)
    {
        // Fonnte data
        $from    = $request->sender;  // nomor pengirim
        $message = trim($request->text); // isi pesan

        // CEK USER
        $user = User::where('no_wa', $from)->first();
        if (!$user) {
            return response()->json(['status' => 'unknown_number']);
        }

        // CEK CUTI SESUAI ROLE
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
        //     SETUJU (BALAS "1")
        // ==========================
        if ($message == '1') {

            if ($user->role === 'hr') {

                // UPDATE STATUS CUTI
                $cuti->update(['status' => 'disetujui_hr']);

                // NOTIFY PEGAWAI
                sendWA($cuti->user->no_wa,
                    "Pengajuan cuti Anda telah *DISETUJUI HR*.\nMenunggu persetujuan pimpinan."
                );

                // NOTIFY PIMPINAN
                $this->notifPimpinan($cuti);

                return response()->json(['status' => 'disetujui_hr']);
            }

            if ($user->role === 'pimpinan') {

                $cuti->update(['status' => 'disetujui_pimpinan']);

                sendWA($cuti->user->no_wa,
                    "Pengajuan cuti Anda telah *DISETUJUI PIMPINAN*."
                );

                return response()->json(['status' => 'disetujui_pimpinan']);
            }
        }

        // ==========================
        //     TOLAK (BALAS "2")
        // ==========================
        if ($message == '2') {

            $cuti->update(['status' => 'ditolak']);

            sendWA($cuti->user->no_wa,
                "Pengajuan cuti Anda *DITOLAK oleh {$user->role}*."
            );

            return response()->json(['status' => 'ditolak']);
        }

        return response()->json(['status' => 'invalid_command']);
    }


    private function notifPimpinan($cuti)
    {
        $pimpinan = User::where('role', 'pimpinan')->first();
        if (!$pimpinan) return;

        $pesan = 
            "📢 *Persetujuan Cuti*\n".
            "Nama: {$cuti->user->name}\n".
            "Tanggal: {$cuti->tanggal_mulai} s/d {$cuti->tanggal_selesai}\n".
            "Balas *1* untuk SETUJU\n".
            "Balas *2* untuk TOLAK";

        sendWA($pimpinan->no_wa, $pesan);
    }
}
