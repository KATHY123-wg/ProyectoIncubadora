@extends('layouts.base')

@section('titulo', 'Dashboard Admin')
@section('titulo_nav', 'Administrador')

@section('sidebar')
<div class="sidebar" id="sidebar">
    <div class="logo text-center">
        <img src="{{ asset('images/logo (2).png') }}" alt="Logo" class="img-fluid">
        <h6 class="mt-2">ADMINISTRADOR</h6>

        <div style="font-size: 14px; margin-top: 8px; color: #5D4037; font-weight: bold;">
            <div class="user-name">
                <svg xmlns="http://www.w3.org/2000/svg" width="1.1em" height="1.1em" viewBox="0 0 16 16" style="margin-right:.25rem; vertical-align:-.125em;">
                <path fill="#FF6600" d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H3z"/>
                <path fill="#FF6600" d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/>
                </svg>
                {{ Auth::user()->nombre }}
            </div>
         </div>
    </div>

    <a class="nav-link btn-sidebar" href="{{ route('admin.inicio') }}">
        <i class="bi bi-house-door me-2 menu-icon"></i> Inicio
    </a>
    <a class="nav-link" href="{{ route('admin.graficos') }}">
        <i class="bi bi-bar-chart-line me-2 menu-icon"></i> Gráficos
    </a>
    <div class="sidebar-dropdown">
        <a class="nav-link btn-sidebar d-flex justify-content-between align-items-center" href="#">
            <span><i class="bi bi-file-earmark-text me-2 menu-icon"></i> Reportes</span>
            <i class="bi bi-caret-down-fill"></i>
        </a>
        <div class="submenu">
           <a href="{{ route('reportes.ventas') }}" class="nav-link sub-link">
                <i class="bi bi-file-earmark-bar-graph me-2 menu-icon"></i> Reportes de Ventas
            </a>

            <a href="{{ route('reportes.procesos') }}" class="nav-link sub-link">
                <i class="bi bi-file-earmark-bar-graph me-2"></i> Reportes de Procesos
            </a>
        </div>
    </div>
    <a class="nav-link" href="{{ route('admin.nosotros') }}">
        <i class="bi bi-people me-2 menu-icon"></i> Nosotros
    </a>
    <a class="nav-link" href="{{ route('admin.ventas') }}">
        <i class="bi bi-cash-stack me-2 menu-icon"></i> Ventas
    </a>
    <a class="nav-link" href="{{ route('admin.usuarios') }}">
        <i class="bi bi-people-fill me-2 menu-icon"></i> Gestión de Usuarios
    </a>
    <a class="nav-link" href="{{ route('admin.incubadoras') }}">
        <i class="bi bi-egg-fried me-2"></i> Gestión de Incubadoras
    </a>
</div>
@endsection

@section('contenido')

{{-- ======= VIDEO DE FONDO A PANTALLA COMPLETA ======= --}}
<style>

</style>


<div id="bgVideoWrap" aria-hidden="true">
  <video
    id="bgVideo"
    playsinline
    webkit-playsinline
    muted
    loop
    autoplay
    preload="auto"
    poster="{{ asset('images/incubacion.png') }}"
  >
    <source src="{{ asset('videos/incubadora.webm') }}" type="video/webm">
    <source src="{{ asset('videos/incubadora.mp4') }}" type="video/mp4">
    Tu navegador no soporta video HTML5.
  </video>
  <div id="bgDim"></div>
</div>
{{-- ======= /VIDEO DE FONDO ======= --}}

<h2 class="mb-4 fw-bold">Panel del Administrador</h2>

<!-- 📌 Tarjetas tipo botón con íconos estilo profesional -->
<section class="pt-4">
    <div class="container">
        <div class="row g-4 justify-content-center">
            <!-- Tarjeta: Incubadoras disponibles / vendidas -->
            <div class="col-md-5">
                <a href="{{ route('admin.ventas') }}" class="text-decoration-none">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body text-center">
                            <div class="feature rounded-3 mb-3" style="background-color: #6f7744; color: white;">
                                <i class="bi bi-box-seam fs-2"></i>
                            </div>
                            <h5 class="card-title text-dark">Incubadoras disponibles / vendidas</h5>
                            <p class="card-text text-secondary">Consulta el estado de tu inventario de incubadoras.</p>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Tarjeta: Usuarios registrados -->
            <div class="col-md-5">
                <a href="{{ route('admin.usuarios') }}" class="text-decoration-none">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body text-center">
                            <div class="feature rounded-3 mb-3" style="background-color: #6f7744; color: white;">
                                <i class="bi bi-people fs-2"></i>
                            </div>
                            <h5 class="card-title text-dark">Usuarios registrados</h5>
                            <p class="card-text text-secondary">Gestión de usuarios activos en el sistema.</p>
                        </div>
                    </div>
                </a>
            </div>
            
        </div>
    </div>
    <style>
        /*botones de video*/
  /* Fondo de video */
  #bgVideoWrap{
    position: fixed;
    inset: 0;
    z-index: 0;          /* detrás del contenido */
    overflow: hidden;
    pointer-events: none;/* que NUNCA bloquee clics */
  }
  #bgVideo{
    width: 100vw;
    height: 100vh;
    object-fit: cover;
    display: block;
  }
  #bgDim{
    position: fixed;
    inset: 0;
    background: rgba(196, 225, 231, 0.25);
    pointer-events: none;
    z-index: 0;
  }

  /* Deja ver el video debajo del contenido central si quieres */
  .main-content, .navbar, footer{ 
    position: relative;
    z-index: 2;          /* por encima del video */
    background: transparent;
  }

  /* NO tocar el position del sidebar, solo súbelo de nivel */
  #sidebar{
    z-index: 3;          /* encima del contenido central */
  }
    </style>
</section>

{{-- Forzar autoplay si el navegador lo bloquea sin interacción --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
  const v = document.getElementById('bgVideo');
  if (!v) return;
  const tryPlay = () => {
    const pr = v.play?.();
    if (pr && typeof pr.then === 'function') {
      pr.catch(() => {
        v.muted = true;
        v.play().catch(()=>{});
      });
    }
  };
  tryPlay();
  window.addEventListener('focus', tryPlay);
});
</script>

@livewireScripts
@once
  <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
@endonce
@stack('scripts')
@endsection
