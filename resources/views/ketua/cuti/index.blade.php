@extends('ketua.layouts.app')

@section('title', 'Persetujuan Cuti')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold text-success">Daftar Pengajuan Cuti</h3>
</div>

<div class="card shadow-sm border-0 rounded-4">
    <div class="card-header bg-success text-white">
        <h5 class="mb-0">
            <i class="bi bi-calendar-check"></i> Cuti Menunggu Persetujuan Ketua
        </h5>
    </div>
    <div class="card-body">
        @if($dataCuti->isEmpty())
            <div class="alert alert-info text-center mb-0">
                <i class="bi bi-info-circle"></i> Tidak ada pengajuan cuti menunggu persetujuan.
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Pegawai</th>
                            <th>Jenis Cuti</th>
                            <th>Tanggal</th>
                            <th>Lama</th>
                            <th>Alasan</th>
                            <th>Bukti</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($dataCuti as $cuti)
                            <tr onclick="window.location='{{ route('ketua.cuti.show', $cuti->id) }}'" style="cursor: pointer;">
                                <td>{{ $loop->iteration }}</td>
                                <td><strong>{{ $cuti->user->name }}</strong></td>
                                <td>{{ ucfirst($cuti->jenis_cuti) }}</td>
                                <td>
                                    {{ \Carbon\Carbon::parse($cuti->tanggal_mulai)->format('d M Y') }} -
                                    {{ \Carbon\Carbon::parse($cuti->tanggal_selesai)->format('d M Y') }}
                                </td>
                                <td><span class="badge bg-light text-dark">{{ $cuti->lama_cuti }} hari</span></td>
                                <td>{{ Str::limit($cuti->alasan, 40) }}</td>
                                <td onclick="event.stopPropagation()">
                                    @if($cuti->bukti_file)
                                        <a href="{{ asset('storage/' . $cuti->bukti_file) }}" target="_blank" class="btn btn-info btn-sm">
                                            <i class="bi bi-file-earmark-text"></i> Lihat
                                        </a>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $statusLabel = match($cuti->status) {
                                            'disetujui_hr' => 'Menunggu Ketua',
                                            'disetujui_ketua' => 'Disetujui Ketua',
                                            'ditolak' => 'Ditolak',
                                            default => 'Unknown'
                                        };
                                        $statusColor = match($cuti->status) {
                                            'disetujui_hr' => 'warning',
                                            'disetujui_ketua' => 'success',
                                            'ditolak' => 'danger',
                                            default => 'secondary'
                                        };
                                    @endphp
                                    <span class="badge bg-{{ $statusColor }}">{{ $statusLabel }}</span>
                                </td>
                                <td onclick="event.stopPropagation()">
                                    @if($cuti->status == 'disetujui_hr')
                                        <a href="{{ route('ketua.cuti.show', $cuti->id) }}" class="btn btn-sm btn-outline-success">
                                            <i class="bi bi-eye"></i> Lihat
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted">Tidak ada pengajuan cuti.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
