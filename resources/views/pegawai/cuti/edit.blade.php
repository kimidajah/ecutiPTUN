@extends('pegawai.layouts.app')

@section('title', 'Edit Pengajuan Cuti')

@section('content')
<div class="card shadow-sm border-0 rounded-4">
    <div class="card-header bg-success text-white rounded-top-4">
        <h5 class="mb-0"><i class="bi bi-pencil-square"></i> Edit Pengajuan Cuti</h5>
    </div>

    <div class="card-body">
        <form action="{{ route('pegawai.cuti.update', $cuti->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label class="form-label fw-semibold">Jenis Cuti</label>
                <select name="jenis_cuti" class="form-select" required>
                    <option value="tahunan" {{ $cuti->jenis_cuti == 'tahunan' ? 'selected' : '' }}>Cuti Tahunan (12 hari)</option>
                    <option value="sakit" {{ $cuti->jenis_cuti == 'sakit' ? 'selected' : '' }}>Cuti Sakit (Unlimited - butuh surat dokter)</option>
                    <option value="bersalin" {{ $cuti->jenis_cuti == 'bersalin' ? 'selected' : '' }}>Cuti Bersalin (90 hari)</option>
                    <option value="penting" {{ $cuti->jenis_cuti == 'penting' ? 'selected' : '' }}>Cuti Penting (12 hari)</option>
                    <option value="besar" {{ $cuti->jenis_cuti == 'besar' ? 'selected' : '' }}>Cuti Besar (60 hari)</option>
                </select>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Tanggal Mulai</label>
                    <input type="date" id="tanggal_mulai" name="tanggal_mulai" class="form-control"
                        value="{{ old('tanggal_mulai', $cuti->tanggal_mulai) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Tanggal Selesai</label>
                    <input type="date" id="tanggal_selesai" name="tanggal_selesai" class="form-control"
                        value="{{ old('tanggal_selesai', $cuti->tanggal_selesai) }}" required>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Lama Cuti (hari)</label>
                <input type="text" id="lama_cuti" class="form-control bg-light" readonly
                    value="{{ $cuti->lama_cuti }}">
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Keterangan</label>
                <textarea name="keterangan" class="form-control" rows="3" required>{{ old('keterangan', $cuti->alasan) }}</textarea>
            </div>

            <div class="row mb-3">
                <div class="col-md-8">
                    <label class="form-label fw-semibold">Alamat selama cuti</label>
                    <input type="text" name="alamat_selama_cuti" class="form-control" value="{{ old('alamat_selama_cuti', $cuti->alamat_selama_cuti) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Telp saat cuti</label>
                    <input type="text" name="telp_selama_cuti" class="form-control" value="{{ old('telp_selama_cuti', $cuti->telp_selama_cuti) }}">
                </div>
            </div>

            <div class="d-flex justify-content-end">
                <a href="{{ route('pegawai.cuti.show', $cuti->id) }}" class="btn btn-secondary me-2">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-save"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Script untuk hitung lama cuti otomatis (exclude weekend & hari libur) --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const tglMulai = document.getElementById('tanggal_mulai');
    const tglSelesai = document.getElementById('tanggal_selesai');
    const lamaCuti = document.getElementById('lama_cuti');

    function hitungHariKerja() {
        if (!tglMulai.value || !tglSelesai.value) {
            lamaCuti.value = '';
            return;
        }

        const mulai = new Date(tglMulai.value);
        const selesai = new Date(tglSelesai.value);

        if (selesai < mulai) {
            lamaCuti.value = '';
            return;
        }

        let hariKerja = 0;
        let currentDate = new Date(mulai);

        // Loop setiap hari dari tanggal mulai hingga selesai
        while (currentDate <= selesai) {
            // Cek apakah hari itu adalah weekend (Sabtu=6, Minggu=0)
            const dayOfWeek = currentDate.getDay();
            const isWeekend = (dayOfWeek === 0 || dayOfWeek === 6);

            // Jika bukan weekend, hitung sebagai hari kerja
            // (Hari libur nasional akan di-handle di backend)
            if (!isWeekend) {
                hariKerja++;
            }

            currentDate.setDate(currentDate.getDate() + 1);
        }

        lamaCuti.value = hariKerja + ' Hari';
    }

    tglMulai.addEventListener('change', hitungHariKerja);
    tglSelesai.addEventListener('change', hitungHariKerja);
});
</script>
@endsection
