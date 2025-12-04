<?php

namespace App\Http\Controllers;

use App\Models\Cuti;
use App\Helpers\CutiHelper;
use Illuminate\Http\Request;

class HakimController extends Controller
{
    // =========================
    // Dashboard
    // =========================
    public function dashboard()
    {
        return view('hakim.dashboard');
    }

    // =========================
    // Daftar Pengajuan Cuti
    // =========================
    public function cutiIndex()
    {
        $user = auth()->user();
        $tahunIni = \Carbon\Carbon::now()->year;
        
        // Tampilkan hanya cuti milik hakim yang login
        $dataCuti = Cuti::with('user')
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();

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

        return view('hakim.cuti.index', compact('dataCuti', 'allKategori', 'cutiByKategori', 'user'));
    }

    // =========================
    // Form Pengajuan Cuti
    // =========================
    public function cutiCreate()
    {
        return view('hakim.cuti.create');
    }

    // =========================
    // Simpan Pengajuan Cuti
    // =========================
    public function cutiStore(Request $request)
    {
        $request->validate([
            'jenis_cuti' => 'required|in:tahunan,sakit,bersalin,penting,besar',
            'tanggal_mulai' => 'required|date|after_or_equal:today',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'alasan' => 'required|string',
            'alamat_selama_cuti' => 'required|string',
            'telp_selama_cuti' => 'required|string',
        ]);

        $user = auth()->user();
        $jenisCuti = $request->jenis_cuti;

        // Hitung lama cuti (mengecualikan hari libur nasional)
        $tanggal_mulai = \Carbon\Carbon::parse($request->tanggal_mulai);
        $tanggal_selesai = \Carbon\Carbon::parse($request->tanggal_selesai);
        $lama_cuti = CutiHelper::hitungHariKerjaCuti($tanggal_mulai, $tanggal_selesai);

        // Validasi saldo cuti berdasarkan jenis
        $validasi = CutiHelper::validateCutiSaldo($user->id, $jenisCuti, $lama_cuti);
        
        if (!$validasi['valid']) {
            return back()->with('error', $validasi['message']);
        }

        // Simpan pengajuan
        Cuti::create([
            'user_id' => auth()->id(),
            'hr_id' => $user->hr_id,
            'pimpinan_id' => $user->pimpinan_id,
            'jenis_cuti' => $jenisCuti,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'lama_cuti' => $lama_cuti,
            'alasan' => $request->alasan,
            'alamat_selama_cuti' => $request->alamat_selama_cuti,
            'telp_selama_cuti' => $request->telp_selama_cuti,
            'status' => 'menunggu',
        ]);

        // Kurangi saldo cuti (kecuali cuti sakit yang unlimited)
        if (!\App\Models\PengaturanCuti::isUnlimited($jenisCuti)) {
            if ($jenisCuti === 'tahunan') {
                $user->kurangiSaldoCutiTahunanDenganPrioritasTahunLalu($lama_cuti);
            } else {
                $user->kurangiSaldoCutiByJenis($jenisCuti, $lama_cuti);
            }
        }

        return redirect()->route('hakim.cuti.index')->with('success', 'Pengajuan cuti berhasil dibuat.');
    }

    // =========================
    // Detail Cuti
    // =========================
    public function cutiShow($id)
    {
        $cuti = Cuti::with('user')->findOrFail($id);

        // Hanya hakim yang bersangkutan yang bisa melihat
        if ($cuti->user_id !== auth()->id()) {
            abort(403);
        }

        return view('hakim.cuti.show', compact('cuti'));
    }

    // =========================
    // Edit Cuti (sebelum disetujui Sub Kepegawaian)
    // =========================
    public function cutiEdit($id)
    {
        $cuti = Cuti::findOrFail($id);

        if ($cuti->user_id !== auth()->id()) {
            abort(403);
        }

        if ($cuti->status !== 'menunggu') {
            return back()->with('error', 'Pengajuan tidak dapat diubah karena sudah diproses.');
        }

        return view('hakim.cuti.edit', compact('cuti'));
    }

