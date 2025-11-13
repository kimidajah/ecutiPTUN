<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PegawaiController extends Controller
{
    public function dashboard()
    {
        return view('pegawai.dashboard');
    }

    public function permintaanCuti()
    {
        return view('pegawai.permintaan-cuti');
    }

    public function aturanCuti()
    {
        return view('pegawai.aturan-cuti');
    }

    public function userKaryawan()
    {
        return view('pegawai.user-karyawan');
    }
}
