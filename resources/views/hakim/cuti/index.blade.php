@extends('hakim.layouts.app')

@section('title', 'Cuti Saya')

@section('content')
@php
    use App\Models\Cuti;
    use Illuminate\Support\Facades\Auth;
    use Carbon\Carbon;

    $user = Auth::user();
    $tahunIni = Carbon::now()->year;

    // Tampilkan SEMUA cuti (bukan hanya yang disetujui pimpinan) untuk riwayat lengkap
    $cutiTahunIni = Cuti::where('user_id', $user->id)
        ->whereYear('tanggal_mulai', $tahunIni)
        ->orderBy('created_at', 'desc')
        ->get();

    // Hitung total HANYA dari cuti yang sudah disetujui pimpinan
    $totalCutiTahunIni = Cuti::where('user_id', $user->id)
        ->whereYear('tanggal_mulai', $tahunIni)
        ->where('status', 'disetujui_pimpinan')
        ->sum('lama_cuti');

    $batasCuti = $user->sisa_cuti ?? 12;

    $sisaCuti = max(0, $batasCuti - $totalCutiTahunIni);
@endphp


<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold text-success">Cuti Saya</h3>
    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#cutiModal">
        <i class="bi bi-plus-circle"></i> Buat Cuti Baru
    </button>
</div>

{{-- Saldo Cuti Per Jenis --}}
<div class="row g-4 mt-4">
    <div class="col-12">
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-body">
                <h5 class="fw-bold text-success mb-4"><i class="bi bi-wallet2"></i> Saldo Cuti Tersedia</h5>
                
                @php
                    $jenisCutiInfo = [
                        ['key' => 'tahunan', 'label' => 'Cuti Tahunan', 'saldo' => $user->saldo_cuti_tahunan, 'icon' => 'calendar-check', 'color' => 'primary'],
                        ['key' => 'sakit', 'label' => 'Cuti Sakit', 'saldo' => null, 'icon' => 'heart-pulse', 'color' => 'danger', 'unlimited' => true],
                        ['key' => 'bersalin', 'label' => 'Cuti Bersalin', 'saldo' => $user->saldo_cuti_bersalin, 'icon' => 'person-hearts', 'color' => 'info'],
                        ['key' => 'penting', 'label' => 'Cuti Penting', 'saldo' => $user->saldo_cuti_penting, 'icon' => 'exclamation-circle', 'color' => 'warning'],
                        ['key' => 'besar', 'label' => 'Cuti Besar', 'saldo' => $user->saldo_cuti_besar, 'icon' => 'calendar3', 'color' => 'success']
                    ];
                @endphp
                
                <div class="row g-3">
                    {{-- Saldo Cuti Tahun Lalu (ditempatkan di sebelah KIRI Cuti Tahunan) --}}
                    <div class="col-md-4 col-lg-2">
                        <div class="card border-secondary h-100 bg-light">
                            <div class="card-body text-center">
                                <i class="bi bi-calendar-event text-secondary fs-3 mb-2"></i>
                                <h6 class="text-secondary mb-2 fw-bold">Sisa Tahun Lalu</h6>
                                <h4 class="fw-bold text-secondary mb-0">
                                    {{ $user->saldo_cuti_tahun_lalu ?? 0 }}
                                </h4>
                                <small class="text-muted">hari</small>
                                @if($user->saldo_cuti_tahun_lalu > 0)
                                    <div class="mt-2">
                                        <small class="badge bg-warning text-dark">Digunakan terlebih dahulu</small>
                                    </div>
                                @else
                                    <div class="mt-2">
                                        <small class="text-muted italic">Sudah habis</small>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    @foreach($jenisCutiInfo as $jenis)
                        <div class="col-md-4 col-lg-2">
                            <div class="card border-{{ $jenis['color'] }} h-100">
                                <div class="card-body text-center">
                                    <i class="bi bi-{{ $jenis['icon'] }} text-{{ $jenis['color'] }} fs-3 mb-2"></i>
                                    <h6 class="text-{{ $jenis['color'] }} mb-2">{{ $jenis['label'] }}</h6>
                                    @if(isset($jenis['unlimited']) && $jenis['unlimited'])
                                        <h4 class="fw-bold text-{{ $jenis['color'] }} mb-0">
                                            <i class="bi bi-infinity"></i>
                                        </h4>
                                        <small class="text-muted">Unlimited</small>
                                    @else
                                        <h4 class="fw-bold text-{{ $jenis['color'] }} mb-0">
                                            {{ $jenis['saldo'] ?? 0 }}
                                        </h4>
                                        <small class="text-muted">hari</small>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Cuti Terpakai Berdasarkan Kategori - Cards --}}
