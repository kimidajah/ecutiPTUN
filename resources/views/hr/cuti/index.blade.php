@extends('hr.layouts.app')

@section('title', 'Manajemen Cuti')

@section('content')

<div class="row mb-4">
    <div class="col-12">
        <h3 class="fw-bold">Manajemen Cuti Pegawai</h3>
        <p class="text-muted">Kelola pengajuan cuti yang masuk.</p>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body">

        @include('hr.cuti._table')

    </div>
</div>

@endsection
