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

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">NIP</label>
                    <input type="text" name="nip" class="form-control" placeholder="NIP Pegawai">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Jabatan</label>
                    <input type="text" name="jabatan" class="form-control" placeholder="Jabatan">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Gol. Ruang</label>
                    <input type="text" name="gol_ruang" class="form-control" placeholder="Contoh: III/a">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Unit Kerja</label>
                    <input type="text" name="unit_kerja" class="form-control" placeholder="Unit Kerja">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Tanggal Masuk Kerja</label>
                <input type="date" name="tanggal_masuk" class="form-control" value="{{ old('tanggal_masuk') }}">
                <small class="form-text text-muted">Tanggal mulai bekerja untuk perhitungan masa kerja</small>
            </div>

            <div class="mb-3">
                <label class="form-label">Role</label>
                <select name="role" id="role-select" class="form-select" required>
                    <option value="pegawai">Pegawai</option>
                    <option value="hakim">Hakim</option>
                    <option value="sub_kepegawaian">Sub Kepegawaian</option>
                    <option value="ketua">Ketua Divisi</option>
                    <option value="pimpinan">Pimpinan</option>
                    <option value="admin">Admin</option>
                </select>
            </div>

            {{-- Dropdown Sub Kepegawaian - For Pegawai & Hakim --}}
            <div class="mb-3" id="hr-field">
                <label class="form-label">Pilih Sub Kepegawaian</label>
                <select name="hr_id" id="select-hr" class="form-select">
                    <option value="">-- Pilih Sub Kepegawaian --</option>
                    @foreach($hrList as $hr)
                        <option value="{{ $hr->id }}">{{ $hr->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Dropdown Ketua Divisi - Only for Pegawai --}}
            <div class="mb-3" id="ketua-field">
                <label class="form-label">Pilih Ketua Divisi</label>
                <select name="ketua_id" id="select-ketua" class="form-select">
                    <option value="">-- Pilih Ketua Divisi --</option>
                    @foreach($ketuaList as $ketua)
                        <option value="{{ $ketua->id }}">{{ $ketua->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Dropdown Pimpinan - For Pegawai & Hakim --}}
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
    const ketuaField = document.getElementById('ketua-field');
    const pimpinanField = document.getElementById('pimpinan-field');
    const selectHr = document.getElementById('select-hr');
    const selectKetua = document.getElementById('select-ketua');
    const selectPimpinan = document.getElementById('select-pimpinan');

    function toggleDropdown() {
        const role = roleSelect.value;
        
        if (role === 'pegawai') {
            // Pegawai: HR + Ketua Divisi + Pimpinan
            hrField.style.display = 'block';
            ketuaField.style.display = 'block';
            pimpinanField.style.display = 'block';
            selectHr.disabled = false;
            selectKetua.disabled = false;
            selectPimpinan.disabled = false;
        } else if (role === 'hakim') {
            // Hakim: HR + Pimpinan (tanpa Ketua Divisi)
            hrField.style.display = 'block';
            ketuaField.style.display = 'none';
            pimpinanField.style.display = 'block';
            selectHr.disabled = false;
            selectKetua.disabled = true;
            selectPimpinan.disabled = false;
            selectKetua.value = ""; // Reset ketua
        } else {
            // Role lain: sembunyikan semua
            hrField.style.display = 'none';
            ketuaField.style.display = 'none';
            pimpinanField.style.display = 'none';
            selectHr.disabled = true;
            selectKetua.disabled = true;
            selectPimpinan.disabled = true;
            selectHr.value = "";
            selectKetua.value = "";
            selectPimpinan.value = "";
        }
    }

    // Jalankan saat halaman dibuka
    toggleDropdown();

    // Jalankan ketika role berubah
    roleSelect.addEventListener('change', toggleDropdown);
</script>

@endsection
