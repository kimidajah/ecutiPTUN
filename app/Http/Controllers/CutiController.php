<?php

namespace App\Http\Controllers;

use App\Models\Cuti;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Helpers\FormatHelper;
use App\Helpers\CutiHelper;
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

        // Data untuk cards per kategori
        $allKategori = [
            'tahunan' => 'Cuti Tahunan',
            'sakit' => 'Cuti Sakit',
            'bersalin' => 'Cuti Bersalin',
            'penting' => 'Cuti Penting',
            'besar' => 'Cuti Besar',
        ];
        
        $cutiByKategori = Cuti::where('user_id', $user->id)
            ->whereYear('tanggal_mulai', $tahunIni)
            ->where('status', 'disetujui_pimpinan')
            ->groupBy('jenis_cuti')
            ->selectRaw('jenis_cuti, SUM(lama_cuti) as total_hari, COUNT(*) as jumlah_pengajuan')
            ->get()
            ->keyBy('jenis_cuti');

        return view('pegawai.cuti.index', compact(
            'user',
            'cutiTahunIni',
            'totalCutiTahunIni',
            'batasCuti',
            'sisaCuti',
            'allKategori',
            'cutiByKategori'
        ));
    }

    public function createCuti()
    {
        return view('pegawai.cuti.create');
    }

    private function kurangiSaldoCutiTahunanDenganPrioritasTahunLalu(User $user, $jumlah)
    {
        $user->kurangiSaldoCutiTahunanDenganPrioritasTahunLalu($jumlah);
    }

    public function storeCuti(Request $request)
    {
        $request->validate([
            'jenis_cuti'      => 'required|in:tahunan,sakit,bersalin,penting,besar',
            'tanggal_mulai'   => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'keterangan'      => 'required|max:255',
            'alamat_selama_cuti' => 'nullable|string|max:255',
            'telp_selama_cuti'   => 'nullable|string|max:50',
        ]);

        $user = Auth::user();
        $jenisCuti = $request->jenis_cuti;

        $mulai = Carbon::parse($request->tanggal_mulai);
        $selesai = Carbon::parse($request->tanggal_selesai);
        $lamaCuti = CutiHelper::hitungHariKerjaCuti($mulai, $selesai);

        // Validasi saldo cuti berdasarkan jenis
        $validasi = CutiHelper::validateCutiSaldo($user->id, $jenisCuti, $lamaCuti);
        
        if (!$validasi['valid']) {
            return back()->with('error', $validasi['message']);
        }

        // save
        $cuti = Cuti::create([
            'user_id' => $user->id,
            'hr_id' => $user->hr_id,
            'ketua_id' => $user->ketua_id,
            'pimpinan_id' => $user->pimpinan_id,
            'jenis_cuti' => $jenisCuti,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'lama_cuti' => $lamaCuti,
            'alasan' => $request->keterangan,
            'alamat_selama_cuti' => $request->alamat_selama_cuti,
            'telp_selama_cuti' => $request->telp_selama_cuti,
            'status' => 'menunggu',
        ]);

        // Kurangi saldo cuti (kecuali cuti sakit yang unlimited)
        if (!\App\Models\PengaturanCuti::isUnlimited($jenisCuti)) {
            if ($jenisCuti === 'tahunan') {
                // Prioritas: kurangi tahun lalu dulu, baru tahun ini
                $user->kurangiSaldoCutiTahunanDenganPrioritasTahunLalu($lamaCuti);
            } else {
                $user->kurangiSaldoCutiByJenis($jenisCuti, $lamaCuti);
            }
        }

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

    /**
     * Helper untuk restore saldo cuti tahunan (menambah kembali)
     * Restore ke tahun ini dulu, jika penuh baru ke tahun lalu
     */
    private function restoreSaldoCutiTahunan(User $user, $jumlah)
    {
        $kapasitasTahunIni = 12; // Default kapasitas tahunan
        $spaceTahunIni = $kapasitasTahunIni - $user->saldo_cuti_tahunan;

        if ($spaceTahunIni >= $jumlah) {
            // Tambah ke tahun ini saja
            $user->saldo_cuti_tahunan += $jumlah;
        } else {
            // Isi tahun ini penuh, sisanya ke tahun lalu
            $user->saldo_cuti_tahunan = $kapasitasTahunIni;
            $sisa = $jumlah - $spaceTahunIni;
            $user->saldo_cuti_tahun_lalu += $sisa;
        }

        $user->save();
    }

    /**
     * Helper untuk kurangi saldo cuti tahunan dengan prioritas tahun lalu
     */



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
            'jenis_cuti'      => 'required|in:tahunan,sakit,bersalin,penting,besar',
            'tanggal_mulai'   => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'keterangan'      => 'required|string|max:255',
            'alamat_selama_cuti' => 'nullable|string|max:255',
            'telp_selama_cuti'   => 'nullable|string|max:50',
        ]);

        $cuti = Cuti::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $user = Auth::user();
        $jenisCutiLama = $cuti->jenis_cuti;
        $lamaCutiLama = $cuti->lama_cuti;

        $mulai = Carbon::parse($request->tanggal_mulai);
        $selesai = Carbon::parse($request->tanggal_selesai);
        $lamaCutiBaru = CutiHelper::hitungHariKerjaCuti($mulai, $selesai);
        $jenisCutiBaru = $request->jenis_cuti;

        // Kembalikan saldo lama (kecuali sakit)
        if (!\App\Models\PengaturanCuti::isUnlimited($jenisCutiLama)) {
            if ($jenisCutiLama === 'tahunan') {
                // Untuk tahun lalu yang sudah digunakan, kembalikan ke tahun lalu dulu
                $this->restoreSaldoCutiTahunan($user, $lamaCutiLama);
            } else {
                $user->tambahSaldoCutiByJenis($jenisCutiLama, $lamaCutiLama);
            }
        }

        // Validasi saldo baru
        $validasi = CutiHelper::validateCutiSaldo($user->id, $jenisCutiBaru, $lamaCutiBaru);
        if (!$validasi['valid']) {
            // Rollback: kembalikan pengurangan saldo
            if (!\App\Models\PengaturanCuti::isUnlimited($jenisCutiLama)) {
                if ($jenisCutiLama === 'tahunan') {
                    $this->kurangiSaldoCutiTahunanDenganPrioritasTahunLalu($user, $lamaCutiLama);
                } else {
                    $user->kurangiSaldoCutiByJenis($jenisCutiLama, $lamaCutiLama);
                }
            }
            return back()->with('error', $validasi['message']);
        }

        // Kurangi saldo baru (kecuali sakit)
        if (!\App\Models\PengaturanCuti::isUnlimited($jenisCutiBaru)) {
            if ($jenisCutiBaru === 'tahunan') {
                // Prioritas: kurangi tahun lalu dulu, baru tahun ini
                $user->kurangiSaldoCutiTahunanDenganPrioritasTahunLalu($lamaCutiBaru);
            } else {
                $user->kurangiSaldoCutiByJenis($jenisCutiBaru, $lamaCutiBaru);
            }
        }

        $cuti->update([
            'jenis_cuti' => $jenisCutiBaru,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'lama_cuti' => $lamaCutiBaru,
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

        $user = Auth::user();
        
        // Kembalikan saldo jika cuti sudah disetujui (kecuali sakit)
        if ($cuti->status === 'disetujui_pimpinan' && !\App\Models\PengaturanCuti::isUnlimited($cuti->jenis_cuti)) {
            $user->tambahSaldoCutiByJenis($cuti->jenis_cuti, $cuti->lama_cuti);
        }

        $cuti->delete();

        return redirect()->route('pegawai.cuti.index')
            ->with('success', 'Pengajuan cuti dihapus.');
    }
}
