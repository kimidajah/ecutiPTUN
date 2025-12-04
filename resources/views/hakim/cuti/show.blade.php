@extends('hakim.layouts.app')

@section('title', 'Detail Pengajuan Cuti')

@section('content')
<div class="row">
    <div class="col-md-10 mx-auto">
        <div class="card shadow-sm">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">
                    <i class="bi bi-file-text"></i> Detail Pengajuan Cuti
                </h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <h6 class="fw-bold">Jenis Cuti</h6>
                        <p>{{ $cuti->jenis_cuti }}</p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="fw-bold">Status</h6>
                        <p>
                            @switch($cuti->status)
                                @case('menunggu')
                                    <span class="badge bg-warning">Menunggu</span>
                                    @break
                                @case('disetujui_hr')
                                    <span class="badge bg-info">Disetujui Sub Kepegawaian</span>
                                    @break
                                @case('disetujui_pimpinan')
                                    <span class="badge bg-success">Disetujui</span>
                                    @break
                                @case('ditolak')
                                    <span class="badge bg-danger">Ditolak</span>
                                    @break
                            @endswitch
                        </p>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <h6 class="fw-bold">Tanggal Mulai</h6>
                        <p>{{ \Carbon\Carbon::parse($cuti->tanggal_mulai)->format('d/m/Y') }}</p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="fw-bold">Tanggal Selesai</h6>
                        <p>{{ \Carbon\Carbon::parse($cuti->tanggal_selesai)->format('d/m/Y') }}</p>
                    </div>
                </div>

                <div class="mb-3">
                    <h6 class="fw-bold">Lama Cuti</h6>
                    <p>{{ $cuti->lama_cuti }} hari</p>
                </div>

                <div class="mb-3">
                    <h6 class="fw-bold">Alasan/Keterangan</h6>
                    <p>{{ $cuti->alasan }}</p>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <h6 class="fw-bold">Alamat Selama Cuti</h6>
                        <p>{{ $cuti->alamat_selama_cuti }}</p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="fw-bold">Nomor Telepon</h6>
                        <p>{{ $cuti->telp_selama_cuti }}</p>
                    </div>
                </div>

                @if ($cuti->catatan_hr)
                    <div class="mb-3 alert alert-info">
                        <h6 class="fw-bold">Catatan HR</h6>
                        <p>{{ $cuti->catatan_hr }}</p>
                    </div>
                @endif

                @if ($cuti->catatan_pimpinan)
                    <div class="mb-3 alert alert-warning">
                        <h6 class="fw-bold">Catatan Pimpinan</h6>
                        <p>{{ $cuti->catatan_pimpinan }}</p>
                    </div>
                @endif

                <div class="d-grid gap-2 d-sm-flex justify-content-sm-end">
                    <a href="{{ route('hakim.cuti.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                    @if ($cuti->status === 'menunggu')
                        <a href="{{ route('hakim.cuti.edit', $cuti->id) }}" class="btn btn-warning">
                            <i class="bi bi-pencil"></i> Edit
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
