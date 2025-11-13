<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Tentukan redirect setelah login
     */
    protected function redirectTo()
    {
        $user = Auth::user();

        switch ($user->role) {
            case 'pegawai':
                return route('pegawai.dashboard'); // /pegawai/dashboard
            case 'hr':
                return route('hr.dashboard'); // /hr/dashboard
            case 'pimpinan':
                return route('pimpinan.dashboard'); // /pimpinan/dashboard
            case 'admin':
                return route('admin.dashboard'); // /admin/dashboard
            default:
                return '/home';
        }
    }

    /**
     * Konstruktor
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
}
