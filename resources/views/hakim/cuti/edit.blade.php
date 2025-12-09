@extends('hakim.layouts.app')

@section('title', 'Edit Pengajuan Cuti')

@section('content')
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card shadow-sm">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0">
                    <i class="bi bi-pencil"></i> Edit Pengajuan Cuti
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('hakim.cuti.update', $cuti->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="jenis_cuti" class="form-label">Jenis Cuti</label>
                        <select name="jenis_cuti" id="jenis_cuti" class="form-control @error('jenis_cuti') is-invalid @enderror" required>
                            <option value="">-- Pilih Jenis Cuti --</option>
                            <option value="tahunan" {{ $cuti->jenis_cuti == 'tahunan' ? 'selected' : '' }}>Cuti Tahunan (12 hari)</option>
                            <option value="sakit" {{ $cuti->jenis_cuti == 'sakit' ? 'selected' : '' }}>Cuti Sakit (Unlimited - butuh surat dokter)</option>
                            <option value="bersalin" {{ $cuti->jenis_cuti == 'bersalin' ? 'selected' : '' }}>Cuti Bersalin (90 hari)</option>
                            <option value="penting" {{ $cuti->jenis_cuti == 'penting' ? 'selected' : '' }}>Cuti Penting (12 hari)</option>
                            <option value="besar" {{ $cuti->jenis_cuti == 'besar' ? 'selected' : '' }}>Cuti Besar (60 hari)</option>
                        </select>
                        @error('jenis_cuti')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="tanggal_mulai" class="form-label">Tanggal Mulai</label>
                                <input type="date" name="tanggal_mulai" id="tanggal_mulai" class="form-control @error('tanggal_mulai') is-invalid @enderror" value="{{ $cuti->tanggal_mulai }}" required>
                                @error('tanggal_mulai')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="tanggal_selesai" class="form-label">Tanggal Selesai</label>
                                <input type="date" name="tanggal_selesai" id="tanggal_selesai" class="form-control @error('tanggal_selesai') is-invalid @enderror" value="{{ $cuti->tanggal_selesai }}" required>
                                @error('tanggal_selesai')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="alasan" class="form-label">Alasan/Keterangan</label>
                        <textarea name="alasan" id="alasan" class="form-control @error('alasan') is-invalid @enderror" rows="3" required>{{ $cuti->alasan }}</textarea>
                        @error('alasan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="alamat_selama_cuti" class="form-label">Alamat Selama Cuti</label>
                        <input type="text" name="alamat_selama_cuti" id="alamat_selama_cuti" class="form-control @error('alamat_selama_cuti') is-invalid @enderror" value="{{ $cuti->alamat_selama_cuti }}" required>
                        @error('alamat_selama_cuti')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="telp_selama_cuti" class="form-label">Nomor Telepon Selama Cuti</label>
                        <input type="text" name="telp_selama_cuti" id="telp_selama_cuti" class="form-control @error('telp_selama_cuti') is-invalid @enderror" value="{{ $cuti->telp_selama_cuti }}" required>
                        @error('telp_selama_cuti')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Upload Bukti untuk Cuti Sakit dan Bersalin -->
                    <div class="mb-3" id="bukti_file_section_edit" style="display: {{ in_array($cuti->jenis_cuti, ['sakit', 'bersalin']) ? 'block' : 'none' }};">
                        <label for="bukti_file_edit" class="form-label">Unggah Bukti Surat Dokter <span class="text-danger">*</span></label>
                        @if($cuti->bukti_file)
                            <div class="mb-2">
                                <a href="{{ asset('storage/' . $cuti->bukti_file) }}" target="_blank" class="btn btn-sm btn-info">
                                    <i class="bi bi-file-earmark-text"></i> Lihat Bukti Saat Ini
                                </a>
                            </div>
                        @endif
                        <input type="file" name="bukti_file" id="bukti_file_edit" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                        <small class="text-muted">Format: PDF, JPG, PNG (Max: 2MB). Kosongkan jika tidak ingin mengubah.</small>
                    </div>

                    <div class="d-grid gap-2 d-sm-flex justify-content-sm-end">
                        <a href="{{ route('hakim.cuti.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Kembali
                        </a>
                        <button type="submit" class="btn btn-warning">
                            <i class="bi bi-check-circle"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Show/hide bukti file based on jenis cuti
    document.getElementById('jenis_cuti').addEventListener('change', function() {
        const buktiSection = document.getElementById('bukti_file_section_edit');
        const buktiFile = document.getElementById('bukti_file_edit');
        
        if (this.value === 'sakit' || this.value === 'bersalin') {
            buktiSection.style.display = 'block';
            buktiFile.required = false; // Optional in edit mode
        } else {
            buktiSection.style.display = 'none';
            buktiFile.required = false;
            buktiFile.value = '';
        }
    });
</script>

@endsection
