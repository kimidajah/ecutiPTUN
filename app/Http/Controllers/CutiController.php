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
            'jenis_cuti'      => 'required|in:Cuti Tahunan,Cuti Besar,Cuti Sakit,Cuti Melahirkan,Cuti Karena Alasan Penting,Cuti Di Luar Tanggungan Negara',
            'tanggal_mulai'   => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'keterangan'      => 'required|max:255',
            'alamat_selama_cuti' => 'nullable|string|max:255',
            'telp_selama_cuti'   => 'nullable|string|max:50',
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
            'hr_id' => $user->hr_id,
            'pimpinan_id' => $user->pimpinan_id,
            'jenis_cuti' => $request->jenis_cuti,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'lama_cuti' => $lamaCuti,
            'alasan' => $request->keterangan,
            'alamat_selama_cuti' => $request->alamat_selama_cuti,
            'telp_selama_cuti' => $request->telp_selama_cuti,
            'status' => 'menunggu',
        ]);

        // 🔔 Kirim notif ke HR (pakai helper)
        $this->notifHR($cuti);

        return redirect()->route('pegawai.cuti.index')->with('success', 'Pengajuan cuti dikirim!');
    }

    private function notifHR($cuti)
    {
        // ambil HR sesuai pegawai
        $pegawai = $cuti->user;
        $hr = User::find($pegawai->hr_id);

        if (!$hr) return;

        $nomor = $hr->no_wa ?? null;
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
            'jenis_cuti'      => 'required|in:Cuti Tahunan,Cuti Besar,Cuti Sakit,Cuti Melahirkan,Cuti Karena Alasan Penting,Cuti Di Luar Tanggungan Negara',
            'tanggal_mulai'   => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'keterangan'      => 'required|string|max:255',
            'alamat_selama_cuti' => 'nullable|string|max:255',
            'telp_selama_cuti'   => 'nullable|string|max:50',
        ]);

        $cuti = Cuti::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $mulai = Carbon::parse($request->tanggal_mulai);
        $selesai = Carbon::parse($request->tanggal_selesai);
        $lamaCuti = $mulai->diffInDays($selesai) + 1;

        $cuti->update([
            'jenis_cuti' => $request->jenis_cuti,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'lama_cuti' => $lamaCuti,
            'alasan' => $request->keterangan,
            'alamat_selama_cuti' => $request->alamat_selama_cuti,
            'telp_selama_cuti' => $request->telp_selama_cuti,
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
