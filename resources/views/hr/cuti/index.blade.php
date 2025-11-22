@extends('hr.layouts.app')

@section('title', 'Manajemen Cuti')

@section('content')

<style>
    [x-cloak] { display: none !important; }
</style>

<div x-data="cutiPopup()" x-init="init()">

    {{-- TABLE --}}
    @include('hr.cuti.table')

    {{-- MODAL --}}
    @include('hr.cuti.modal')

</div>

@endsection
