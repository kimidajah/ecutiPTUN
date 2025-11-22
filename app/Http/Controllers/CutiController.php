<?php

namespace App\Http\Controllers;

use App\Models\Cuti;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CutiController extends Controller
{
    // ===========================
    // INDEX (DAFTAR CUTI)
    // ===========================
    public function indexCuti()
    {
        $user = Auth::user();
        $tahunIni = Carbon::now()->year;

        $cutiTahunIni = Cuti::where('user_id', $user->id)
            ->whereYear('tanggal_mulai', $tahunIni)
            ->get();

        $totalCutiTahunIni = $cutiTahunIni->sum('lama_cuti');

        $batasCuti = $user->saldo_cuti_tahunan;

        $sisaCuti = $batasCuti - $totalCutiTahunIni;
        if ($sisaCuti < 0) $sisaCuti = 0; 

        return view('pegawai.cuti.index', compact(
            'user',
            'cutiTahunIni',
            'totalCutiTahunIni',
            'batasCuti',
            'sisaCuti'
        ));
    }

    // ===========================
    // FORM BUAT CUTI
    // ===========================
    public function createCuti()
    {
        return view('pegawai.cuti.create');
    }

    // ===========================
    // SIMPAN CUTI BARU
    // ===========================
    public function storeCuti(Request $request)
    {
        $request->validate([
            'tanggal_mulai'   => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'keterangan'      => 'required|string|max:255',
        ]);

        $user = Auth::user();

        $tanggalMulai = Carbon::parse($request->tanggal_mulai);
        $tanggalSelesai = Carbon::parse($request->tanggal_selesai);
        $lamaCuti = $tanggalMulai->diffInDays($tanggalSelesai) + 1;

        // VALIDASI SISA CUTI
        $totalCutiTahunIni = Cuti::where('user_id', $user->id)
            ->whereYear('tanggal_mulai', date('Y'))
            ->sum('lama_cuti');

        $sisaCuti = $user->saldo_cuti_tahunan - $totalCutiTahunIni;

        if ($sisaCuti <= 0) {
            return back()->with('error', 'Sisa cuti Anda sudah habis.');
        }

        if ($lamaCuti > $sisaCuti) {
            return back()->with('error', 'Sisa cuti Anda hanya ' . $sisaCuti . ' hari.');
        }

        // SIMPAN CUTI DENGAN STATUS "MENUNGGU"
        $cuti = Cuti::create([
            'user_id'        => $user->id,
            'jenis_cuti'     => 'Cuti Tahunan',
            'tanggal_mulai'  => $request->tanggal_mulai,
            'tanggal_selesai'=> $request->tanggal_selesai,
            'lama_cuti'      => $lamaCuti,
            'alasan'         => $request->keterangan,
            'status'         => 'menunggu', // KONSISTEN
        ]);

        // 🔔 Kirim notif ke HR
        $this->notifHR($cuti);

        return redirect()->route('pegawai.cuti.index')
            ->with('success', 'Pengajuan cuti berhasil dikirim!');
    }

    private function notifHR($cuti)
    {
        // Pastikan kolom nomor WA HR sesuai (wa_number / no_wa)
        $hr = \App\Models\User::where('role', 'hr')->first();
        if (!$hr) return;

        $pesan = "Pengajuan Cuti Baru\n";
        $pesan .= "Nama: {$cuti->user->name}\n";
        $pesan .= "Jenis: {$cuti->jenis_cuti}\n";
        $pesan .= "Tanggal: {$cuti->tanggal_mulai} s/d {$cuti->tanggal_selesai}\n";
        $pesan .= "Lama: {$cuti->lama_cuti} hari\n";
        $pesan .= "Alasan: {$cuti->alasan}\n";
        $pesan .= "Balas 1 untuk Terima, 2 untuk Tolak";

        // gunakan kolom yang kamu pakai di tabel users; ganti jika fieldnya `no_wa`
        $waField = $hr->wa_number ?? $hr->no_wa ?? null;
        if (!$waField) {
            Log::warning("notifHR: HR tidak memiliki nomor WA untuk menerima notifikasi (user_id: {$hr->id})");
            return;
        }

        $this->sendWA($waField, $pesan);
    }

    // ===========================
    // DETAIL CUTI
    // ===========================
    public function showCuti($id)
    {
        $cuti = Cuti::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        return view('pegawai.cuti.show', compact('cuti'));
    }

    // ===========================
    // EDIT CUTI
    // ===========================
    public function editCuti($id)
    {
        $cuti = Cuti::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        return view('pegawai.cuti.edit', compact('cuti'));
    }

    // ===========================
    // UPDATE CUTI
    // ===========================
    public function updateCuti(Request $request, $id)
    {
        $request->validate([
            'tanggal_mulai'   => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'keterangan'      => 'required|string|max:255',
        ]);

        $cuti = Cuti::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $tanggalMulai = Carbon::parse($request->tanggal_mulai);
        $tanggalSelesai = Carbon::parse($request->tanggal_selesai);
        $lamaCuti = $tanggalMulai->diffInDays($tanggalSelesai) + 1;

        // UPDATE TANPA MENGUBAH STATUS KARENA STATUS DIURUS HR/PIMPINAN
        $cuti->update([
            'tanggal_mulai'   => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'lama_cuti'       => $lamaCuti,
            'alasan'          => $request->keterangan,  
        ]);

        return redirect()->route('pegawai.cuti.show', $cuti->id)->with('success', 'Pengajuan cuti berhasil diperbarui!');
    }

    // ===========================
    // HAPUS CUTI
    // ===========================
    public function destroyCuti($id)
    {
        $cuti = Cuti::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $cuti->delete();

        return redirect()
            ->route('pegawai.cuti.index')
            ->with('success', 'Pengajuan cuti berhasil dihapus.');
    }

    // ===========================
    // UTIL: Kirim WA lewat Fonnte (atau API serupa)
    // ===========================
    private function sendWA($target, $message)
    {
        try {
            // normalize nomor: jika dimulai 0 → ubah ke 62...
            if (substr($target, 0, 1) === '0') {
                $target = '62' . substr($target, 1);
            }

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
                ],
                CURLOPT_TIMEOUT => 10,
            ]);

            $response = curl_exec($curl);
            $info = curl_getinfo($curl);
            $err = curl_error($curl);
            curl_close($curl);

            if ($err) {
                Log::error("sendWA curl error: {$err}", ['target' => $target, 'response_info' => $info]);
                return false;
            }

            // log response body when not successful (helps debugging API)
            if ($info['http_code'] < 200 || $info['http_code'] >= 300) {
                Log::error("sendWA http error", ['target' => $target, 'http_code' => $info['http_code'], 'body' => $response]);
                return false;
            }

            return $response;
        } catch (\Exception $e) {
            Log::error("sendWA exception: " . $e->getMessage());
            return false;
        }
    }
}
