@extends('layouts.app')

@section('title', 'Beranda')

@section('content')
  {{-- Beranda Section --}}
  @include('components.beranda')

  {{-- // Alur Pengajuan Cuti Section --}}
  @include('components.alur')

  {{-- Jenis Cuti Section --}}
  @include('components.jenis-cuti')

@endsection 
