@extends('pegawai.layouts.app')

@section('title', 'Detail Cuti')

@section('content')
<div class="card shadow-sm border-0 rounded-4">
    <div class="card-body">
        <h4 class="fw-bold text-success">Detail Pengajuan Cuti</h4>
        <p><strong>Tanggal:</strong> {{ $cuti->tanggal_mulai }} - {{ $cuti->tanggal_selesai }}</p>
        <p><strong>Lama Cuti:</strong> {{ $cuti->lama_cuti }} Hari</p>
        <p><strong>Keterangan:</strong> {{ $cuti->alasan }}</p>
        <p><strong>Status:</strong> 
            @php
                $statusLabel = match($cuti->status) {
                    'menunggu' => 'Menunggu Persetujuan',
                    'disetujui_hr' => 'Disetujui Sub Kepegawaian',
                    'disetujui_ketua' => 'Disetujui Ketua',
                    'disetujui_pimpinan' => 'Disetujui Pimpinan',
                    'ditolak' => 'Ditolak',
                    default => ucfirst($cuti->status)
                };
            @endphp
            {{ $statusLabel }}
        </p>

        <div class="mt-3">
            <a href="{{ route('pegawai.cuti.edit', $cuti->id) }}" class="btn btn-warning btn-sm">Edit</a>
            <form action="{{ route('pegawai.cuti.destroy', $cuti->id) }}" method="POST" class="d-inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Hapus data cuti ini?')">Hapus</button>
            </form>
            <a href="{{ route('pegawai.cuti.index') }}" class="btn btn-secondary btn-sm">Kembali</a>
        </div>
    </div>
</div>
@endsection
