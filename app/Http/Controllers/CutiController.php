<?php

namespace App\Http\Controllers;

use App\Models\Cuti;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

        // Batas cuti tetap = saldo_cuti_tahunan (default 12)
        $batasCuti = $user->saldo_cuti_tahunan;

        // Sisa cuti langsung ambil dari user
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

        // VALIDASI
        if ($user->sisa_cuti <= 0) {
            return back()->with('error', 'Sisa cuti Anda sudah habis.');
        }

        $totalCutiTahunIni = Cuti::where('user_id', $user->id)
            ->whereYear('tanggal_mulai', date('Y'))
            ->sum('lama_cuti');

        $sisaCuti = $user->saldo_cuti_tahunan - $totalCutiTahunIni;

        if ($lamaCuti > $sisaCuti) {
            return back()->with('error', 'Sisa cuti Anda hanya ' . $sisaCuti . ' hari.');
        }


        // SIMPAN
        Cuti::create([
            'user_id'     => $user->id,
            'jenis_cuti'  => 'Cuti Tahunan',
            'tanggal_mulai'   => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'lama_cuti'       => $lamaCuti,
            'alasan'          => $request->keterangan,
            'status'          => 'menunggu',
        ]);

        return redirect()->route('pegawai.cuti.index')
            ->with('success', 'Pengajuan cuti berhasil dikirim!');
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

        // Kembalikan jatah cuti
        $user = \App\Models\User::find(Auth::id());
        // Hapus data cuti
        $cuti->delete();

        return redirect()
            ->route('pegawai.cuti.index')
            ->with('success', 'Pengajuan cuti berhasil dihapus dan jatah cuti dikembalikan.');
    }
}
