@if(isset($dataCuti) && count($dataCuti) > 0)
<div class="table-responsive">
    <table class="table table-hover align-middle">
        <thead>
            <tr>
                <th>Pegawai</th>
                <th>Jenis Cuti</th>
                <th>Tanggal</th>
                <th>Alasan</th>
                <th>Bukti</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($dataCuti as $c)
                @php
                    $start = \Carbon\Carbon::parse($c->tanggal_mulai);
                    $end = \Carbon\Carbon::parse($c->tanggal_selesai);
                    $lamaCuti = $start->diffInDays($end) + 1;
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
                    <td>{{ $c->user->name }}</td>
                    <td>{{ $c->jenis_cuti }}</td>
                    <td>{{ $c->tanggal_mulai }} s/d {{ $c->tanggal_selesai }} ({{ $lamaCuti }} hari)</td>
                    <td>{{ Str::limit($c->alasan, 40) }}</td>
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
                            <form action="{{ route('hr.cuti.approve', $c->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button class="btn btn-success btn-sm">Setuju</button>
                            </form>

                            <form action="{{ route('hr.cuti.reject', $c->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button class="btn btn-danger btn-sm">Tolak</button>
                            </form>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>

                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@else
<p class="text-muted">Belum ada pengajuan cuti.</p>
@endif
