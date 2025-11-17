@if(isset($dataCuti) && count($dataCuti) > 0)
<div class="table-responsive">
    <table class="table table-hover align-middle">
        <thead>
            <tr>
                <th>Pegawai</th>
                <th>Jenis Cuti</th>
                <th>Tanggal</th>
                <th>Alasan</th>
                <th>Status</th>
                <th width="150">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($dataCuti as $c)
                <tr>
                    <td>{{ $c->pegawai->nama }}</td>
                    <td>{{ $c->jenis_cuti }}</td>
                    <td>{{ $c->tanggal_mulai }} s/d {{ $c->tanggal_selesai }}</td>
                    <td>{{ Str::limit($c->alasan, 40) }}</td>
                    <td>
                        <span class="badge bg-{{ $c->status_color }}">
                            {{ ucfirst($c->status) }}
                        </span>
                    </td>
                    <td>

                        {{-- Tombol Detail --}}
                        <a href="{{ route('hr.cuti.show', $c->id) }}" 
                           class="btn btn-sm btn-primary">
                            Detail
                        </a>

                        {{-- Tombol Approve --}}
                        @if($c->status == 'pending')
                        <form action="{{ route('hr.cuti.approve', $c->id) }}" 
                              method="POST" class="d-inline">
                            @csrf
                            <button type="submit" 
                                class="btn btn-sm btn-success">
                                Approve
                            </button>
                        </form>

                        {{-- Tombol Reject --}}
                        <form action="{{ route('hr.cuti.reject', $c->id) }}" 
                              method="POST" class="d-inline">
                            @csrf
                            <button type="submit" 
                                class="btn btn-sm btn-danger">
                                Reject
                            </button>
                        </form>
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