<div class="row g-4 mt-3">
    @php
        // Semua kategori cuti yang tersedia
        $allKategori = [
            'tahun_lalu' => 'Cuti Tahun Lalu',
            'tahunan' => 'Cuti Tahunan',
            'sakit' => 'Cuti Sakit',
            'bersalin' => 'Cuti Bersalin',
            'penting' => 'Cuti Penting',
            'besar' => 'Cuti Besar'
        ];
        
        // Ambil data cuti yang disetujui per kategori
        $cutiByKategori = \App\Models\Cuti::where('user_id', Auth::id())
            ->whereYear('tanggal_mulai', $tahunIni)
            ->where('status', 'disetujui_pimpinan')
            ->groupBy('jenis_cuti')
            ->selectRaw('jenis_cuti, SUM(lama_cuti) as total_hari, COUNT(*) as jumlah_pengajuan')
            ->get()
            ->keyBy('jenis_cuti');
    @endphp

    @foreach($allKategori as $key => $label)
        @php
            $data = $cutiByKategori->get($key);
            $totalHari = $data->total_hari ?? 0;
            $jumlahPengajuan = $data->jumlah_pengajuan ?? 0;
        @endphp
        <div class="col-md-4">
            <div class="card shadow-sm border-0 rounded-4 h-100">
                <div class="card-body">
                    <h6 class="text-muted mb-2">
                        <i class="bi bi-calendar-check"></i> {{ $label }}
                    </h6>
                    <h2 class="fw-bold text-success mb-2">{{ $totalHari }} Hari</h2>
                    <p class="mb-0 small text-muted">
                        <i class="bi bi-file-earmark-text"></i> {{ $jumlahPengajuan }} pengajuan tahun ini
                    </p>
                    @if($jumlahPengajuan > 0)
                        <small class="text-muted">
                            Rata-rata: {{ round($totalHari / $jumlahPengajuan, 1) }} hari/pengajuan
                        </small>
                    @endif
                </div>
            </div>
        </div>
    @endforeach
</div>

{{-- Riwayat Cuti --}}
<div class="row g-4 mt-3">
    <div class="col-12">
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">
                    <i class="bi bi-calendar-check"></i> Riwayat Pengajuan Cuti
                </h5>
            </div>
            <div class="card-body">
                @if ($dataCuti->isEmpty())
                    <div class="alert alert-info text-center">
                        <i class="bi bi-info-circle"></i> Tidak ada pengajuan cuti. 
                        <a href="#" data-bs-toggle="modal" data-bs-target="#cutiModal">Buat pengajuan baru</a>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>No</th>
                                    <th>Jenis Cuti</th>
                                    <th>Tanggal</th>
                                    <th>Lama</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($dataCuti as $cuti)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $cuti->jenis_cuti }}</td>
                                        <td>
                                            {{ \Carbon\Carbon::parse($cuti->tanggal_mulai)->format('d/m/Y') }} - 
                                            {{ \Carbon\Carbon::parse($cuti->tanggal_selesai)->format('d/m/Y') }}
                                        </td>
                                        <td>{{ $cuti->lama_cuti }} hari</td>
                                        <td>
                                            @php
                                                $statusLabel = match($cuti->status) {
                                                    'menunggu' => 'Menunggu Persetujuan',
                                                    'disetujui_hr' => 'Disetujui Sub Kepegawaian',
                                                    'disetujui_pimpinan' => 'Disetujui Pimpinan',
                                                    'ditolak' => 'Ditolak',
                                                    default => 'Unknown'
                                                };
                                                $statusColor = match($cuti->status) {
                                                    'menunggu' => 'warning',
                                                    'disetujui_hr' => 'info',
                                                    'disetujui_pimpinan' => 'success',
                                                    'ditolak' => 'danger',
                                                    default => 'secondary'
                                                };
                                            @endphp
                                            <span class="badge bg-{{ $statusColor }}">{{ $statusLabel }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Modal untuk form pengajuan cuti --}}
