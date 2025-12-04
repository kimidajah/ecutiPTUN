@extends('hakim.layouts.app')

@section('title', 'Ajukan Cuti Baru')

@section('content')
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card shadow-sm">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">
                    <i class="bi bi-file-earmark-plus"></i> Ajukan Cuti Baru
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('hakim.cuti.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="jenis_cuti" class="form-label">Jenis Cuti</label>
                        <select name="jenis_cuti" id="jenis_cuti" class="form-control @error('jenis_cuti') is-invalid @enderror" required>
                            <option value="">-- Pilih Jenis Cuti --</option>
                            <option value="tahunan" {{ old('jenis_cuti') == 'tahunan' ? 'selected' : '' }}>Cuti Tahunan (12 hari)</option>
                            <option value="sakit" {{ old('jenis_cuti') == 'sakit' ? 'selected' : '' }}>Cuti Sakit (Unlimited - butuh surat dokter)</option>
                            <option value="bersalin" {{ old('jenis_cuti') == 'bersalin' ? 'selected' : '' }}>Cuti Bersalin (90 hari)</option>
                            <option value="penting" {{ old('jenis_cuti') == 'penting' ? 'selected' : '' }}>Cuti Penting (12 hari)</option>
                            <option value="besar" {{ old('jenis_cuti') == 'besar' ? 'selected' : '' }}>Cuti Besar (60 hari)</option>
                        </select>
                        @error('jenis_cuti')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="tanggal_mulai" class="form-label">Tanggal Mulai</label>
                                <input type="date" name="tanggal_mulai" id="tanggal_mulai" class="form-control @error('tanggal_mulai') is-invalid @enderror" value="{{ old('tanggal_mulai') }}" required>
                                @error('tanggal_mulai')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="tanggal_selesai" class="form-label">Tanggal Selesai</label>
                                <input type="date" name="tanggal_selesai" id="tanggal_selesai" class="form-control @error('tanggal_selesai') is-invalid @enderror" value="{{ old('tanggal_selesai') }}" required>
                                @error('tanggal_selesai')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="alasan" class="form-label">Alasan/Keterangan</label>
                        <textarea name="alasan" id="alasan" class="form-control @error('alasan') is-invalid @enderror" rows="3" required>{{ old('alasan') }}</textarea>
                        @error('alasan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="alamat_selama_cuti" class="form-label">Alamat Selama Cuti</label>
                        <input type="text" name="alamat_selama_cuti" id="alamat_selama_cuti" class="form-control @error('alamat_selama_cuti') is-invalid @enderror" value="{{ old('alamat_selama_cuti') }}" required>
                        @error('alamat_selama_cuti')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="telp_selama_cuti" class="form-label">Nomor Telepon Selama Cuti</label>
                        <input type="text" name="telp_selama_cuti" id="telp_selama_cuti" class="form-control @error('telp_selama_cuti') is-invalid @enderror" value="{{ old('telp_selama_cuti') }}" required>
                        @error('telp_selama_cuti')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-grid gap-2 d-sm-flex justify-content-sm-end">
                        <a href="{{ route('hakim.cuti.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Kembali
                        </a>
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check-circle"></i> Ajukan Cuti
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
