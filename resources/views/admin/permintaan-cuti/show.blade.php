@extends('admin.layouts.app')

@section('title', 'Detail Permintaan Cuti')

@section('content')
<div class="container py-4">

    <h3 class="fw-bold text-success mb-4">Detail Permintaan Cuti</h3>

    <a href="{{ route('admin.permintaan.cuti') }}" class="btn btn-outline-secondary mb-4">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>

    <div class="row g-3">

        <!-- Data Pegawai -->
        <div class="col-md-6">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header bg-success text-white fw-bold">
                    Data Pegawai
                </div>
                <div class="card-body">
                    <p class="mb-1"><strong>Nama:</strong> {{ $cuti->user->name }}</p>
                    <p class="mb-1"><strong>Email:</strong> {{ $cuti->user->email }}</p>
                    <p class="mb-0"><strong>Sisa Cuti:</strong> {{ $cuti->user->sisa_cuti }} Hari</p>
                </div>
            </div>
        </div>

        <!-- Data Cuti -->
        <div class="col-md-6">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header bg-success text-white fw-bold">
                    Informasi Cuti
                </div>
                <div class="card-body">
                    <p class="mb-1"><strong>Jenis:</strong> {{ $cuti->jenis_cuti }}</p>
                    <p class="mb-1"><strong>Mulai:</strong> {{ $cuti->tanggal_mulai }}</p>
                    <p class="mb-1"><strong>Selesai:</strong> {{ $cuti->tanggal_selesai }}</p>
                    <p class="mb-1"><strong>Lama:</strong> {{ $cuti->lama_cuti }} Hari</p>
                    <p class="mb-1"><strong>Alasan:</strong> {{ $cuti->alasan }}</p>
                    <p class="mt-2">
                        <strong>Status:</strong>
                        @if($cuti->status == 'pending')
                            <span class="badge bg-warning text-dark">Menunggu</span>
                        @elseif($cuti->status == 'disetujui')
                            <span class="badge bg-success">Disetujui</span>
                        @else
                            <span class="badge bg-danger">Ditolak</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>

    </div>

    <!-- Catatan -->
    <div class="card shadow-sm border-0 rounded-3 mt-4">
        <div class="card-header bg-success text-white fw-bold">
            Catatan
        </div>
        <div class="card-body">
            <p class="mb-2"><strong>Catatan HR:</strong><br>{{ $cuti->catatan_hr ?? '-' }}</p>
            <p class="mb-0"><strong>Catatan Pimpinan:</strong><br>{{ $cuti->catatan_pimpinan ?? '-' }}</p>
        </div>
    </div>

</div>
@endsection