    // =========================
    // Update Cuti
    // =========================
    public function cutiUpdate(Request $request, $id)
    {
        $cuti = Cuti::findOrFail($id);

        if ($cuti->user_id !== auth()->id()) {
            abort(403);
        }

        if ($cuti->status !== 'menunggu') {
            return back()->with('error', 'Pengajuan tidak dapat diubah.');
        }

        $request->validate([
            'jenis_cuti' => 'required|in:tahunan,sakit,bersalin,penting,besar',
            'tanggal_mulai' => 'required|date|after_or_equal:today',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'alasan' => 'required|string',
            'alamat_selama_cuti' => 'required|string',
            'telp_selama_cuti' => 'required|string',
        ]);

        $user = auth()->user();
        $jenisCutiLama = $cuti->jenis_cuti;
        $lamaCutiLama = $cuti->lama_cuti;

        // Hitung lama cuti (mengecualikan hari libur nasional)
        $tanggal_mulai = \Carbon\Carbon::parse($request->tanggal_mulai);
        $tanggal_selesai = \Carbon\Carbon::parse($request->tanggal_selesai);
        $lamaCutiBaru = CutiHelper::hitungHariKerjaCuti($tanggal_mulai, $tanggal_selesai);
        $jenisCutiBaru = $request->jenis_cuti;

        // Kembalikan saldo lama (kecuali sakit)
        if (!\App\Models\PengaturanCuti::isUnlimited($jenisCutiLama)) {
            if ($jenisCutiLama === 'tahunan') {
                $this->restoreSaldoCutiTahunan($user, $lamaCutiLama);
            } else {
                $user->tambahSaldoCutiByJenis($jenisCutiLama, $lamaCutiLama);
            }
        }

        // Validasi saldo baru
        $validasi = CutiHelper::validateCutiSaldo($user->id, $jenisCutiBaru, $lamaCutiBaru);
        if (!$validasi['valid']) {
            // Rollback
            if (!\App\Models\PengaturanCuti::isUnlimited($jenisCutiLama)) {
                if ($jenisCutiLama === 'tahunan') {
                    $user->kurangiSaldoCutiTahunanDenganPrioritasTahunLalu($lamaCutiLama);
                } else {
                    $user->kurangiSaldoCutiByJenis($jenisCutiLama, $lamaCutiLama);
                }
            }
            return back()->with('error', $validasi['message']);
        }

        // Kurangi saldo baru (kecuali sakit)
        if (!\App\Models\PengaturanCuti::isUnlimited($jenisCutiBaru)) {
            if ($jenisCutiBaru === 'tahunan') {
                $user->kurangiSaldoCutiTahunanDenganPrioritasTahunLalu($lamaCutiBaru);
            } else {
                $user->kurangiSaldoCutiByJenis($jenisCutiBaru, $lamaCutiBaru);
            }
        }

        // Update
        $cuti->update([
            'jenis_cuti' => $jenisCutiBaru,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'lama_cuti' => $lamaCutiBaru,
            'alasan' => $request->alasan,
            'alamat_selama_cuti' => $request->alamat_selama_cuti,
            'telp_selama_cuti' => $request->telp_selama_cuti,
        ]);

        return redirect()->route('hakim.cuti.index')->with('success', 'Pengajuan cuti berhasil diperbarui.');
    }

    // =========================
    // Batalkan Pengajuan
    // =========================
    public function cutiCancel($id)
    {
        $cuti = Cuti::findOrFail($id);

        if ($cuti->user_id !== auth()->id()) {
            abort(403);
        }

        if ($cuti->status !== 'menunggu') {
            return back()->with('error', 'Hanya pengajuan yang menunggu yang dapat dibatalkan.');
        }

        $user = auth()->user();
        
        // Kembalikan saldo (kecuali sakit)
        if (!\App\Models\PengaturanCuti::isUnlimited($cuti->jenis_cuti)) {
            if ($cuti->jenis_cuti === 'tahunan') {
                $this->restoreSaldoCutiTahunan($user, $cuti->lama_cuti);
            } else {
                $user->tambahSaldoCutiByJenis($cuti->jenis_cuti, $cuti->lama_cuti);
            }
        }

        $cuti->delete();

        return redirect()->route('hakim.cuti.index')->with('success', 'Pengajuan cuti berhasil dibatalkan.');
    }

    // =========================
    // CETAK SURAT KETERANGAN CUTI (PDF)
    // =========================
    public function cetakSuratCuti($id)
    {
        $user = auth()->user();
        $cuti = \App\Models\Cuti::with('user')->where('user_id', $user->id)->findOrFail($id);

        if ($cuti->status !== 'disetujui_pimpinan') {
            return back()->with('error', 'Surat hanya tersedia untuk cuti yang telah disetujui pimpinan.');
        }

        // Render HTML dari Blade
        $html = view('pegawai.cetak_pdf.surat', compact('cuti', 'user'))->render();

        // Gunakan Dompdf secara langsung
        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4');
        $dompdf->render();

        $filename = 'Surat_Keterangan_Cuti_'.$user->name.'_'.now()->format('Ymd').'.pdf';
        return response($dompdf->output(), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="'.$filename.'"');
    }

    /**
     * Helper untuk restore saldo cuti tahunan (menambah kembali)
     */
    private function restoreSaldoCutiTahunan(\App\Models\User $user, $jumlah)
    {
        $kapasitasTahunIni = 12;
        $spaceTahunIni = $kapasitasTahunIni - $user->saldo_cuti_tahunan;

        if ($spaceTahunIni >= $jumlah) {
            $user->saldo_cuti_tahunan += $jumlah;
        } else {
            $user->saldo_cuti_tahunan = $kapasitasTahunIni;
            $sisa = $jumlah - $spaceTahunIni;
            $user->saldo_cuti_tahun_lalu += $sisa;
        }

        $user->save();
    }
}
