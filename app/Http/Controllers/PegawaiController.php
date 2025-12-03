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
        $dompdf->setPaper('A4');
        $dompdf->render();

        $filename = 'Surat_Keterangan_Cuti_'.$user->name.'_'.now()->format('Ymd').'.pdf';
        return response($dompdf->output(), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="'.$filename.'"');
    }
}
