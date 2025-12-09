<?php

namespace App\Http\Controllers;

use App\Models\Cuti;
use App\Helpers\CutiHelper;
use App\Helpers\WablasService;
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
            'bukti_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        // Validasi bukti file wajib untuk cuti sakit dan bersalin
        if (in_array($request->jenis_cuti, ['sakit', 'bersalin']) && !$request->hasFile('bukti_file')) {
            return back()->with('error', 'Bukti surat dokter wajib diunggah untuk cuti sakit dan melahirkan.');
        }

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

        // Handle file upload
        $buktiFilePath = null;
        if ($request->hasFile('bukti_file')) {
            $file = $request->file('bukti_file');
            $fileName = time() . '_' . $user->id . '_' . $file->getClientOriginalName();
            $buktiFilePath = $file->storeAs('bukti_cuti', $fileName, 'public');
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
            'bukti_file' => $buktiFilePath,
            'status' => 'menunggu',
        ]);

        // Note: Saldo cuti akan dikurangi setelah disetujui oleh pimpinan (status disetujui_pimpinan)

        // 🔔 Kirim notif ke user via Wablas
        if ($user->no_wa) {
            WablasService::sendMessage(
                $user->no_wa,
                "*Pengajuan Cuti Diterima*\n\n" .
                "Halo " . $user->nama_pegawai . ",\n\n" .
                "Pengajuan cuti *" . $jenisCuti . "* Anda telah diterima.\n\n" .
                "📅 Tanggal: " . $tanggal_mulai->format('d/m/Y') . " - " . $tanggal_selesai->format('d/m/Y') . "\n" .
                "⏱️ Durasi: " . $lama_cuti . " hari\n\n" .
                "Silahkan cek status pengajuan Anda di aplikasi.\n\n" .
                "_Sistem e-Cuti PTUN_"
            );
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
            'bukti_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $user = auth()->user();
        $jenisCutiLama = $cuti->jenis_cuti;
        $lamaCutiLama = $cuti->lama_cuti;

        // Hitung lama cuti (mengecualikan hari libur nasional)
        $tanggal_mulai = \Carbon\Carbon::parse($request->tanggal_mulai);
        $tanggal_selesai = \Carbon\Carbon::parse($request->tanggal_selesai);
        $lamaCutiBaru = CutiHelper::hitungHariKerjaCuti($tanggal_mulai, $tanggal_selesai);
        $jenisCutiBaru = $request->jenis_cuti;

        // Validasi saldo baru (hanya saat menunggu, saldo tidak diubah di edit)
        // Hanya validasi bahwa masih ada saldo yang cukup
        $validasi = CutiHelper::validateCutiSaldo($user->id, $jenisCutiBaru, $lamaCutiBaru);
        if (!$validasi['valid']) {
            return back()->with('error', $validasi['message']);
        }

        // Handle file upload
        $buktiFilePath = $cuti->bukti_file; // Keep existing file by default
        if ($request->hasFile('bukti_file')) {
            // Delete old file if exists
            if ($cuti->bukti_file && \Storage::disk('public')->exists($cuti->bukti_file)) {
                \Storage::disk('public')->delete($cuti->bukti_file);
            }
            
            $file = $request->file('bukti_file');
            $fileName = time() . '_' . $user->id . '_' . $file->getClientOriginalName();
            $buktiFilePath = $file->storeAs('bukti_cuti', $fileName, 'public');
        }

        // Note: Saldo cuti akan dikurangi saat status disetujui_pimpinan
        // Jangan ubah saldo saat edit, hanya ubah tanggal dan jenis cuti

        // Update
        $cuti->update([
            'jenis_cuti' => $jenisCutiBaru,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'lama_cuti' => $lamaCutiBaru,
            'alasan' => $request->alasan,
            'alamat_selama_cuti' => $request->alamat_selama_cuti,
            'telp_selama_cuti' => $request->telp_selama_cuti,
            'bukti_file' => $buktiFilePath,
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

        // Note: Saldo tidak perlu dikembalikan karena belum dikurangi saat status menunggu
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
