@extends('ketua.layouts.app')

@section('title', 'Persetujuan Cuti')

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
        @forelse($dataCuti as $cuti)
            @php
                $statusLabel = match($cuti->status) {
                    'disetujui_hr' => 'Menunggu Atasan Langsung',
                    'disetujui_ketua' => 'Menunggu Pimpinan',
                    'ditolak' => 'Ditolak',
                    default => 'Unknown'
                };
                $statusColor = match($cuti->status) {
                    'disetujui_hr' => 'warning',
                    'disetujui_ketua' => 'primary',
                    'ditolak' => 'danger',
                    default => 'secondary'
                };
            @endphp
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $cuti->user->name }}</td>
                <td>{{ $cuti->jenis_cuti }}</td>
                <td>{{ \Carbon\Carbon::parse($cuti->tanggal_mulai)->format('d-m-Y') }}</td>
                <td>{{ \Carbon\Carbon::parse($cuti->tanggal_selesai)->format('d-m-Y') }}</td>
                <td>{{ $cuti->lama_cuti }} hari</td>
                <td>{{ $cuti->alasan }}</td>
                <td>
                    @if($cuti->bukti_file)
                        <a href="{{ asset('storage/' . $cuti->bukti_file) }}" target="_blank" class="btn btn-info btn-sm">
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
                    @if ($cuti->status === 'disetujui_hr')
                        <form action="{{ route('ketua.cuti.approve', $cuti->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button class="btn btn-sm btn-success">Setuju</button>
                        </form>

                        <form action="{{ route('ketua.cuti.reject', $cuti->id) }}" method="POST" class="d-inline">
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
