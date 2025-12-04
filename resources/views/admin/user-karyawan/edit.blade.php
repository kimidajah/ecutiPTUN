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

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">NIP</label>
                    <input type="text" name="nip" class="form-control" value="{{ $user->nip }}" placeholder="NIP Pegawai">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Jabatan</label>
                    <input type="text" name="jabatan" class="form-control" value="{{ $user->jabatan }}" placeholder="Jabatan">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Gol. Ruang</label>
                    <input type="text" name="gol_ruang" class="form-control" value="{{ $user->gol_ruang }}" placeholder="Contoh: III/a">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Unit Kerja</label>
                    <input type="text" name="unit_kerja" class="form-control" value="{{ $user->unit_kerja }}" placeholder="Unit Kerja">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Tanggal Masuk Kerja</label>
                <input type="date" name="tanggal_masuk" class="form-control" value="{{ $user->tanggal_masuk }}">
                <small class="form-text text-muted">Tanggal mulai bekerja untuk perhitungan masa kerja</small>
            </div>

            <div class="mb-3">
                <label class="form-label">Role</label>
                <select name="role" id="roleSelect" class="form-select" required>
                    <option value="pegawai" {{ $user->role == 'pegawai' ? 'selected' : '' }}>Pegawai</option>
                    <option value="hakim" {{ $user->role == 'hakim' ? 'selected' : '' }}>Hakim</option>
                    <option value="sub_kepegawaian" {{ $user->role == 'sub_kepegawaian' ? 'selected' : '' }}>Sub Kepegawaian</option>
                    <option value="ketua" {{ $user->role == 'ketua' ? 'selected' : '' }}>Ketua Divisi</option>
                    <option value="pimpinan" {{ $user->role == 'pimpinan' ? 'selected' : '' }}>Pimpinan</option>
                    <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
                </select>
            </div>

            {{-- DROPDOWN Sub Kepegawaian - For Pegawai & Hakim --}}
            <div class="mb-3" id="hrField">
                <label class="form-label">Pilih Sub Kepegawaian</label>
                <select name="hr_id" id="hrSelect" class="form-select">
                    <option value="">-- Pilih Sub Kepegawaian --</option>
                    @foreach($hrList as $hr)
                        <option value="{{ $hr->id }}" {{ $user->hr_id == $hr->id ? 'selected' : '' }}>
                            {{ $hr->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- DROPDOWN Ketua Divisi - Only for Pegawai --}}
            <div class="mb-3" id="ketuaField">
                <label class="form-label">Pilih Ketua Divisi</label>
                <select name="ketua_id" id="ketuaSelect" class="form-select">
                    <option value="">-- Pilih Ketua Divisi --</option>
                    @foreach($ketuaList as $ketua)
                        <option value="{{ $ketua->id }}" {{ $user->ketua_id == $ketua->id ? 'selected' : '' }}>
                            {{ $ketua->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- DROPDOWN PIMPINAN - For Pegawai & Hakim --}}
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
                    let ketuaField = document.getElementById('ketuaField');
                    let pimpinanField = document.getElementById('pimpinanField');
                    let hrSelect = document.getElementById('hrSelect');
                    let ketuaSelect = document.getElementById('ketuaSelect');
                    let pimpinanSelect = document.getElementById('pimpinanSelect');

                    if (role === 'pegawai') {
                        // Pegawai: HR + Ketua Divisi + Pimpinan
                        hrField.style.display = 'block';
                        ketuaField.style.display = 'block';
                        pimpinanField.style.display = 'block';
                        hrSelect.disabled = false;
                        ketuaSelect.disabled = false;
                        pimpinanSelect.disabled = false;
                    } else if (role === 'hakim') {
                        // Hakim: HR + Pimpinan (tanpa Ketua Divisi)
                        hrField.style.display = 'block';
                        ketuaField.style.display = 'none';
                        pimpinanField.style.display = 'block';
                        hrSelect.disabled = false;
                        ketuaSelect.disabled = true;
                        pimpinanSelect.disabled = false;
                        ketuaSelect.value = ''; // Reset ketua
                    } else {
                        // Role lain: sembunyikan semua
                        hrField.style.display = 'none';
                        ketuaField.style.display = 'none';
                        pimpinanField.style.display = 'none';
                        hrSelect.disabled = true;
                        ketuaSelect.disabled = true;
                        pimpinanSelect.disabled = true;
                        hrSelect.value = '';
                        ketuaSelect.value = '';
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
