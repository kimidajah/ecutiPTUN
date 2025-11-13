<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Models\Cuti;
use Illuminate\Http\Request;

class AdminController extends Controller
{

     public function index()
    {
        $users = User::all();
        return view('admin.user-karyawan.index', compact('users'));
    }

    public function create()
    {
        return view('admin.user-karyawan.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'role' => 'required',
            'sisa_cuti' => 'required|integer'
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'sisa_cuti' => $request->sisa_cuti,
        ]);

        return redirect()->route('admin.user.index')->with('success', 'User berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        return view('admin.user-karyawan.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required',
            'sisa_cuti' => 'required|integer'
        ]);

        $user->update($request->only('name', 'email', 'role', 'sisa_cuti'));

        return redirect()->route('admin.user.index')->with('success', 'User berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('admin.user.index')->with('success', 'User berhasil dihapus.');
    }

    public function dashboard()
    {
        return view('admin.dashboard', [
            'totalUser'      => User::count(),
            'totalKaryawan'  => User::where('role', 'pegawai')->count(),
            'totalHR'        => User::where('role', 'hr')->count(),
            'totalPimpinan'  => User::where('role', 'pimpinan')->count(),
            'totalCuti'      => Cuti::count(),
            'cutiPending'    => Cuti::where('status', 'pending')->count(),
            'cutiDiterima'   => Cuti::where('status', 'disetujui')->count(),
            'cutiDitolak'    => Cuti::where('status', 'ditolak')->count(),
        ]);
    }


    public function permintaanCuti()
    {
        return view('admin.permintaan-cuti');
    }

    public function aturanCuti()
    {
        return view('admin.aturan-cuti');
    }

    public function userKaryawan()
    {
        $users = User::all();
        return view('admin.user-karyawan.index', compact('users'));
    }

    
}
