<div class="card shadow-sm border-0 rounded-4">
    <div class="card-body">
        <h5 class="fw-bold text-success mb-3"><i class="bi bi-clock-history"></i> Riwayat Cuti Anda</h5>

        @if ($cutiTahunIni->isEmpty())
            <div class="alert alert-info mb-0">Belum ada riwayat cuti untuk tahun ini.</div>
        @else
            <table class="table table-hover align-middle mb-0">
                <thead class="table-success">
                    <tr>
                        <th>Tanggal Mulai</th>
                        <th>Tanggal Selesai</th>
                        <th>Lama Cuti</th>
                        <th>Keterangan</th>
                        <th>Bukti</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                @foreach ($cutiTahunIni as $cuti)
                <tr onclick="window.location='{{ route('pegawai.cuti.show', $cuti->id) }}'"
                    style="cursor: pointer;">
                    <td>{{ \Carbon\Carbon::parse($cuti->tanggal_mulai)->format('d M Y') }}</td>
                    <td>{{ \Carbon\Carbon::parse($cuti->tanggal_selesai)->format('d M Y') }}</td>
                    <td>{{ $cuti->lama_cuti }} Hari</td>
                    <td>{{ $cuti->alasan }}</td>
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
                            $statusLabel = match(strtolower($cuti->status)) {
                                'pending', 'menunggu' => 'Menunggu',
                                'disetujui_hr' => 'Disetujui Sub Kepegawaian',
                                'disetujui_ketua' => 'Disetujui Ketua',
                                'disetujui_pimpinan' => 'Disetujui Pimpinan',
                                'disetujui' => 'Disetujui',
                                'ditolak' => 'Ditolak',
                                default => ucfirst($cuti->status)
                            };
                            $statusColor = match(strtolower($cuti->status)) {
                                'pending', 'menunggu' => 'warning',
                                'disetujui_hr', 'disetujui_ketua' => 'info',
                                'disetujui_pimpinan', 'disetujui' => 'success',
                                'ditolak' => 'danger',
                                default => 'secondary'
                            };
                        @endphp
                        <span class="badge bg-{{ $statusColor }} text-dark">{{ $statusLabel }}</span>
                    </td>
                </tr>
                @endforeach
                </tbody>


            </table>
        @endif
    </div>
</div>
