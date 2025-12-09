@extends('hr.layouts.app')

@section('title', 'Dashboard Sub Kepegawaian')

@section('content')

@php
    use App\Models\Cuti;
    use App\Models\User;
    use Carbon\Carbon;
    
    // Hitung data untuk chart trend 6 bulan terakhir
    $months = [];
    $cutiCounts = [];
    for($i = 5; $i >= 0; $i--) {
        $date = Carbon::now()->subMonths($i);
        $months[] = $date->format('M Y');
        $cutiCounts[] = Cuti::whereYear('created_at', $date->year)
            ->whereMonth('created_at', $date->month)
            ->count();
    }
    
    // Hitung status cuti
    $cutiPending = Cuti::where('status', 'menunggu')->count();
    $cutiDisetujui = Cuti::whereIn('status', ['disetujui', 'disetujui_hr', 'disetujui_pimpinan'])->count();
    $cutiDitolak = Cuti::where('status', 'ditolak')->count();
@endphp

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
                <h2 class="fw-bold"> {{ User::where('role', 'pegawai')->count() }} </h2>
            </div>
        </div>
    </div>

    {{-- Card: Pengajuan Cuti Pending --}}
    <div class="col-md-3">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h6 class="text-muted mb-1">Cuti Pending</h6>
                <h2 class="fw-bold text-warning"> 
                    {{ $cutiPending }} 
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
                    {{ $cutiDisetujui }} 
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
                    {{ $cutiDitolak }} 
                </h2>
            </div>
        </div>
    </div>

</div>

{{-- Charts Section --}}
<div class="row g-4 mt-4">
    {{-- Chart Status Cuti --}}
    <div class="col-md-6">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h5 class="fw-bold text-success mb-3">Status Pengajuan Cuti</h5>
                <canvas id="hrCutiStatusChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Chart Trend Cuti per Bulan --}}
    <div class="col-md-6">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h5 class="fw-bold text-success mb-3">Trend Pengajuan Cuti (6 Bulan Terakhir)</h5>
                <canvas id="hrCutiTrendChart"></canvas>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Chart Status Cuti (Pie Chart)
    const hrCutiStatusCtx = document.getElementById('hrCutiStatusChart').getContext('2d');
    new Chart(hrCutiStatusCtx, {
        type: 'pie',
        data: {
            labels: ['Pending', 'Disetujui', 'Ditolak'],
            datasets: [{
                data: [{{ $cutiPending }}, {{ $cutiDisetujui }}, {{ $cutiDitolak }}],
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

    // Chart Trend Cuti (Bar Chart)
    const hrCutiTrendCtx = document.getElementById('hrCutiTrendChart').getContext('2d');
    
    new Chart(hrCutiTrendCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($months) !!},
            datasets: [{
                label: 'Jumlah Pengajuan Cuti',
                data: {!! json_encode($cutiCounts) !!},
                backgroundColor: '#74c69d',
                borderColor: '#52b788',
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
