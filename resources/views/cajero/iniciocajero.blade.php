@extends('layouts.base')

@section('titulo', 'Panel Vendedor')
@section('titulo_nav', 'Cajero / Vendedor')

@section('sidebar')
<div class="sidebar" id="sidebar">
    <div class="logo text-center">
        <img src="{{ asset('images/logo (2).png') }}" alt="Logo" class="img-fluid">
        <h6 class="mt-2">VENTAS</h6>

        
        {{-- Nombre del usuario autenticado (abre modal de perfil) --}}
        <a href="#" data-bs-toggle="modal" data-bs-target="#modalPerfil"
        style="font-size: 14px; margin-top: 8px; color: #5D4037; font-weight: bold; display:inline-block; text-decoration:none;">
            👤 {{ Auth::user()->nombre }}
        </a>

    </div>

        <a class="nav-link" href="{{ route('vendedor.inicio') }}"><i class="bi bi-house-door me-2"></i> Inicio</a>
        <a class="nav-link" href="{{ route('vendedor.nosotros') }}"><i class="bi bi-info-circle me-2"></i> Nosotros</a>
        <a class="nav-link" href="{{ route('vendedor.ventas') }}"><i class="bi bi-clock-history me-2"></i> Ventas</a>
       

    
</div>
@endsection



@section('contenido')
    @livewire('vendedor.dashboard')
    @livewire('perfil.editar-perfil')
@endsection
