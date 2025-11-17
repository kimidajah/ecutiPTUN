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
                <h2 class="fw-bold"> {{ $totalPegawai ?? 0 }} </h2>
            </div>
        </div>
    </div>

    {{-- Card: Pengajuan Cuti Pending --}}
    <div class="col-md-3">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h6 class="text-muted mb-1">Cuti Pending</h6>
                <h2 class="fw-bold text-warning"> {{ $cutiPending ?? 0 }} </h2>
            </div>
        </div>
    </div>

    {{-- Card: Cuti Disetujui Bulan Ini --}}
    <div class="col-md-3">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h6 class="text-muted mb-1">Cuti Disetujui</h6>
                <h2 class="fw-bold text-success"> {{ $cutiApprovedMonth ?? 0 }} </h2>
            </div>
        </div>
    </div>

    {{-- Card: Total Cuti Ditolak --}}
    <div class="col-md-3">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h6 class="text-muted mb-1">Cuti Ditolak</h6>
                <h2 class="fw-bold text-danger"> {{ $cutiRejected ?? 0 }} </h2>
            </div>
        </div>
    </div>

</div>

{{-- Tabel Pengajuan Cuti Terbaru --}}
<div class="row mt-5">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white">
                <h5 class="mb-0 fw-bold">Pengajuan Cuti Terbaru</h5>
            </div>
            <div class="card-body">

                @if(isset($latestCuti) && count($latestCuti) > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Pegawai</th>
                                    <th>Jenis Cuti</th>
                                    <th>Tanggal</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($latestCuti as $c)
                                    <tr>
                                        <td>{{ $c->pegawai->nama }}</td>
                                        <td>{{ $c->jenis_cuti }}</td>
                                        <td>{{ $c->tanggal_mulai }} s/d {{ $c->tanggal_selesai }}</td>
                                        <td>
                                            <span class="badge bg-{{ $c->status_color }}">
                                                {{ ucfirst($c->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted">Belum ada pengajuan cuti terbaru.</p>
                @endif

            </div>
        </div>
    </div>
</div>

@endsection
