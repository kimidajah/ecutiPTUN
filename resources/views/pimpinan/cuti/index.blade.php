@extends('pimpinan.layouts.app')

@section('title', 'Persetujuan Cuti')

@section('content')
<h1 class="mb-4">Daftar Pengajuan Cuti</h1>

<table class="table table-striped table-bordered">
    <thead class="table-dark">
        <tr>
            <th>No</th>
            <th>Pegawai</th>
            <th>Jenis Cuti</th>
            <th>Tanggal Mulai</th>
            <th>Tanggal Selesai</th>
            <th>Lama</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        @forelse($dataCuti as $cuti)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $cuti->pegawai->nama }}</td>
                <td>{{ $cuti->jenis_cuti }}</td>
                <td>{{ \Carbon\Carbon::parse($cuti->tanggal_mulai)->format('d-m-Y') }}</td>
                <td>{{ \Carbon\Carbon::parse($cuti->tanggal_selesai)->format('d-m-Y') }}</td>
                <td>{{ $cuti->lama_cuti }} hari</td>
                <td>
                    @if($cuti->status == 'pending')
                        <span class="badge bg-warning text-dark">Menunggu</span>
                    @elseif($cuti->status == 'approved')
                        <span class="badge bg-success">Disetujui</span>
                    @elseif($cuti->status == 'rejected')
                        <span class="badge bg-danger">Ditolak</span>
                    @endif
                </td>
                <td>
                    <a href="{{ route('pimpinan.cuti.show', $cuti->id) }}" class="btn btn-sm btn-primary">
                        Detail
                    </a>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="8" class="text-center">Tidak ada pengajuan cuti.</td>
            </tr>
        @endforelse
    </tbody>
</table>
@endsection
