<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PegawaiController extends Controller
{
    // ===========================
    // DASHBOARD
    // ===========================
    public function dashboard()
    {
        return view('pegawai.dashboard');
    }

    // ===========================
    // CETAK SURAT KETERANGAN CUTI (PDF)
    // ===========================
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
        // Set custom paper size for F4 (approx 215 x 330 mm -> converted to points)
        // 1 mm = 72 / 25.4 points
        $widthPt = 215 * 72 / 25.4; // ~609.45
        $heightPt = 330 * 72 / 25.4; // ~935.04
        // Dompdf expects a four-element array [left, top, right, bottom]
        $dompdf->setPaper([0, 0, $widthPt, $heightPt]);
        $dompdf->render();

        $filename = 'Surat_Keterangan_Cuti_'.$user->name.'_'.now()->format('Ymd').'.pdf';
        return response($dompdf->output(), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="'.$filename.'"');
    }
}
