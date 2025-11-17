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

{{-- Script untuk hitung lama cuti otomatis --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const tglMulai = document.getElementById('tanggal_mulai');
    const tglSelesai = document.getElementById('tanggal_selesai');
    const lamaCuti = document.getElementById('lama_cuti');

    function hitungLamaCuti() {
        const mulai = new Date(tglMulai.value);
        const selesai = new Date(tglSelesai.value);

        if (tglMulai.value && tglSelesai.value && selesai >= mulai) {
            const diffTime = Math.abs(selesai - mulai);
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1; // Tambah 1 agar inklusif
            lamaCuti.value = diffDays + ' Hari';
        } else {
            lamaCuti.value = '';
        }
    }

    tglMulai.addEventListener('change', hitungLamaCuti);
    tglSelesai.addEventListener('change', hitungLamaCuti);
});
</script>
@endsection
