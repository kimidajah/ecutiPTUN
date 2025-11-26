<?php

namespace App\Http\Controllers;

use App\Models\Cuti;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Helpers\FormatHelper;
use App\Helpers\WaHelper;

class CutiController extends Controller
{
    public function indexCuti()
    {
        $user = Auth::user();
        $tahunIni = Carbon::now()->year;

        // Ambil cuti tahun ini yang statusnya disetujui saja
        $cutiTahunIni = Cuti::where('user_id', $user->id)
            ->whereYear('tanggal_mulai', $tahunIni)
            ->whereIn('status', ['disetujui_pimpinan'])
            ->get();

        $totalCutiTahunIni = $cutiTahunIni->sum('lama_cuti');
        $batasCuti = $user->saldo_cuti_tahunan;
        $sisaCuti = max(0, $batasCuti - $totalCutiTahunIni);

        return view('pegawai.cuti.index', compact(
            'user',
            'cutiTahunIni',
            'totalCutiTahunIni',
            'batasCuti',
            'sisaCuti'
        ));
    }

    public function createCuti()
    {
        return view('pegawai.cuti.create');
    }

    public function storeCuti(Request $request)
    {
        $request->validate([
            'tanggal_mulai'   => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'keterangan'      => 'required|max:255',
        ]);

        $user = Auth::user();

        $mulai = Carbon::parse($request->tanggal_mulai);
        $selesai = Carbon::parse($request->tanggal_selesai);
        $lamaCuti = $mulai->diffInDays($selesai) + 1;

        // sisa cuti
        $totalCutiTahunIni = Cuti::where('user_id', $user->id)
            ->whereYear('tanggal_mulai', date('Y'))
            ->sum('lama_cuti');

        $sisaCuti = $user->saldo_cuti_tahunan - $totalCutiTahunIni;

        if ($sisaCuti <= 0) return back()->with('error', 'Sisa cuti Anda habis.');
        if ($lamaCuti > $sisaCuti) return back()->with('error', "Sisa cuti Anda hanya $sisaCuti hari.");

        // save
        $cuti = Cuti::create([
            'user_id' => $user->id,
            'jenis_cuti' => 'Cuti Tahunan',
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'lama_cuti' => $lamaCuti,
            'alasan' => $request->keterangan,
            'status' => 'menunggu',
        ]);

        // 🔔 Kirim notif ke HR (pakai helper)
        $this->notifHR($cuti);

        return redirect()->route('pegawai.cuti.index')->with('success', 'Pengajuan cuti dikirim!');
    }

    private function notifHR($cuti)
    {
        $hr = User::where('role', 'hr')->first();
        if (!$hr) return;

        $nomor = $hr->no_wa ?? $hr->wa_number ?? null;
        if (!$nomor) return;

        $pesan = FormatHelper::notifhr($cuti);

        WaHelper::send($nomor, $pesan);
    }

    public function showCuti($id)
    {
        $cuti = Cuti::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        return view('pegawai.cuti.show', compact('cuti'));
    }

    public function editCuti($id)
    {
        $cuti = Cuti::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        return view('pegawai.cuti.edit', compact('cuti'));
    }

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

        $mulai = Carbon::parse($request->tanggal_mulai);
        $selesai = Carbon::parse($request->tanggal_selesai);
        $lamaCuti = $mulai->diffInDays($selesai) + 1;

        $cuti->update([
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'lama_cuti' => $lamaCuti,
            'alasan' => $request->keterangan,
        ]);

        return redirect()->route('pegawai.cuti.show', $cuti->id)
            ->with('success', 'Pengajuan cuti berhasil diperbarui!');
    }

    public function destroyCuti($id)
    {
        $cuti = Cuti::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $cuti->delete();

        return redirect()->route('pegawai.cuti.index')
            ->with('success', 'Pengajuan cuti dihapus.');
    }
}
