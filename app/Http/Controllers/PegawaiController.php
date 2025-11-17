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
}
