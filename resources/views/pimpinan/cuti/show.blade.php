@extends('pimpinan.layouts.app')

@section('title', 'Detail Cuti')

@section('content')
<h1 class="mb-4">Detail Pengajuan Cuti</h1>

<div class="card">
    <div class="card-body">
        <h5 class="card-title">{{ $cuti->pegawai->nama }}</h5>
        <p><strong>Jenis Cuti:</strong> {{ $cuti->jenis_cuti }}</p>
        <p><strong>Tanggal Mulai:</strong> {{ \Carbon\Carbon::parse($cuti->tanggal_mulai)->format('d-m-Y') }}</p>
        <p><strong>Tanggal Selesai:</strong> {{ \Carbon\Carbon::parse($cuti->tanggal_selesai)->format('d-m-Y') }}</p>
        <p><strong>Lama Cuti:</strong> {{ $cuti->lama_cuti }} hari</p>
        <p><strong>Alasan:</strong> {{ $cuti->alasan }}</p>
        <p><strong>Status:</strong> 
            @if($cuti->status == 'pending')
                <span class="badge bg-warning text-dark">Menunggu</span>
            @elseif($cuti->status == 'approved')
                <span class="badge bg-success">Disetujui</span>
            @elseif($cuti->status == 'rejected')
                <span class="badge bg-danger">Ditolak</span>
            @endif
        </p>

        @if($cuti->status == 'pending')
        <form action="{{ route('pimpinan.cuti.approve', $cuti->id) }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-success">Setujui</button>
        </form>

        <form action="{{ route('pimpinan.cuti.reject', $cuti->id) }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-danger">Tolak</button>
        </form>
        @endif

        <a href="{{ route('pimpinan.cuti.index') }}" class="btn btn-secondary mt-3">Kembali</a>
    </div>
</div>
@endsection
