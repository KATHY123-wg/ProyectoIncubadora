@extends('layouts.base')

@section('titulo', 'Alertas')
@section('titulo_nav', 'Panel de Alertas')

{{-- Sidebar por rol --}}
@section('sidebar')
    @php $rol = auth()->user()->rol ?? 'vendedor'; @endphp

    @if($rol === 'admin')
        @include('partials.sidebar_admin')   {{-- crea este archivo si no existe --}}
    @elseif($rol === 'avicultor')
        @include('partials.sidebar_avicultor')
    @endif
@endsection

@section('contenido')
    <div class="container py-3">
        {{-- Aquí va el componente Livewire que ya construimos --}}
        @livewire('alertas-panel')
    </div>
@endsection


