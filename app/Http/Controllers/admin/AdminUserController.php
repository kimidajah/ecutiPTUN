<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminUserController extends Controller
{
    public function index()
    {
        $users = User::all();
        return view('admin.user-karyawan.index', compact('users'));
    }

    public function create()
    {
        $hrList = User::where('role', 'hr')->get();
        $pimpinanList = User::where('role', 'pimpinan')->get();

        return view('admin.user.create', compact('hrList', 'pimpinanList'));
    }


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
}
    