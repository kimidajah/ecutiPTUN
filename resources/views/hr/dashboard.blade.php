@extends('hr.layouts.app')

@section('title', 'Dashboard HR')

@section('content')

<div class="row mb-4">
    <div class="col-12">
        <h3 class="fw-bold">Dashboard HR</h3>
        <p class="text-muted">Ringkasan aktivitas dan data kepegawaian.</p>
    </div>
</div>

<div class="row g-4">

    {{-- Card: Total Pegawai --}}
    <div class="col-md-3">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h6 class="text-muted mb-1">Total Pegawai</h6>
                <h2 class="fw-bold"> {{ \App\Models\User::where('role', 'pegawai')->count() }} </h2>
            </div>
        </div>
    </div>

    {{-- Card: Pengajuan Cuti Pending --}}
    <div class="col-md-3">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h6 class="text-muted mb-1">Cuti Pending</h6>
                <h2 class="fw-bold text-warning"> 
                    {{ \App\Models\Cuti::where('status', 'menunggu')->count() }} 
                </h2>
            </div>
        </div>
    </div>

    {{-- Card: Cuti Disetujui --}}
    <div class="col-md-3">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h6 class="text-muted mb-1">Cuti Disetujui</h6>
                <h2 class="fw-bold text-success"> 
                    {{ \App\Models\Cuti::where('status', 'disetujui')->count() }} 
                </h2>
            </div>
        </div>
    </div>

    {{-- Card: Cuti Ditolak --}}
    <div class="col-md-3">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h6 class="text-muted mb-1">Cuti Ditolak</h6>
                <h2 class="fw-bold text-danger"> 
                    {{ \App\Models\Cuti::where('status', 'ditolak')->count() }} 
                </h2>
            </div>
        </div>
    </div>

</div>

@endsection
