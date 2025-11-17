@extends('pimpinan.layouts.app')

@section('title', 'Dashboard Pimpinan')

@section('content')
<h1 class="mb-4">Dashboard Pimpinan</h1>

<div class="row">

    {{-- Total Pengajuan Cuti --}}
    <div class="col-md-3 mb-3">
        <div class="card text-white bg-primary h-100">
            <div class="card-body">
                <h5 class="card-title">Total Pengajuan Cuti</h5>
                <p class="card-text display-6">{{ $totalCuti ?? 0 }}</p>
            </div>
        </div>
    </div>

    {{-- Pengajuan Pending --}}
    <div class="col-md-3 mb-3">
        <div class="card text-white bg-warning h-100">
            <div class="card-body">
                <h5 class="card-title">Pending</h5>
                <p class="card-text display-6">{{ $pendingCuti ?? 0 }}</p>
            </div>
        </div>
    </div>

    {{-- Pengajuan Disetujui --}}
    <div class="col-md-3 mb-3">
        <div class="card text-white bg-success h-100">
            <div class="card-body">
                <h5 class="card-title">Disetujui</h5>
                <p class="card-text display-6">{{ $approvedCuti ?? 0 }}</p>
            </div>
        </div>
    </div>

    {{-- Pengajuan Ditolak --}}
    <div class="col-md-3 mb-3">
        <div class="card text-white bg-danger h-100">
            <div class="card-body">
                <h5 class="card-title">Ditolak</h5>
                <p class="card-text display-6">{{ $rejectedCuti ?? 0 }}</p>
            </div>
        </div>
    </div>

</div>

<div class="mt-4">
    <a href="{{ route('pimpinan.cuti.index') }}" class="btn btn-primary">
        Kelola Pengajuan Cuti
    </a>
</div>
@endsection
