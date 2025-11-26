@extends('pegawai.layouts.app')

@section('title', 'Cuti Saya')

@section('content')
@php
    use App\Models\Cuti;
    use Illuminate\Support\Facades\Auth;
    use Carbon\Carbon;

    $user = Auth::user();
    $tahunIni = Carbon::now()->year;

    // HANYA cuti yang sudah disetujui pimpinan
    $cutiTahunIni = Cuti::where('user_id', $user->id)
        ->whereYear('tanggal_mulai', $tahunIni)
        ->where('status', 'disetujui_pimpinan')
        ->get();

    $totalCutiTahunIni = $cutiTahunIni->sum('lama_cuti');

    $batasCuti = $user->sisa_cuti ?? 12;

    $sisaCuti = max(0, $batasCuti - $totalCutiTahunIni);
@endphp


<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold text-success">Cuti Saya</h3>
    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#cutiModal">
        <i class="bi bi-plus-circle"></i> Buat Cuti Baru
    </button>
</div>

{{-- Include komponen ringkasan --}}
@include('pegawai.cuti._summary', [
    'batasCuti' => $batasCuti,
    'totalCutiTahunIni' => $totalCutiTahunIni,
    'sisaCuti' => $sisaCuti
])

{{-- Include tabel riwayat cuti --}}
@include('pegawai.cuti._table_history', [
    'cutiTahunIni' => $cutiTahunIni
])

{{-- Include modal form pengajuan cuti --}}
@include('pegawai.cuti._modal_form')
@endsection
