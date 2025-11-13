<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PimpinanController extends Controller
{
    public function dashboard()
    {
        return view('pimpinan.dashboard');
    }

    public function permintaanCuti()
    {
        return view('pimpinan.permintaan-cuti');
    }

    public function aturanCuti()
    {
        return view('pimpinan.aturan-cuti');
    }

    public function userKaryawan()
    {
        return view('pimpinan.user-karyawan');
    }
}
