@extends('layouts.base')

@section('titulo', 'Dashboard Avicultor')
@section('titulo_nav', 'Avicultor')

@section('sidebar')
<div class="sidebar" id="sidebar">
    <div class="logo text-center">
        <img src="{{ asset('images/logo (2).png') }}" alt="Logo" class="img-fluid">
        <h6 class="mt-2">AVICULTOR</h6>

        {{-- Nombre del usuario autenticado --}}
        <div style="font-size: 14px; margin-top: 8px; color: #5D4037; font-weight: bold;">
            <div class="user-name">
                <i class="bi bi-person-fill" style="color: orange;"></i> {{ Auth::user()->nombre }}
            </div>
        </div>
    </div>
            <a class="nav-link btn-sidebar" href="{{ route('avicultor.inicio') }}">
                <i class="bi bi-house me-2 menu-icon"></i> Inicio
            </a>
            <a class="nav-link btn-sidebar" href="{{ route('avicultor.graficos') }}">
                <i class="bi bi-bar-chart-fill me-2"></i> Gráficos
            </a>
            <a class="nav-link btn-sidebar" href="{{ route('avicultor.procesos') }}">
                <i class="bi bi-play-circle me-2"></i> Procesos
            </a>
            <a class="nav-link btn-sidebar" href="{{ route('avicultor.reportes') }}">
                <i class="bi bi-file-earmark-bar-graph me-2"></i> Reportes
            </a>
            <a class="nav-link btn-sidebar" href="{{ route('avicultor.nosotros') }}">
                <i class="bi bi-info-square me-2"></i> Nosotros
            </a>
</div>
@endsection

@section('contenido')
    <h2 class="mb-4 fw-bold">Bienvenido {{ Auth::user()->name }} - Avicultor</h2>

    <h4 class="fw-bold mb-4">PROCESO ACTUAL DE INCUBACIÓN</h4>

    @php
        use Carbon\Carbon;

        // Valores por defecto
        $diasRestantes = null;
        $diasTranscurridos = null;

        // Lee datos del proceso si existen:
        // Espera: $procesoActual->fecha_inicio (datetime)
        //         y (fecha_fin_estimada datetime) o (duracion_dias int)
        if (isset($procesoActual)) {
            $now = Carbon::now();
            $inicio = !empty($procesoActual->fecha_inicio) ? Carbon::parse($procesoActual->fecha_inicio) : null;

            // Determina fecha fin
            if (!empty($procesoActual->fecha_fin_estimada)) {
                $fin = Carbon::parse($procesoActual->fecha_fin_estimada);
            } elseif ($inicio && !empty($procesoActual->duracion_dias)) {
                $fin = $inicio->copy()->addDays((int)$procesoActual->duracion_dias);
            } else {
                $fin = null;
            }

            if ($inicio) {
                // Días transcurridos desde que inició
                $diasTranscurridos = $inicio->diffInDays($now);
            }

            if ($fin) {
                // Días restantes hasta la fecha fin (no negativos)
                $dif = $now->diffInDays($fin, false); // negativo si ya pasó
                $diasRestantes = $dif > 0 ? $dif : 0;
            }
        }

        // Tarjetas (las de días usan el color del navbar por JS)
        $cards = [
            ['title' => 'TEMPERATURA',     'value' => '25 °C',  'icon' => 'bi-thermometer-high', 'color' => '#FF5252'],
            ['title' => 'HUMEDAD',         'value' => '55 %',   'icon' => 'bi-droplet-half',     'color' => '#42A5F5'],
            ['title' => 'DÍAS RESTANTES',  'value' => $diasRestantes !== null ? ($diasRestantes . ' días') : '—', 'icon' => 'bi-hourglass-split', 'color' => 'navbar'],
            ['title' => 'DÍA DE INCUBACIÓN','value' => $diasTranscurridos !== null ? $diasTranscurridos : '—',    'icon' => 'bi-calendar-check',  'color' => 'navbar'],
        ];
    @endphp

    <div class="row g-4 justify-content-center">
        @foreach ($cards as $card)
            <div class="col-12 col-sm-6 col-md-6 col-lg-3">
                <div class="card shadow border-0 h-100" style="border-radius: 16px;">
                    <div class="card-header text-white text-center fw-semibold
                        {{ $card['color'] === 'navbar' ? 'use-navbar-color' : '' }}"
                        style="border-top-left-radius: 16px; border-top-right-radius: 16px;
                               {{ $card['color'] !== 'navbar' ? 'background-color: '.$card['color'].';' : '' }}">
                        {{ $card['title'] }}
                    </div>
                    <div class="card-body d-flex flex-column align-items-center justify-content-center">
                        <div class="icon-animado mb-3">
                            <i class="bi {{ $card['icon'] }} {{ $card['color'] === 'navbar' ? 'use-navbar-color-icon' : '' }}"
                               style="font-size: 4.5rem; {{ $card['color'] !== 'navbar' ? 'color: '.$card['color'].';' : '' }}"></i>
                        </div>
                        <p class="fs-4 fw-bold mb-0">{{ $card['value'] }}</p>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <section class="pt-4">
    <div class="container">
        <div class="row g-4 justify-content-center">
            <!-- Tarjeta: Iniciar Proceso -->
            <div class="col-md-5">
                <a href="{{ route('avicultor.procesos') }}" class="text-decoration-none">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body text-center">
                            <div class="feature rounded-3 mb-3" style="background-color: #A1887F; color: white;">
                                <i class="bi bi-play-circle "></i>
                            </div>
                            <h5 class="card-title text-dark">Iniciar Proceso</h5>
                            <p class="card-text text-secondary">Iniciar procesos de incubabación.</p>
                        </div>
                    </div>
                </a>
            </div>
            <!-- Tarjeta: Reportes -->
            <div class="col-md-5">
                <a href="{{ route('avicultor.reportes') }}" class="text-decoration-none">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body text-center">
                            <div class="feature rounded-3 mb-3" style="background-color: #A1887F; color: white;">
                                <i class="bi bi-file-earmark-bar-graph "></i>
                            </div>
                            <h5 class="card-title text-dark">Reportes</h5>
                            <p class="card-text text-secondary">Visualiza estadísticas y análisis del sistema.</p>
                        </div>
                    </div>
                </a>
            </div>
        </div> 
    </div>
    </section>

    {{-- Toma el color de fondo del navbar y lo aplica a las tarjetas marcadas --}}
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const nav = document.querySelector('.navbar');
        if (!nav) return;
        const navColor = window.getComputedStyle(nav).backgroundColor;

        document.querySelectorAll('.use-navbar-color').forEach(el => {
            el.style.backgroundColor = navColor;
            el.style.color = '#fff';
        });
        document.querySelectorAll('.use-navbar-color-icon').forEach(el => {
            el.style.color = navColor;
        });
    });
    </script>
@endsection
