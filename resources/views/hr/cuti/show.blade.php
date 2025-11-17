@extends('hr.layouts.app')

@section('title', 'Detail Cuti')

@section('content')

<h3 class="fw-bold mb-3">Detail Pengajuan Cuti</h3>

<div class="card shadow-sm border-0">
    <div class="card-body">

        <table class="table table-borderless">
            <tr>
                <th>Pegawai</th>
                <td>{{ $cuti->pegawai->nama }}</td>
            </tr>
            <tr>
                <th>Jenis Cuti</th>
                <td>{{ $cuti->jenis_cuti }}</td>
            </tr>
            <tr>
                <th>Tanggal</th>
                <td>{{ $cuti->tanggal_mulai }} s/d {{ $cuti->tanggal_selesai }}</td>
            </tr>
            <tr>
                <th>Alasan</th>
                <td>{{ $cuti->alasan }}</td>
            </tr>
            <tr>
                <th>Status</th>
                <td>
                    <span class="badge bg-{{ $cuti->status_color }}">
                        {{ ucfirst($cuti->status) }}
                    </span>
                </td>
            </tr>
        </table>

        @if($cuti->status == 'pending')
        <div class="mt-4">
            <form action="{{ route('hr.cuti.approve', $cuti->id) }}" method="POST" class="d-inline">
                @csrf
                <button class="btn btn-success">Setujui</button>
            </form>

            <form action="{{ route('hr.cuti.reject', $cuti->id) }}" method="POST" class="d-inline">
                @csrf
                <button class="btn btn-danger">Tolak</button>
            </form>
        </div>
        @endif

        <a href="{{ route('hr.cuti.index') }}" class="btn btn-secondary mt-3">
            Kembali
        </a>

    </div>
</div>

@endsection
