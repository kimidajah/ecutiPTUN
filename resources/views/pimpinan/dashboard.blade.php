@extends('pimpinan.layouts.app')

@section('title', 'Dashboard Pimpinan')

@section('content')
<h1 class="mb-4">Dashboard Pimpinan</h1>

<div class="row">

    {{-- Total Pengajuan Cuti --}}
    <div class="col-md-3 mb-3">
        <div class="card shadow-sm border-0 rounded-4 text-center">
            <div class="card-body">
                <h6 class="text-muted mb-2">Total Pengajuan Cuti</h6>
                <h3 class="fw-bold text-success">{{ $totalCuti }}</h3>
            </div>
        </div>
    </div>

    {{-- Pengajuan Pending --}}
    <div class="col-md-3 mb-3">
        <div class="card shadow-sm border-0 rounded-4 text-center">
            <div class="card-body">
                <h6 class="text-muted mb-2">Pending</h6>
                <h3 class="fw-bold text-success">{{ $pendingCuti }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card shadow-sm border-0 rounded-4 text-center">
            <div class="card-body">
                <h6 class="text-muted mb-2">Disetujui</h6>
                <h3 class="fw-bold text-success">{{ $approvedCuti }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card shadow-sm border-0 rounded-4 text-center">
            <div class="card-body">
                <h6 class="text-muted mb-2">Ditolak</h6>
                <h3 class="fw-bold text-success">{{ $rejectedCuti }}</h3>
            </div>
        </div>
    </div>


</div>

        <div class="card shadow-sm border-0 rounded-4 text-center">
            <div class="card-body">
                <a href="{{ route('pimpinan.cuti.index') }}" class="btn btn-success">
                    Kelola Pengajuan Cuti
                </a>
            </div>
        </div>

@endsection
