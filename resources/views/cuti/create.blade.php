@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h3>Ajukan Cuti</h3>

    <form action="{{ route('cuti.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label>Jenis Cuti</label>
            <input type="text" name="jenis_cuti" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Tanggal Mulai</label>
            <input type="date" name="tanggal_mulai" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Tanggal Selesai</label>
            <input type="date" name="tanggal_selesai" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Alasan</label>
            <textarea name="alasan" class="form-control" rows="3" required></textarea>
        </div>

        <button type="submit" class="btn btn-success">Kirim Pengajuan</button>
    </form>
</div>
@endsection
