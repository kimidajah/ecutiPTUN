<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Cuti;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    /**
     * Dashboard Admin
     */
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


    /**
     * Halaman daftar User/Karyawan
     */
    public function index()
    {
        $users = User::all();
        return view('admin.user-karyawan.index', compact('users'));
    }


    /**
     * Halaman tambah user
     */
    public function create()
    {
        $hrList = User::where('role', 'hr')->get();
        $pimpinanList = User::where('role', 'pimpinan')->get();

        return view('admin.user-karyawan.create', compact('hrList', 'pimpinanList'));
    }


    /**
     * Proses tambah user
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required',
            'role' => 'required',
            'no_wa' => 'nullable',
            'hr_id' => 'nullable|exists:users,id',
            'pimpinan_id' => 'nullable|exists:users,id',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => $request->role,
            'no_wa' => $request->no_wa,
            'hr_id' => $request->hr_id,
            'pimpinan_id' => $request->pimpinan_id,
        ]);

        return redirect()->route('admin.user.index')->with('success', 'User berhasil ditambahkan');
    }


    /**
     * Halaman edit user
     */
    public function edit(User $user)
    {
        return view('admin.user-karyawan.edit', compact('user'));
    }


    /**
     * Proses update user
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'      => 'required|string',
            'email'     => 'required|email|unique:users,email,' . $user->id,
            'role'      => 'required',
            'sisa_cuti' => 'required|integer|min:0|max:24',
            'no_wa'     => 'nullable|string|max:20',
        ]);

        $user->update([
            'name'       => $request->name,
            'email'      => $request->email,
            'role'       => $request->role,
            'sisa_cuti'  => $request->sisa_cuti,
            'no_wa'      => $request->no_wa,
        ]);

        return redirect()->route('admin.user.index')
            ->with('success', 'User berhasil diperbarui.');
    }


    /**
     * Hapus user
     */
    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('admin.user.index')
            ->with('success', 'User berhasil dihapus.');
    }


    /**
     * Menu permintaan cuti
     */
    public function permintaanCuti()
    {
        $permintaanCuti = Cuti::with('user')->orderBy('created_at', 'desc')->get();

        return view('admin.permintaan-cuti.index', compact('permintaanCuti'));
    }


    public function detailCuti($id)
    {
        $cuti = Cuti::with('user')->findOrFail($id);

        return view('admin.permintaan-cuti.show', compact('cuti'));
    }


    /**
     * Alias: userKaryawan
     */
    public function userKaryawan()
    {
        $users = User::all();
        return view('admin.user-karyawan.index', compact('users'));
    }
}
