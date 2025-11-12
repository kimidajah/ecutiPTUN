<?php

namespace App\Http\Controllers;

use App\Models\Cuti;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CutiController extends Controller
{
    // Menampilkan daftar pengajuan cuti milik pegawai
    public function index()
    {
        $cuti = Cuti::where('user_id', Auth::id())->latest()->get();
        return view('cuti.index', compact('cuti'));
    }

    // Form untuk tambah pengajuan cuti
    public function create()
    {
        return view('cuti.create');
    }

    // Simpan data cuti baru
    public function store(Request $request)
    {
        $request->validate([
            'jenis_cuti' => 'required|string|max:255',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'alasan' => 'required|string',
        ]);

        Cuti::create([
            'user_id' => Auth::id(),
            'jenis_cuti' => $request->jenis_cuti,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'alasan' => $request->alasan,
            'status' => 'menunggu',
        ]);

        return redirect()->route('cuti.index')->with('success', 'Pengajuan cuti berhasil dikirim!');
    }
}
