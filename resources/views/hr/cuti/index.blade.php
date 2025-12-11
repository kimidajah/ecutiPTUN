@extends('hr.layouts.app')

@section('title', 'Manajemen Cuti')

@section('content')
<h1 class="mb-4 fw-bold text-success">Daftar Pengajuan Cuti</h1>

<table class="table table-striped table-bordered shadow-sm">
    <thead class="table-success">
        <tr>
            <th>No</th>
            <th>Pegawai</th>
            <th>Jenis Cuti</th>
            <th>Tanggal Mulai</th>
            <th>Tanggal Selesai</th>
            <th>Lama</th>
            <th>Alasan</th>
            <th>Bukti</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        @forelse($dataCuti as $c)
            @php
                $statusLabel = match($c->status) {
                    'menunggu' => 'Menunggu',
                    'disetujui_hr' => 'Disetujui Sub Kepegawaian',
                    'disetujui_ketua' => 'Disetujui Ketua',
                    'disetujui_pimpinan' => 'Disetujui Pimpinan',
                    'ditolak' => 'Ditolak',
                    default => ucfirst($c->status)
                };

                $statusColor = match($c->status) {
                    'menunggu' => 'warning',
                    'disetujui_hr' => 'info',
                    'disetujui_ketua' => 'info',
                    'disetujui_pimpinan' => 'success',
                    'ditolak' => 'danger',
                    default => 'secondary'
                };
            @endphp
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $c->user->name }}</td>
                <td>{{ $c->jenis_cuti }}</td>
                <td>{{ \Carbon\Carbon::parse($c->tanggal_mulai)->format('d-m-Y') }}</td>
                <td>{{ \Carbon\Carbon::parse($c->tanggal_selesai)->format('d-m-Y') }}</td>
                <td>{{ $c->lama_cuti }} hari</td>
                <td>{{ $c->alasan }}</td>
                <td>
                    @if($c->bukti_file)
                        <a href="{{ asset('storage/' . $c->bukti_file) }}" target="_blank" class="btn btn-info btn-sm">
                            <i class="bi bi-file-earmark-text"></i> Lihat
                        </a>
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </td>

                <td>
                    <span class="badge bg-{{ $statusColor }}">{{ $statusLabel }}</span>
                </td>

                <td>
                    @if ($c->status === 'menunggu')
                        <form action="{{ route('hr.cuti.approve', $c->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button class="btn btn-sm btn-success">Setuju</button>
                        </form>

                        <form action="{{ route('hr.cuti.reject', $c->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button class="btn btn-sm btn-danger">Tolak</button>
                        </form>
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="10" class="text-center">Tidak ada pengajuan cuti.</td>
            </tr>
        @endforelse
    </tbody>
</table>
@endsection