<div class="sidebar" id="sidebar">
    <div class="logo text-center">
        <img src="{{ asset('images/logo (2).png') }}" alt="Logo" class="img-fluid">
        <h6 class="mt-2">AVICULTOR</h6>

        <a href="#" data-bs-toggle="modal" data-bs-target="#modalPerfil"
           onclick="event.preventDefault();"
           style="text-decoration:none; color:#5D4037; font-weight:bold; font-size:14px;">
           👤 {{ Auth::user()->nombre }}
        </a>
    </div>

    <a class="nav-link btn-sidebar {{ request()->routeIs('avicultor.inicio') ? 'active' : '' }}"
       href="{{ route('avicultor.inicio') }}">
        <i class="bi bi-house me-2 menu-icon"></i> Inicio
    </a>

    <a class="nav-link {{ request()->routeIs('avicultor.graficos') ? 'active' : '' }}"
       href="{{ route('avicultor.graficos') }}">
        <i class="bi bi-bar-chart-fill me-2"></i> Gráficos
    </a>

    <a class="nav-link {{ request()->routeIs('avicultor.procesos') ? 'active' : '' }}"
       href="{{ route('avicultor.procesos') }}">
        <i class="bi bi-play-circle me-2"></i> Procesos
    </a>

    <a class="nav-link {{ request()->routeIs('avicultor.reportes') ? 'active' : '' }}"
       href="{{ route('avicultor.reportes') }}">
        <i class="bi bi-file-earmark-bar-graph me-2"></i> Reportes
    </a>

    {{-- Alertas (solo las suyas por filtro del componente) 
    <a class="nav-link {{ request()->routeIs('alertas.panel') ? 'active' : '' }}"
       href="{{ route('alertas.panel') }}">
        <i class="bi bi-bell-fill me-2"></i> Alertas
    </a>--}}

    <a class="nav-link {{ request()->routeIs('avicultor.nosotros') ? 'active' : '' }}"
       href="{{ route('avicultor.nosotros') }}">
        <i class="bi bi-info-square me-2"></i> Nosotros
    </a>
</div>
