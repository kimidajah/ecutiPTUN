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
        // Hitung jumlah admin
        $totalAdmin = User::where('role', 'admin')->count();
        
        return view('admin.dashboard', [
            'totalUser'      => User::count(),
            'totalKaryawan'  => User::where('role', 'pegawai')->count(),
            'totalHR'        => User::where('role', 'sub_kepegawaian')->count(),
            'totalPimpinan'  => User::where('role', 'pimpinan')->count(),
            'totalAdmin'     => $totalAdmin,
            'totalCuti'      => Cuti::count(),
            'cutiPending'    => Cuti::where('status', 'menunggu')->count(),
            'cutiDiterima'   => Cuti::whereIn('status', ['disetujui_hr', 'disetujui_pimpinan'])->count(),
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
        $hrList = User::where('role', 'sub_kepegawaian')->get();
        $pimpinanList = User::where('role', 'pimpinan')->get();
        $ketuaList = User::where('role', 'ketua')->get();

        return view('admin.user-karyawan.create', compact('hrList', 'pimpinanList', 'ketuaList'));
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
            'nip' => 'nullable|string|max:50',
            'jabatan' => 'nullable|string|max:100',
            'gol_ruang' => 'nullable|string|max:20',
            'unit_kerja' => 'nullable|string|max:100',
            'tanggal_masuk' => 'nullable|date',
            'hr_id' => 'nullable|exists:users,id',
            'ketua_id' => 'nullable|exists:users,id',
            'pimpinan_id' => 'nullable|exists:users,id',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => $request->role,
            'no_wa' => $request->no_wa,
            'nip' => $request->nip,
            'jabatan' => $request->jabatan,
            'gol_ruang' => $request->gol_ruang,
            'unit_kerja' => $request->unit_kerja,
            'tanggal_masuk' => $request->tanggal_masuk,
            'hr_id' => $request->hr_id,
            'ketua_id' => $request->ketua_id,
            'pimpinan_id' => $request->pimpinan_id,
        ]);

        return redirect()->route('admin.user.index')->with('success', 'User berhasil ditambahkan');
    }


    /**
     * Halaman edit user
     */
    public function edit(User $user)
    {
        $hrList = User::where('role', 'sub_kepegawaian')->get();
        $pimpinanList = User::where('role', 'pimpinan')->get();
        $ketuaList = User::where('role', 'ketua')->get();

        return view('admin.user-karyawan.edit', compact('hrList', 'pimpinanList', 'ketuaList', 'user'));
    }


    /**
     * Proses update user
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $data = $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'no_wa' => 'nullable',
            'nip' => 'nullable|string|max:50',
            'jabatan' => 'nullable|string|max:100',
            'gol_ruang' => 'nullable|string|max:20',
            'unit_kerja' => 'nullable|string|max:100',
            'tanggal_masuk' => 'nullable|date',
            'role' => 'required',
            'hr_id' => 'nullable',
            'ketua_id' => 'nullable',
            'pimpinan_id' => 'nullable',
        ]);

        // Jika role pegawai → HR + Ketua Divisi + Pimpinan
        // Jika role hakim → HR + Pimpinan (tanpa Ketua Divisi)
        // Jika role lain → kosongkan semua
        if ($request->role === 'pegawai') {
            // Pegawai boleh punya HR, Ketua Divisi, dan Pimpinan
        } elseif ($request->role === 'hakim') {
            // Hakim hanya HR dan Pimpinan, kosongkan Ketua Divisi
            $data['ketua_id'] = null;
        } else {
            // Role lain kosongkan semua
            $data['hr_id'] = null;
            $data['ketua_id'] = null;
            $data['pimpinan_id'] = null;
        }

        $user->update($data);

        return redirect()->route('admin.user.index')
            ->with('success', 'User berhasil diperbarui!');
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

    /**
     * Alias: userKaryawan
     */
    public function userKaryawan()
    {
        $users = User::all();
        return view('admin.user-karyawan.index', compact('users'));
    }
}
