<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {
        // Redirect berdasarkan role
        if (Auth::check()) {
            $role = Auth::user()->role;
            
            return match($role) {
                'admin' => redirect()->route('admin.dashboard'),
                'sub_kepegawaian' => redirect()->route('hr.dashboard'),
                'hakim' => redirect()->route('hakim.dashboard'),
                'ketua' => redirect()->route('ketua.dashboard'),
                'pimpinan' => redirect()->route('pimpinan.dashboard'),
                'pegawai' => redirect()->route('pegawai.dashboard'),
                default => view('home'),
            };
        }
        
        return view('home');
    }
}
