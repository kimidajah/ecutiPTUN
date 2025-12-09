@extends('pegawai.layouts.app')

@section('title', 'Cuti Saya')

@section('content')
@php
    use App\Models\Cuti;
    use Illuminate\Support\Facades\Auth;
    use Carbon\Carbon;

    $user = Auth::user();
    $tahunIni = Carbon::now()->year;

    // Tampilkan SEMUA cuti (bukan hanya yang disetujui pimpinan) untuk riwayat lengkap
    $cutiTahunIni = Cuti::where('user_id', $user->id)
        ->whereYear('tanggal_mulai', $tahunIni)
        ->orderBy('created_at', 'desc')
        ->get();

    // Hitung total HANYA dari cuti yang sudah disetujui pimpinan
    $totalCutiTahunIni = Cuti::where('user_id', $user->id)
        ->whereYear('tanggal_mulai', $tahunIni)
        ->where('status', 'disetujui_pimpinan')
        ->sum('lama_cuti');

    $batasCuti = $user->sisa_cuti ?? 12;

    $sisaCuti = max(0, $batasCuti - $totalCutiTahunIni);
@endphp


<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold text-success">Cuti Saya</h3>
    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#cutiModal">
        <i class="bi bi-plus-circle"></i> Buat Cuti Baru
    </button>
</div>

{{-- Include komponen ringkasan --}}
@include('pegawai.cuti._summary', [
    'batasCuti' => $batasCuti,
    'totalCutiTahunIni' => $totalCutiTahunIni,
    'sisaCuti' => $sisaCuti
])

{{-- Saldo Cuti Per Jenis --}}
<div class="row g-4 mt-4">
    <div class="col-12">
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-body">
                <h5 class="fw-bold text-success mb-4"><i class="bi bi-wallet2"></i> Saldo Cuti Tersedia</h5>
                
                @php
                    $jenisCutiInfo = [
                        ['key' => 'tahunan', 'label' => 'Cuti Tahunan', 'saldo' => $user->saldo_cuti_tahunan, 'icon' => 'calendar-check', 'color' => 'primary'],
                        ['key' => 'sakit', 'label' => 'Cuti Sakit', 'saldo' => null, 'icon' => 'heart-pulse', 'color' => 'danger', 'unlimited' => true],
                        ['key' => 'bersalin', 'label' => 'Cuti Bersalin', 'saldo' => $user->saldo_cuti_bersalin, 'icon' => 'person-hearts', 'color' => 'info'],
                        ['key' => 'penting', 'label' => 'Cuti Penting', 'saldo' => $user->saldo_cuti_penting, 'icon' => 'exclamation-circle', 'color' => 'warning'],
                        ['key' => 'besar', 'label' => 'Cuti Besar', 'saldo' => $user->saldo_cuti_besar, 'icon' => 'calendar3', 'color' => 'success']
                    ];
                @endphp
                
                <div class="row g-3">
                    {{-- Saldo Cuti Tahun Lalu (ditempatkan di sebelah KIRI Cuti Tahunan) --}}
                    <div class="col-md-4 col-lg-2">
                        <div class="card border-secondary h-100 bg-light">
                            <div class="card-body text-center">
                                <i class="bi bi-calendar-event text-secondary fs-3 mb-2"></i>
                                <h6 class="text-secondary mb-2 fw-bold">Sisa Tahun Lalu</h6>
                                <h4 class="fw-bold text-secondary mb-0">
                                    {{ $user->saldo_cuti_tahun_lalu ?? 0 }}
                                </h4>
                                <small class="text-muted">hari</small>
                                @if($user->saldo_cuti_tahun_lalu > 0)
                                    <div class="mt-2">
                                        <small class="badge bg-warning text-dark">Digunakan terlebih dahulu</small>
                                    </div>
                                @else
                                    <div class="mt-2">
                                        <small class="text-muted italic">Sudah habis</small>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    @foreach($jenisCutiInfo as $jenis)
                        <div class="col-md-4 col-lg-2">
                            <div class="card border-{{ $jenis['color'] }} h-100">
                                <div class="card-body text-center">
                                    <i class="bi bi-{{ $jenis['icon'] }} text-{{ $jenis['color'] }} fs-3 mb-2"></i>
                                    <h6 class="text-{{ $jenis['color'] }} mb-2">{{ $jenis['label'] }}</h6>
                                    @if(isset($jenis['unlimited']) && $jenis['unlimited'])
                                        <h4 class="fw-bold text-{{ $jenis['color'] }} mb-0">
                                            <i class="bi bi-infinity"></i>
                                        </h4>
                                        <small class="text-muted">Unlimited</small>
                                    @else
                                        <h4 class="fw-bold text-{{ $jenis['color'] }} mb-0">
                                            {{ $jenis['saldo'] ?? 0 }}
                                        </h4>
                                        <small class="text-muted">hari</small>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Cuti Terpakai Berdasarkan Kategori - Cards --}}
<div class="row g-4 mt-3">
    @php
        // Semua kategori cuti yang tersedia
        $allKategori = [
            'tahun_lalu' => 'Cuti Tahun Lalu',
            'tahunan' => 'Cuti Tahunan',
            'sakit' => 'Cuti Sakit',
            'bersalin' => 'Cuti Bersalin',
            'penting' => 'Cuti Penting',
            'besar' => 'Cuti Besar'
        ];
        
        // Ambil data cuti yang disetujui per kategori
        $cutiByKategori = \App\Models\Cuti::where('user_id', Auth::id())
            ->whereYear('tanggal_mulai', $tahunIni)
            ->where('status', 'disetujui_pimpinan')
            ->groupBy('jenis_cuti')
            ->selectRaw('jenis_cuti, SUM(lama_cuti) as total_hari, COUNT(*) as jumlah_pengajuan')
            ->get()
            ->keyBy('jenis_cuti');
    @endphp

    @foreach($allKategori as $key => $label)
        @php
            $data = $cutiByKategori->get($key);
            $totalHari = $data->total_hari ?? 0;
            $jumlahPengajuan = $data->jumlah_pengajuan ?? 0;
        @endphp
        <div class="col-md-4">
            <div class="card shadow-sm border-0 rounded-4 h-100">
                <div class="card-body">
                    <h6 class="text-muted mb-2">
                        <i class="bi bi-calendar-check"></i> {{ $label }}
                    </h6>
                    <h2 class="fw-bold text-success mb-2">{{ $totalHari }} Hari</h2>
                    <p class="mb-0 small text-muted">
                        <i class="bi bi-file-earmark-text"></i> {{ $jumlahPengajuan }} pengajuan tahun ini
                    </p>
                    @if($jumlahPengajuan > 0)
                        <small class="text-muted">
                            Rata-rata: {{ round($totalHari / $jumlahPengajuan, 1) }} hari/pengajuan
                        </small>
                    @endif
                </div>
            </div>
        </div>
    @endforeach
</div>

{{-- Include tabel riwayat cuti --}}
@include('pegawai.cuti._table_history', [
    'cutiTahunIni' => $cutiTahunIni
])

{{-- Include modal form pengajuan cuti --}}
@include('pegawai.cuti._modal_form')
@endsection
