@extends('admin.layouts.app')

@section('title', 'User & Karyawan')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold text-success">Daftar User & Karyawan</h3>
    <a href="{{ route('admin.user.create') }}" class="btn btn-success">
        <i class="bi bi-person-plus"></i> Tambah User
    </a>
    <a href="{{ route('admin.user.create') }}" class="btn btn-success">
        <i class="bi bi-person-plus"></i> Tambah HR
    </a>
    <a href="{{ route('admin.user.create') }}" class="btn btn-success">
        <i class="bi bi-person-plus"></i> Tambah Pimpinan
    </a>
</div>

<div class="card shadow-sm border-0 rounded-4">
    <div class="card-body">
        <table class="table table-hover align-middle">
            <thead class="table-success">
                <tr>
                    <th>#</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Sisa Cuti</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $index => $user)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td><span class="badge bg-success">{{ ucfirst($user->role) }}</span></td>
                        <td>{{ $user->sisa_cuti }}</td>
                        <td>
                            <a href="{{ route('admin.user.edit', $user->id) }}" class="btn btn-sm btn-warning">
                                <i class="bi bi-pencil-square"></i>
                            </a>
                            <form action="{{ route('admin.user.destroy', $user->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger"
                                    onclick="return confirm('Yakin ingin menghapus user ini?')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted">Belum ada data pengguna.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
