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

    {{-- Jumlah Sub Kepegawaian --}}
    <div class="col-md-3">
        <div class="card shadow-sm border-0 rounded-4 text-center p-3">
            <div class="card-body">
                <h6 class="text-muted">Jumlah Sub Kepegawaian</h6>
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

{{-- Charts Section --}}
<div class="row g-4 mt-4">
    {{-- Chart Status Cuti --}}
    <div class="col-md-6">
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-body">
                <h5 class="fw-bold text-success mb-3">Status Pengajuan Cuti</h5>
                <canvas id="statusCutiChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Chart Distribusi User --}}
    <div class="col-md-6">
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-body">
                <h5 class="fw-bold text-success mb-3">Distribusi User Berdasarkan Role</h5>
                <canvas id="userRoleChart"></canvas>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Chart Status Cuti (Pie Chart)
    const statusCutiCtx = document.getElementById('statusCutiChart').getContext('2d');
    new Chart(statusCutiCtx, {
        type: 'pie',
        data: {
            labels: ['Pending', 'Diterima', 'Ditolak'],
            datasets: [{
                data: [{{ $cutiPending }}, {{ $cutiDiterima }}, {{ $cutiDitolak }}],
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

    // Chart Distribusi User (Doughnut Chart)
    const userRoleCtx = document.getElementById('userRoleChart').getContext('2d');
    new Chart(userRoleCtx, {
        type: 'doughnut',
        data: {
            labels: ['Karyawan', 'Sub Kepegawaian', 'Pimpinan', 'Admin'],
            datasets: [{
                data: [{{ $totalKaryawan }}, {{ $totalHR }}, {{ $totalPimpinan }}, {{ $totalAdmin }}],
                backgroundColor: ['#0d6efd', '#6c757d', '#212529', '#198754'],
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
</script>
@endpush
@endsection
