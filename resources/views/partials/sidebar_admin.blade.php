<div class="sidebar" id="sidebar">
    <div class="logo text-center">
        <img src="{{ asset('images/logo (2).png') }}" alt="Logo" class="img-fluid">
        <h6 class="mt-2">ADMINISTRADOR</h6>

        <div style="font-size: 14px; margin-top: 8px; color: #5D4037; font-weight: bold;">
            <a href="#" data-bs-toggle="modal" data-bs-target="#modalPerfil"
               onclick="event.preventDefault();"
               style="text-decoration:none; color:#5D4037; font-weight:bold; font-size:14px;">
               👤 {{ Auth::user()->nombre }}
            </a>
        </div>
    </div>

    <a class="nav-link btn-sidebar {{ request()->routeIs('admin.inicio') ? 'active' : '' }}"
       href="{{ route('admin.inicio') }}">
        <i class="bi bi-house-door me-2 menu-icon"></i> Inicio
    </a>

    <a class="nav-link {{ request()->routeIs('admin.usuarios') ? 'active' : '' }}"
       href="{{ route('admin.usuarios') }}">
        <i class="bi bi-people-fill me-2 menu-icon"></i> Gestión de Usuarios
    </a>

    <a class="nav-link {{ request()->routeIs('admin.incubadoras') ? 'active' : '' }}"
       href="{{ route('admin.incubadoras') }}">
        <i class="bi bi-egg-fried me-2"></i> Gestión de Incubadoras
    </a>

    <a class="nav-link {{ request()->routeIs('admin.graficos') ? 'active' : '' }}"
       href="{{ route('admin.graficos') }}">
        <i class="bi bi-bar-chart-line me-2 menu-icon"></i> Gráficos
    </a>

    <a class="nav-link {{ request()->routeIs('admin.ventas') ? 'active' : '' }}"
       href="{{ route('admin.ventas') }}">
        <i class="bi bi-cash-stack me-2 menu-icon"></i> Ventas
    </a>

    {{-- Reportes con submenú --}}
    @php
        $open = request()->routeIs('reportes.*') ? 'show' : '';
    @endphp
    <div class="sidebar-dropdown">
        <a class="nav-link btn-sidebar d-flex justify-content-between align-items-center"
           data-bs-toggle="collapse" href="#repAdmin" role="button" aria-expanded="{{ $open ? 'true':'false' }}"
           aria-controls="repAdmin">
            <span><i class="bi bi-file-earmark-text me-2 menu-icon"></i> Reportes</span>
            <i class="bi bi-caret-down-fill"></i>
        </a>
        <div class="collapse {{ $open }}" id="repAdmin">
            <a href="{{ route('reportes.ventas') }}" class="nav-link sub-link {{ request()->routeIs('reportes.ventas') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-bar-graph me-2 menu-icon"></i> Reportes de Ventas
            </a>
            <a href="{{ route('reportes.procesos') }}" class="nav-link sub-link {{ request()->routeIs('reportes.procesos') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-bar-graph me-2"></i> Reportes de Procesos
            </a>
        </div>
    </div>

    {{-- Alertas 
    <a class="nav-link {{ request()->routeIs('alertas.panel') ? 'active' : '' }}"
       href="{{ route('alertas.panel') }}">
        <i class="bi bi-bell-fill me-2"></i> Alertas
    </a>--}}

    <a class="nav-link {{ request()->routeIs('admin.nosotros') ? 'active' : '' }}"
       href="{{ route('admin.nosotros') }}">
        <i class="bi bi-people me-2 menu-icon"></i> Nosotros
    </a>
</div>
