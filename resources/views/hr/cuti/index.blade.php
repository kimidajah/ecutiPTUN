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
                $isHakim = $c->user->role === 'hakim';
                $statusLabel = match($c->status) {
                    'menunggu' => 'Menunggu Sub Kepegawaian',
                    'disetujui_hr' => $isHakim ? 'Menunggu Pimpinan' : 'Menunggu Atasan Langsung',
                    'disetujui_ketua' => 'Menunggu Pimpinan',
                    'disetujui_pimpinan' => 'Disetujui Pimpinan',
                    'ditolak' => 'Ditolak',
                    default => ucfirst($c->status)
                };

                $statusColor = match($c->status) {
                    'menunggu' => 'warning',
                    'disetujui_hr' => $isHakim ? 'primary' : 'info',
                    'disetujui_ketua' => 'primary',
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
                        @if(!$c->atasan_id && $c->user->role === 'pegawai')
                            <!-- Tombol untuk membuka modal pilih atasan/pimpinan -->
                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#atasan{{ $c->id }}Modal">
                                <i class="bi bi-person-check"></i> Pilih Atasan
                            </button>
                        @elseif(!$c->pimpinan_id && $c->user->role === 'hakim')
                            <!-- Tombol untuk membuka modal pilih pimpinan -->
                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#atasan{{ $c->id }}Modal">
                                <i class="bi bi-person-check"></i> Pilih Pimpinan
                            </button>
                        @else
                            <!-- Sudah dipilih, tampilkan tombol approve/reject -->
                            <form action="{{ route('hr.cuti.approve', $c->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button class="btn btn-sm btn-success">Setuju</button>
                            </form>

                            <form action="{{ route('hr.cuti.reject', $c->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button class="btn btn-sm btn-danger">Tolak</button>
                            </form>
                        @endif
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

<!-- MODALS untuk setiap cuti -->
@forelse($dataCuti as $c)
    @if($c->status === 'menunggu')
    <div class="modal fade" id="atasan{{ $c->id }}Modal" tabindex="-1" aria-labelledby="atasan{{ $c->id }}Label" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="atasan{{ $c->id }}Label">
                        Pilih Atasan Langsung & Pimpinan - {{ $c->user->name }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('hr.cuti.set-atasan-pimpinan', $c->id) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        @php
                            $listAtasan = \App\Models\User::where('role', 'ketua')->get();
                            $listPimpinan = \App\Models\User::where('role', 'pimpinan')->get();
                        @endphp

                        @if($c->user->role === 'pegawai')
                        <!-- ATASAN LANGSUNG -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Pilih Atasan Langsung</label>
                            <div class="mb-2">
                                <small class="text-muted">Kategori:</small><br>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="kategori_atasan" id="atasan_nonplt{{ $c->id }}" value="Pejabat Definitif" checked>
                                    <label class="form-check-label" for="atasan_nonplt{{ $c->id }}">Pejabat Definitif</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="kategori_atasan" id="atasan_plt{{ $c->id }}" value="PLH">
                                    <label class="form-check-label" for="atasan_plt{{ $c->id }}">PLH</label>
                                </div>
                            </div>
                            <select name="atasan_id" class="form-select" required>
                                <option value="">-- Pilih Atasan Langsung --</option>
                                @foreach($listAtasan as $atasan)
                                    <option value="{{ $atasan->id }}">{{ $atasan->name }} ({{ $atasan->role }})</option>
                                @endforeach
                            </select>
                        </div>
                        @endif

                        <!-- PIMPINAN -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Pilih Pimpinan</label>
                            <div class="mb-2">
                                <small class="text-muted">Kategori:</small><br>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="kategori_pimpinan" id="pimpinan_nonplt{{ $c->id }}" value="Pejabat Definitif" checked>
                                    <label class="form-check-label" for="pimpinan_nonplt{{ $c->id }}">Pejabat Definitif</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="kategori_pimpinan" id="pimpinan_plt{{ $c->id }}" value="PLH">
                                    <label class="form-check-label" for="pimpinan_plt{{ $c->id }}">PLH</label>
                                </div>
                            </div>
                            <select name="pimpinan_id" class="form-select" required>
                                <option value="">-- Pilih Pimpinan --</option>
                                @foreach($listPimpinan as $pimpinan)
                                    <option value="{{ $pimpinan->id }}">{{ $pimpinan->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success">Simpan Pilihan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
@empty
@endforelse

@endsection
