@extends('admin.layouts.app')

@section('title', 'Permintaan Cuti')

@section('content')
<style>
    .view-fullscreen {
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .view-fullscreen .card {
        flex: 1 1 auto;
        display: flex;
        flex-direction: column;
        min-height: 0; 
    }

    .view-fullscreen .card .card-body {
        flex: 1 1 auto;
        min-height: 0;
        overflow: auto; 
    }

    .view-fullscreen .card { padding-bottom: 1rem; }
</style>

<div class="view-fullscreen">
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body">
        <h4 class="fw-bold text-success mb-4">Daftar Permintaan Cuti</h4>

        {{-- Alert sukses --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Tabel daftar cuti --}}
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-success">
                    <tr>
                        <th>No</th>
                        <th>Nama Pegawai</th>
                        <th>Tanggal Mulai</th>
                        <th>Tanggal Selesai</th>
                        <th>Alasan</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($permintaanCuti as $cuti)
                        <tr style="cursor: pointer;" 
                            onclick="window.location='{{ route('admin.permintaan.detail', $cuti->id) }}'">
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $cuti->user->name }}</td>
                            <td>{{ $cuti->tanggal_mulai }}</td>
                            <td>{{ $cuti->tanggal_selesai }}</td>
                            <td>{{ $cuti->alasan }}</td>
                            <td>
                                @if($cuti->status == 'menunggu')
                                    <span class="badge bg-warning">Menunggu</span>
                                @elseif($cuti->status == 'disetujui_hr')
                                    <span class="badge bg-warning">Disetujui Sub Kepegawaian</span>
                                @elseif($cuti->status == 'disetujui_pimpinan')
                                    <span class="badge bg-success">Disetujui</span>
                                @else
                                    <span class="badge bg-danger">Ditolak</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                Tidak ada permintaan cuti untuk saat ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        </div>
    </div>
</div>
@endsection
