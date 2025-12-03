@extends('pegawai.layouts.app')

@section('title', 'Dashboard Pegawai')

@section('content')
<div class="row g-4">

    {{-- 🧍 Kiri: Data Diri --}}
    <div class="col-md-5">
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-body text-center">
                {{-- Foto Profil --}}
                <div class="mb-3">
                    @if(Auth::user()->profile_photo_path ?? false)
                        <img src="{{ asset('storage/' . Auth::user()->profile_photo_path) }}" 
                             class="rounded-circle shadow-sm" 
                             alt="Foto Profil" 
                             width="120" height="120">
                    @else
                        <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=74c69d&color=fff&size=120" 
                             class="rounded-circle shadow-sm" 
                             alt="Default Avatar">
                    @endif
                </div>

                {{-- Data Diri --}}
                <h4 class="fw-bold text-success">{{ Auth::user()->name }}</h4>
                <p class="text-muted mb-1">{{ ucfirst(Auth::user()->role) }}</p>
                <p class="mb-1"><i class="bi bi-envelope"></i> {{ Auth::user()->email }}</p>
                <p class="mb-1"><i class="bi bi-telephone"></i> {{ Auth::user()->no_wa }}</p>
                <p class="text-muted small mb-0">
                    Akun dibuat <strong>{{ Auth::user()->created_at->diffForHumans() }}</strong>
                </p>
            </div>
        </div>
    </div>

    {{-- 📅 Kanan: Permintaan Cuti Hari Ini --}}
    <div class="col-md-7">
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-body">
                <h5 class="fw-bold text-success mb-3">
                    <i class="bi bi-calendar-check"></i> Permintaan Cuti Kamu Hari Ini
                </h5>

                @php
                    use App\Models\Cuti;
                    $cutiHariIni = Cuti::where('user_id', Auth::id())
                        ->whereDate('created_at', today())
                        ->get();
                @endphp

                @if($cutiHariIni->isEmpty())
                    <div class="alert alert-info mb-0">
                        Kamu belum mengajukan cuti hari ini.
                    </div>
                @else
                    <table class="table table-bordered align-middle mb-0">
                        <thead class="table-success">
                            <tr>
                                <th>Tanggal Cuti</th>
                                <th>Keterangan</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($cutiHariIni as $cuti)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($cuti->tanggal_mulai)->format('d M Y') }} -
                                        {{ \Carbon\Carbon::parse($cuti->tanggal_selesai)->format('d M Y') }}</td>
                                    <td>{{ $cuti->alasan }}</td>
                                    <td>
                                        @if ($cuti->status == 'menunggu')
                                            <span class="badge bg-warning">Pending</span>
                                        @elseif ($cuti->status == 'disetujui_hr')
                                            <span class="badge bg-warning">Disetujui hr</span>
                                        @elseif ($cuti->status == 'disetujui_pimpinan')
                                            <span class="badge bg-success">Disetujui pimpinan</span>
                                        @else
                                            <span class="badge bg-danger">Ditolak</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($cuti->status == 'disetujui_pimpinan')
                                            <a class="btn btn-sm btn-outline-success" href="{{ route('pegawai.cuti.surat', $cuti->id) }}">
                                                <i class="bi bi-download"></i> Unduh Surat
                                            </a>
                                        @else
                                            <button class="btn btn-sm btn-outline-secondary" disabled>
                                                <i class="bi bi-file-earmark-text"></i> Menunggu Persetujuan
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Charts Section --}}
<div class="row g-4 mt-4">
    {{-- Chart Saldo Cuti --}}
    <div class="col-md-6">
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-body">
                <h5 class="fw-bold text-success mb-3"><i class="bi bi-pie-chart"></i> Saldo Cuti</h5>
                <canvas id="pegawaiSaldoCutiChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Chart Status Pengajuan Cuti --}}
    <div class="col-md-6">
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-body">
                <h5 class="fw-bold text-success mb-3"><i class="bi bi-bar-chart"></i> Status Pengajuan Cuti Saya</h5>
                <canvas id="pegawaiStatusCutiChart"></canvas>
                @php
                    $myCutiPending = \App\Models\Cuti::where('user_id', Auth::id())->where('status', 'menunggu')->count();
                    $myCutiApproved = \App\Models\Cuti::where('user_id', Auth::id())->whereIn('status', ['disetujui', 'disetujui_hr', 'disetujui_pimpinan'])->count();
                    $myCutiRejected = \App\Models\Cuti::where('user_id', Auth::id())->where('status', 'ditolak')->count();
                @endphp
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Chart Saldo Cuti (Doughnut)
    const pegawaiSaldoCtx = document.getElementById('pegawaiSaldoCutiChart').getContext('2d');
    
    @php
        use Carbon\Carbon;
        $user = Auth::user();
        $tahunIni = Carbon::now()->year;
        
        // Hitung cuti yang sudah disetujui pimpinan tahun ini
        $cutiDiambilTahunIni = \App\Models\Cuti::where('user_id', $user->id)
            ->whereYear('tanggal_mulai', $tahunIni)
            ->where('status', 'disetujui_pimpinan')
            ->sum('lama_cuti');
        
        $saldoCutiTahunan = $user->saldo_cuti_tahunan ?? 12;
        $sisaCutiTersedia = max(0, $saldoCutiTahunan - $cutiDiambilTahunIni);
    @endphp
    
    const saldoCuti = {{ $saldoCutiTahunan }};
    const cutiTerpakai = {{ $cutiDiambilTahunIni }};
    const sisaCuti = {{ $sisaCutiTersedia }};
    
    new Chart(pegawaiSaldoCtx, {
        type: 'doughnut',
        data: {
            labels: ['Cuti Terpakai', 'Sisa Cuti'],
            datasets: [{
                data: [cutiTerpakai, sisaCuti],
                backgroundColor: ['#dc3545', '#28a745'],
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.label + ': ' + context.parsed + ' hari';
                        }
                    }
                }
            }
        }
    });

    // Chart Status Cuti (Bar)
    const pegawaiStatusCtx = document.getElementById('pegawaiStatusCutiChart').getContext('2d');
    new Chart(pegawaiStatusCtx, {
        type: 'bar',
        data: {
            labels: ['Pending', 'Disetujui', 'Ditolak'],
            datasets: [{
                label: 'Jumlah Pengajuan',
                data: [{{ $myCutiPending }}, {{ $myCutiApproved }}, {{ $myCutiRejected }}],
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
