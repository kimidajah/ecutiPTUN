@extends('pimpinan.layouts.app')

@section('title', 'Dashboard Pimpinan')

@section('content')

@php
    use App\Models\Cuti;
    
    // Hitung data untuk dashboard
    $totalCuti = Cuti::count();
    $pendingCuti = Cuti::where('status', 'disetujui_hr')->count(); // Menunggu approval pimpinan
    $approvedCuti = Cuti::where('status', 'disetujui_pimpinan')->count();
    $rejectedCuti = Cuti::where('status', 'ditolak')->count();
@endphp

<h1 class="mb-4">Dashboard Pimpinan</h1>

<div class="row">

    {{-- Total Pengajuan Cuti --}}
    <div class="col-md-3 mb-3">
        <div class="card shadow-sm border-0 rounded-4 text-center">
            <div class="card-body">
                <h6 class="text-muted mb-2">Total Pengajuan Cuti</h6>
                <h3 class="fw-bold text-primary">{{ $totalCuti }}</h3>
            </div>
        </div>
    </div>

    {{-- Pengajuan Pending --}}
    <div class="col-md-3 mb-3">
        <div class="card shadow-sm border-0 rounded-4 text-center">
            <div class="card-body">
                <h6 class="text-muted mb-2">Pending</h6>
                <h3 class="fw-bold text-warning">{{ $pendingCuti }}</h3>
            </div>
        </div>
    </div>
    
    {{-- Disetujui --}}
    <div class="col-md-3 mb-3">
        <div class="card shadow-sm border-0 rounded-4 text-center">
            <div class="card-body">
                <h6 class="text-muted mb-2">Disetujui</h6>
                <h3 class="fw-bold text-success">{{ $approvedCuti }}</h3>
            </div>
        </div>
    </div>
    
    {{-- Ditolak --}}
    <div class="col-md-3 mb-3">
        <div class="card shadow-sm border-0 rounded-4 text-center">
            <div class="card-body">
                <h6 class="text-muted mb-2">Ditolak</h6>
                <h3 class="fw-bold text-danger">{{ $rejectedCuti }}</h3>
            </div>
        </div>
    </div>

</div>

{{-- Charts Section --}}
<div class="row mt-4">
    {{-- Chart Status Cuti --}}
    <div class="col-md-6 mb-3">
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-body">
                <h5 class="fw-bold text-success mb-3">Status Pengajuan Cuti</h5>
                <canvas id="pimpinanCutiStatusChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Chart Perbandingan Status --}}
    <div class="col-md-6 mb-3">
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-body">
                <h5 class="fw-bold text-success mb-3">Perbandingan Status Cuti</h5>
                <canvas id="pimpinanCutiBarChart"></canvas>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Chart Status Cuti (Doughnut Chart)
    const pimpinanCutiStatusCtx = document.getElementById('pimpinanCutiStatusChart').getContext('2d');
    new Chart(pimpinanCutiStatusCtx, {
        type: 'doughnut',
        data: {
            labels: ['Pending', 'Disetujui', 'Ditolak'],
            datasets: [{
                data: [{{ $pendingCuti }}, {{ $approvedCuti }}, {{ $rejectedCuti }}],
                backgroundColor: ['#ffc107', '#28a745', '#dc3545'],
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // Chart Bar Comparison
    const pimpinanCutiBarCtx = document.getElementById('pimpinanCutiBarChart').getContext('2d');
    new Chart(pimpinanCutiBarCtx, {
        type: 'bar',
        data: {
            labels: ['Pending', 'Disetujui', 'Ditolak'],
            datasets: [{
                label: 'Jumlah Cuti',
                data: [{{ $pendingCuti }}, {{ $approvedCuti }}, {{ $rejectedCuti }}],
                backgroundColor: ['#ffc107', '#28a745', '#dc3545'],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
</script>
@endpush

@endsection
