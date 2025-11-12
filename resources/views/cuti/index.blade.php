@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h3>Daftar Pengajuan Cuti Saya</h3>
    <a href="{{ route('cuti.create') }}" class="btn btn-primary mb-3">Ajukan Cuti</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Jenis Cuti</th>
                <th>Tanggal</th>
                <th>Alasan</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($cuti as $c)
            <tr>
                <td>{{ $c->jenis_cuti }}</td>
                <td>{{ $c->tanggal_mulai }} - {{ $c->tanggal_selesai }}</td>
                <td>{{ $c->alasan }}</td>
                <td>
                    @if($c->status == 'menunggu')
                        <span class="badge bg-warning text-dark">Menunggu</span>
                    @elseif($c->status == 'disetujui_hr')
                        <span class="badge bg-info text-dark">Disetujui HR</span>
                    @elseif($c->status == 'disetujui_pimpinan')
                        <span class="badge bg-success">Disetujui Pimpinan</span>
                    @else
                        <span class="badge bg-danger">Ditolak</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="4" class="text-center">Belum ada pengajuan cuti.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
