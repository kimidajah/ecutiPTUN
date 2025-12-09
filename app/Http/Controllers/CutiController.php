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
use App\Helpers\WablasService;

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
            'bukti_file'      => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        // Validasi bukti file wajib untuk cuti sakit dan bersalin
        if (in_array($request->jenis_cuti, ['sakit', 'bersalin']) && !$request->hasFile('bukti_file')) {
            return back()->with('error', 'Bukti surat dokter wajib diunggah untuk cuti sakit dan melahirkan.');
        }

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

        // Handle file upload
        $buktiFilePath = null;
        if ($request->hasFile('bukti_file')) {
            $file = $request->file('bukti_file');
            $fileName = time() . '_' . $user->id . '_' . $file->getClientOriginalName();
            $buktiFilePath = $file->storeAs('bukti_cuti', $fileName, 'public');
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
            'bukti_file' => $buktiFilePath,
            'status' => 'menunggu',
        ]);

        // Note: Saldo cuti akan dikurangi setelah disetujui oleh pimpinan (status disetujui_pimpinan)

        // 🔔 Kirim notif ke HR (pakai helper)
        $this->notifHR($cuti);

        // 🔔 Kirim notif ke user via Wablas
        if ($user->no_wa) {
            WablasService::sendMessage(
                $user->no_wa,
                "*Pengajuan Cuti Diterima*\n\n" .
                "Halo " . $user->nama_pegawai . ",\n\n" .
                "Pengajuan cuti *" . $jenisCuti . "* Anda telah diterima.\n\n" .
                "📅 Tanggal: " . Carbon::parse($request->tanggal_mulai)->format('d/m/Y') . " - " . 
                Carbon::parse($request->tanggal_selesai)->format('d/m/Y') . "\n" .
                "⏱️ Durasi: " . $lamaCuti . " hari\n\n" .
                "Silahkan cek status pengajuan Anda di aplikasi.\n\n" .
                "_Sistem e-Cuti PTUN_"
            );
        }

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

        $pesan = FormatHelper::notifHR($cuti);

        // Gunakan WablasService langsung
        $result = WablasService::sendMessage($nomor, $pesan);
        
        if (!$result['success']) {
            \Illuminate\Support\Facades\Log::warning('Notif HR gagal terkirim', [
                'hr_id' => $hr->id,
                'phone' => $nomor,
                'error' => $result['error'],
            ]);
        }
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
            'bukti_file'      => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
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

        $cuti->update([
            'jenis_cuti' => $jenisCutiBaru,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'lama_cuti' => $lamaCutiBaru,
            'alasan' => $request->keterangan,
            'alamat_selama_cuti' => $request->alamat_selama_cuti,
            'telp_selama_cuti' => $request->telp_selama_cuti,
            'bukti_file' => $buktiFilePath,
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
        // Saldo hanya dikurangi saat status disetujui_pimpinan
        if ($cuti->status === 'disetujui_pimpinan' && !\App\Models\PengaturanCuti::isUnlimited($cuti->jenis_cuti)) {
            if ($cuti->jenis_cuti === 'tahunan') {
                $this->restoreSaldoCutiTahunan($user, $cuti->lama_cuti);
            } else {
                $user->tambahSaldoCutiByJenis($cuti->jenis_cuti, $cuti->lama_cuti);
            }
        }

        $cuti->delete();

        return redirect()->route('pegawai.cuti.index')
            ->with('success', 'Pengajuan cuti dihapus.');
    }
}
