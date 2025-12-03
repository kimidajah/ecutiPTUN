@extends('admin.layouts.app')

@section('title', 'Tambah User')

@section('content')
<div class="card shadow-sm border-0 rounded-4">
    <div class="card-body">
        <h4 class="fw-bold text-success mb-3">Tambah User Baru</h4>

        <form action="{{ route('admin.user.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label class="form-label">Nama</label>
                <input type="text" name="name" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Nomor WA</label>
                <input type="text" name="no_wa" class="form-control" placeholder="628xxxxxxxxxx">
            </div>

            <div class="mb-3">
                <label class="form-label">Role</label>
                <select name="role" id="role-select" class="form-select" required>
                    <option value="pegawai">Pegawai</option>
                    <option value="hr">HR</option>
                    <option value="pimpinan">Pimpinan</option>
                    <option value="admin">Admin</option>
                </select>
            </div>

            {{-- Dropdown HR - Only for Pegawai --}}
            <div class="mb-3" id="hr-field">
                <label class="form-label">Pilih HR</label>
                <select name="hr_id" id="select-hr" class="form-select">
                    <option value="">-- Pilih HR --</option>
                    @foreach($hrList as $hr)
                        <option value="{{ $hr->id }}">{{ $hr->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Dropdown Pimpinan - Only for Pegawai --}}
            <div class="mb-3" id="pimpinan-field">
                <label class="form-label">Pilih Pimpinan</label>
                <select name="pimpinan_id" id="select-pimpinan" class="form-select">
                    <option value="">-- Pilih Pimpinan --</option>
                    @foreach($pimpinanList as $p)
                        <option value="{{ $p->id }}">{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="btn btn-success">Simpan</button>
            <a href="{{ route('admin.user.index') }}" class="btn btn-secondary">Kembali</a>
        </form>
    </div>
</div>

{{-- SCRIPT ROLE HANDLE --}}
<script>
    const roleSelect = document.getElementById('role-select');
    const hrField = document.getElementById('hr-field');
    const pimpinanField = document.getElementById('pimpinan-field');
    const selectHr = document.getElementById('select-hr');
    const selectPimpinan = document.getElementById('select-pimpinan');

    function toggleDropdown() {
        if (roleSelect.value === 'pegawai') {
            // Tampilkan field untuk Pegawai
            hrField.style.display = 'block';
            pimpinanField.style.display = 'block';
            selectHr.disabled = false;
            selectPimpinan.disabled = false;
        } else {
            // Sembunyikan field untuk HR, Pimpinan, dan Admin
            hrField.style.display = 'none';
            pimpinanField.style.display = 'none';
            selectHr.disabled = true;
            selectPimpinan.disabled = true;

            // reset value
            selectHr.value = "";
            selectPimpinan.value = "";
        }
    }

    // Jalankan saat halaman dibuka
    toggleDropdown();

    // Jalankan ketika role berubah
    roleSelect.addEventListener('change', toggleDropdown);
</script>

@endsection
