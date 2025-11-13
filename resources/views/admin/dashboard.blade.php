@extends('admin.layouts.app')

@section('title', 'Dashboard Admin')

@section('content')
<div class="card shadow-sm rounded-4 border-0 mb-4">
    <div class="card-body">
        <h3 class="fw-bold text-success">Selamat Datang, {{ Auth::user()->name }}!</h3>
            <p class="text-muted mb-0">
                Akun ini dibuat <strong>{{ Auth::user()->created_at->diffForHumans() }}</strong>
            </p>
    </div>
</div>

<div class="row g-4">
    {{-- Jumlah User --}}
    <div class="col-md-3">
        <div class="card shadow-sm border-0 rounded-4 text-center p-3">
            <div class="card-body">
                <h6 class="text-muted">Total User</h6>
                <h3 class="fw-bold text-success">{{ $totalUser }}</h3>
            </div>
        </div>
    </div>

    {{-- Jumlah HR --}}
    <div class="col-md-3">
        <div class="card shadow-sm border-0 rounded-4 text-center p-3">
            <div class="card-body">
                <h6 class="text-muted">Jumlah HR</h6>
                <h3 class="fw-bold text-secondary">{{ $totalHR }}</h3>
            </div>
        </div>
    </div>

    {{-- Jumlah Pimpinan --}}
    <div class="col-md-3">
        <div class="card shadow-sm border-0 rounded-4 text-center p-3">
            <div class="card-body">
                <h6 class="text-muted">Jumlah Pimpinan</h6>
                <h3 class="fw-bold text-dark">{{ $totalPimpinan }}</h3>
            </div>
        </div>
    </div>


    {{-- Jumlah Karyawan --}}
    <div class="col-md-3">
        <div class="card shadow-sm border-0 rounded-4 text-center p-3">
            <div class="card-body">
                <h6 class="text-muted">Jumlah Karyawan</h6>
                <h3 class="fw-bold text-primary">{{ $totalKaryawan }}</h3>
            </div>
        </div>
    </div>

    {{-- Total Permintaan Cuti --}}
    <div class="col-md-3">
        <div class="card shadow-sm border-0 rounded-4 text-center p-3">
            <div class="card-body">
                <h6 class="text-muted">Total Permintaan Cuti</h6>
                <h3 class="fw-bold text-info">{{ $totalCuti }}</h3>
            </div>
        </div>
    </div>

    {{-- Pending --}}
    <div class="col-md-3">
        <div class="card shadow-sm border-0 rounded-4 text-center p-3">
            <div class="card-body">
                <h6 class="text-muted">Cuti Pending</h6>
                <h3 class="fw-bold text-warning">{{ $cutiPending }}</h3>
            </div>
        </div>
    </div>

    {{-- Diterima --}}
    <div class="col-md-3">
        <div class="card shadow-sm border-0 rounded-4 text-center p-3">
            <div class="card-body">
                <h6 class="text-muted">Cuti Diterima</h6>
                <h3 class="fw-bold text-success">{{ $cutiDiterima }}</h3>
            </div>
        </div>
    </div>

    {{-- Ditolak --}}
    <div class="col-md-3">
        <div class="card shadow-sm border-0 rounded-4 text-center p-3">
            <div class="card-body">
                <h6 class="text-muted">Cuti Ditolak</h6>
                <h3 class="fw-bold text-danger">{{ $cutiDitolak }}</h3>
            </div>
        </div>
    </div>
</div>
@endsection
