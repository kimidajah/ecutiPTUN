@extends('admin.layouts.app')

@section('title', 'Edit User')

@section('content')
<div class="card shadow-sm border-0 rounded-4">
    <div class="card-body">
        <h4 class="fw-bold text-success mb-3">Edit Data User</h4>

        <form action="{{ route('admin.user.update', $user->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label class="form-label">Nama</label>
                <input type="text" name="name" class="form-control" value="{{ $user->name }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="{{ $user->email }}" required>
            </div>

            <div class="mb-3">
                <label for="no_wa" class="form-label">Nomor WhatsApp</label>
                <input type="text" name="no_wa" class="form-control" value="{{ $user->no_wa }}" placeholder="628xxxxxxxxxx">
            </div>

            <div class="mb-3">
                <label class="form-label">Role</label>
                <select name="role" id="roleSelect" class="form-select" required>
                    <option value="pegawai" {{ $user->role == 'pegawai' ? 'selected' : '' }}>Pegawai</option>
                    <option value="hr" {{ $user->role == 'hr' ? 'selected' : '' }}>HR</option>
                    <option value="pimpinan" {{ $user->role == 'pimpinan' ? 'selected' : '' }}>Pimpinan</option>
                    <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
                </select>
            </div>

            {{-- DROPDOWN HR - Only for Pegawai --}}
            <div class="mb-3" id="hrField">
                <label class="form-label">Pilih HR</label>
                <select name="hr_id" id="hrSelect" class="form-select">
                    <option value="">-- Pilih HR --</option>
                    @foreach($hrList as $hr)
                        <option value="{{ $hr->id }}" {{ $user->hr_id == $hr->id ? 'selected' : '' }}>
                            {{ $hr->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- DROPDOWN PIMPINAN - Only for Pegawai --}}
            <div class="mb-3" id="pimpinanField">
                <label class="form-label">Pilih Pimpinan</label>
                <select name="pimpinan_id" id="pimpinanSelect" class="form-select">
                    <option value="">-- Pilih Pimpinan --</option>
                    @foreach($pimpinanList as $p)
                        <option value="{{ $p->id }}" {{ $user->pimpinan_id == $p->id ? 'selected' : '' }}>
                            {{ $p->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <script>
                function toggleDropdown() {
                    let role = document.getElementById('roleSelect').value;
                    let hrField = document.getElementById('hrField');
                    let pimpinanField = document.getElementById('pimpinanField');
                    let hrSelect = document.getElementById('hrSelect');
                    let pimpinanSelect = document.getElementById('pimpinanSelect');

                    if (role === 'pegawai') {
                        // Tampilkan field untuk Pegawai
                        hrField.style.display = 'block';
                        pimpinanField.style.display = 'block';
                        hrSelect.disabled = false;
                        pimpinanSelect.disabled = false;
                    } else {
                        // Sembunyikan field untuk HR, Pimpinan, dan Admin
                        hrField.style.display = 'none';
                        pimpinanField.style.display = 'none';
                        hrSelect.disabled = true;
                        pimpinanSelect.disabled = true;
                        
                        // Reset value
                        hrSelect.value = '';
                        pimpinanSelect.value = '';
                    }
                }

                toggleDropdown(); // panggil saat halaman dibuka

                document.getElementById('roleSelect').addEventListener('change', toggleDropdown);
            </script>

            <button type="submit" class="btn btn-success">Update</button>
            <a href="{{ route('admin.user.index') }}" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</div>
@endsection
