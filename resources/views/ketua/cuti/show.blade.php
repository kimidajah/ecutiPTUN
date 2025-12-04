@extends('ketua.layouts.app')

@section('title', 'Detail Cuti')

@section('content')
<div class="card shadow-sm border-0 rounded-4">
    <div class="card-header bg-success text-white">
        <h5 class="mb-0">
            <i class="bi bi-calendar-check"></i> Detail Pengajuan Cuti
        </h5>
    </div>
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-6">
                <h6 class="text-muted">Pegawai</h6>
                <p class="fw-bold">{{ $cuti->user->name }}</p>
            </div>
            <div class="col-md-6">
                <h6 class="text-muted">Jenis Cuti</h6>
                <p class="fw-bold">{{ ucfirst($cuti->jenis_cuti) }}</p>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <h6 class="text-muted">Tanggal Mulai</h6>
                <p class="fw-bold">{{ \Carbon\Carbon::parse($cuti->tanggal_mulai)->format('d M Y') }}</p>
            </div>
            <div class="col-md-6">
                <h6 class="text-muted">Tanggal Selesai</h6>
                <p class="fw-bold">{{ \Carbon\Carbon::parse($cuti->tanggal_selesai)->format('d M Y') }}</p>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <h6 class="text-muted">Lama Cuti</h6>
                <p class="fw-bold">{{ $cuti->lama_cuti }} hari kerja</p>
            </div>
            <div class="col-md-6">
                <h6 class="text-muted">Status</h6>
                <p>
                    @php
                        $statusLabel = match($cuti->status) {
                            'menunggu' => 'Menunggu Persetujuan',
                            'disetujui_hr' => 'Disetujui Sub Kepegawaian',
                            'disetujui_ketua' => 'Disetujui Ketua',
                            'disetujui_pimpinan' => 'Disetujui Pimpinan',
                            'ditolak' => 'Ditolak',
                            default => 'Unknown'
                        };
                        $statusColor = match($cuti->status) {
                            'menunggu' => 'warning',
                            'disetujui_hr' => 'info',
                            'disetujui_ketua' => 'success',
                            'disetujui_pimpinan' => 'success',
                            'ditolak' => 'danger',
                            default => 'secondary'
                        };
                    @endphp
                    <span class="badge bg-{{ $statusColor }}">{{ $statusLabel }}</span>
                </p>
            </div>
        </div>

        <div class="mb-3">
            <h6 class="text-muted">Alasan</h6>
            <p>{{ $cuti->alasan }}</p>
        </div>

        <div class="mb-3">
            <h6 class="text-muted">Alamat Selama Cuti</h6>
            <p>{{ $cuti->alamat_selama_cuti ?? '-' }}</p>
        </div>

        <div class="mb-3">
            <h6 class="text-muted">Telepon Selama Cuti</h6>
            <p>{{ $cuti->telp_selama_cuti ?? '-' }}</p>
        </div>

        @if($cuti->status == 'disetujui_hr')
        <div class="alert alert-info">
            <strong>Persetujuan Ketua Divisi:</strong>
            <p class="mb-0">Silakan setujui atau tolak pengajuan cuti ini.</p>
        </div>

        <form action="{{ route('ketua.cuti.approve', $cuti->id) }}" method="POST" class="d-inline">
            @csrf
            <textarea class="form-control mb-2" name="catatan_ketua" placeholder="Catatan (opsional)" rows="2"></textarea>
            <button type="submit" class="btn btn-success">
                <i class="bi bi-check-circle"></i> Setujui
            </button>
        </form>

        <form action="{{ route('ketua.cuti.reject', $cuti->id) }}" method="POST" class="d-inline">
            @csrf
            <textarea class="form-control mb-2" name="catatan_ketua" placeholder="Alasan penolakan" rows="2"></textarea>
            <button type="submit" class="btn btn-danger">
                <i class="bi bi-x-circle"></i> Tolak
            </button>
        </form>
        @else
        <p class="text-muted text-center">Pengajuan cuti sudah diproses.</p>
        @endif
    </div>
    <div class="card-footer">
        <a href="{{ route('ketua.cuti.index') }}" class="btn btn-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>
</div>
@endsection
