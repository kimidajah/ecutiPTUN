<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HRController extends Controller
{
    public function dashboard()
    {
        return view('hr.dashboard');
    }

    public function permintaanCuti()
    {
        return view('hr.permintaan-cuti');
    }

    public function aturanCuti()
    {
        return view('hr.aturan-cuti');
    }

    public function userKaryawan()
    {
        return view('hr.user-karyawan');
    }
}