<div class="modal fade" id="cutiModal" tabindex="-1" aria-labelledby="cutiModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="cutiModalLabel">Ajukan Cuti Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('hakim.cuti.store') }}" method="POST" id="cutiForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="jenis_cuti" class="form-label">Jenis Cuti</label>
                        <select class="form-select @error('jenis_cuti') is-invalid @enderror" id="jenis_cuti" name="jenis_cuti" required>
                            <option value="">-- Pilih Jenis Cuti --</option>
                            <option value="tahunan">Cuti Tahunan</option>
                            <option value="sakit">Cuti Sakit</option>
                            <option value="bersalin">Cuti Bersalin</option>
                            <option value="penting">Cuti Penting</option>
                            <option value="besar">Cuti Besar</option>
                        </select>
                        @error('jenis_cuti')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="tanggal_mulai" class="form-label">Tanggal Mulai</label>
                        <input type="date" class="form-control @error('tanggal_mulai') is-invalid @enderror" id="tanggal_mulai" name="tanggal_mulai" required>
                        @error('tanggal_mulai')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="tanggal_selesai" class="form-label">Tanggal Selesai</label>
                        <input type="date" class="form-control @error('tanggal_selesai') is-invalid @enderror" id="tanggal_selesai" name="tanggal_selesai" required>
                        @error('tanggal_selesai')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="lama_cuti_display" class="form-label">Lama Cuti (hari kerja)</label>
                        <input type="number" class="form-control" id="lama_cuti_display" readonly>
                    </div>

                    <div class="mb-3">
                        <label for="alasan" class="form-label">Alasan</label>
                        <textarea class="form-control @error('alasan') is-invalid @enderror" id="alasan" name="alasan" rows="3" required></textarea>
                        @error('alasan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="alamat_selama_cuti" class="form-label">Alamat Selama Cuti</label>
                        <textarea class="form-control @error('alamat_selama_cuti') is-invalid @enderror" id="alamat_selama_cuti" name="alamat_selama_cuti" rows="2" required></textarea>
                        @error('alamat_selama_cuti')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="telp_selama_cuti" class="form-label">Telepon Selama Cuti</label>
                        <input type="text" class="form-control @error('telp_selama_cuti') is-invalid @enderror" id="telp_selama_cuti" name="telp_selama_cuti" required>
                        @error('telp_selama_cuti')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Upload Bukti untuk Cuti Sakit dan Bersalin -->
                    <div class="mb-3" id="bukti_file_section" style="display: none;">
                        <label for="bukti_file" class="form-label">Unggah Bukti Surat Dokter <span class="text-danger">*</span></label>
                        <input type="file" name="bukti_file" id="bukti_file" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                        <small class="text-muted">Format: PDF, JPG, PNG (Max: 2MB). Wajib untuk cuti sakit dan melahirkan.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-success">Ajukan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Hitung lama cuti berdasarkan tanggal mulai dan selesai (exclude weekends)
    document.getElementById('tanggal_mulai').addEventListener('change', calculateDays);
    document.getElementById('tanggal_selesai').addEventListener('change', calculateDays);
    
    // Show/hide bukti file based on jenis cuti
    document.getElementById('jenis_cuti').addEventListener('change', function() {
        const buktiSection = document.getElementById('bukti_file_section');
        const buktiFile = document.getElementById('bukti_file');
        
        if (this.value === 'sakit' || this.value === 'bersalin') {
            buktiSection.style.display = 'block';
            buktiFile.required = true;
        } else {
            buktiSection.style.display = 'none';
            buktiFile.required = false;
            buktiFile.value = '';
        }
    });

    function calculateDays() {
        const startDate = document.getElementById('tanggal_mulai').value;
        const endDate = document.getElementById('tanggal_selesai').value;

        if (!startDate || !endDate) return;

        const start = new Date(startDate);
        const end = new Date(endDate);

        let count = 0;
        const currentDate = new Date(start);

        while (currentDate <= end) {
            const dayOfWeek = currentDate.getDay();
            // Exclude Saturday (6) and Sunday (0)
            if (dayOfWeek !== 0 && dayOfWeek !== 6) {
                count++;
            }
            currentDate.setDate(currentDate.getDate() + 1);
        }

        document.getElementById('lama_cuti_display').value = count;
    }
</script>
@endsection
