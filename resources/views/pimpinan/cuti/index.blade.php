@extends('pimpinan.layouts.app')

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
            <th>Status</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        @forelse($dataCuti as $cuti)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $cuti->user->name }}</td>
                <td>{{ $cuti->jenis_cuti }}</td>
                <td>{{ \Carbon\Carbon::parse($cuti->tanggal_mulai)->format('d-m-Y') }}</td>
                <td>{{ \Carbon\Carbon::parse($cuti->tanggal_selesai)->format('d-m-Y') }}</td>
                <td>{{ $cuti->lama_cuti }} hari</td>
                <td>{{ $cuti->alasan }}</td>

                <td>
                    @if($cuti->status == 'disetujui_hr')
                        <span class="badge bg-warning text-dark">Menunggu Pimpinan</span>
                    @elseif($cuti->status == 'disetujui_pimpinan')
                        <span class="badge bg-success">Disetujui pimpinan</span>
                    @elseif($cuti->status == 'ditolak')
                        <span class="badge bg-danger">Ditolak</span>
                    @endif
                </td>

                <td>
                    {{-- Tombol SETUJU / TOLAK hanya muncul jika status masih 'disetujui_hr' --}}
                    @if($cuti->status == 'disetujui_hr')

                        {{-- Tombol Setuju --}}
                    <form action="{{ route('pimpinan.cuti.approve', $cuti->id) }}" method="POST" class="d-inline">
                        @csrf
                        <button class="btn btn-sm btn-success">Setuju</button>
                    </form>

                    <form action="{{ route('pimpinan.cuti.reject', $cuti->id) }}" method="POST" class="d-inline">
                        @csrf
                        <button class="btn btn-sm btn-danger">Tolak</button>
                    </form>


                    @endif
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
