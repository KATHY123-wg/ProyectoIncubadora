<div class="container mt-4">
@php
  $qs = [
    'usuario_id' => $usuario_id, // vendedor
    'gestion'    => $gestion,
  ];
@endphp

<div class="d-flex gap-2 mb-3">
  <a href="{{ route('reportes.ventas.pdf', $qs) }}" class="btn btn-outline-danger {{ (!$usuario_id || !$gestion) ? 'disabled' : '' }}">
    Exportar PDF
  </a>
  <a href="{{ route('reportes.ventas.xls', $qs) }}" class="btn btn-outline-success {{ (!$usuario_id || !$gestion) ? 'disabled' : '' }}">
    Exportar XLS
  </a>
</div>

    <style>
    /* === Paleta combinada con tu layout actual === */
    :root{
        --brand-primary:   #556B2F;   /* olivo oscuro (navbar/topbar) */
        --brand-secondary: #6C7A3D;   /* olivo medio (sidebar, bordes) */
        --surface:         #FFF8E1;   /* crema de fondo */
        --accent:          #A1887F;   /* acento marrón claro para botones */
        --card-header:     #5D4037;   /* marrón principal para encabezados */
        --ink:             #2b2b2b;  
         --blue:           #edfaf5ff;   /* texto base */
    }

    .card-report{
        border: 1px solid rgba(0,0,0,.08);
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 8px 18px rgba(0,0,0,.08);
        background: #fff;
    }
    .card-report .card-header{
        background: var(--brand-secondary);
        color:#fff;
        padding: 16px 20px;
    }
    .card-report .card-header h2{
        font-weight: 800; 
        letter-spacing:.3px;
        margin:0;
        font-size: clamp(20px, 2vw, 26px);
    }
    .card-report .card-header .sub{
        opacity:.85; font-size: 13px;
    }

    /* Barra de filtros */
    .toolbar{
        background: var(--blue);
        border-bottom: 1px solid var(--brand-secondary);
    }
    .toolbar .form-label{
        font-weight:600; font-size: 13px; color: var(--brand-secondary);
    }

    /* Botón de acción */
    .btn-accent{ background: var(--brand-primary); color:#ffffff; border:none; }
    .btn-accent:hover{ filter:brightness(.92); color:#fff; }

    /* Tabla */
    .table thead th{
        position: sticky; top:0; z-index: 1;
        background: var(--brand-primary); 
        color:#fff;
        border-bottom: 0;
    }
    .table tbody tr:hover{ background: rgba(255,248,225,.6); }
    .table td, .table th{ vertical-align: middle; }
    .td-money{
        text-align:right;
        font-variant-numeric: tabular-nums;
        font-family: ui-monospace, SFMono-Regular, Menlo, monospace;
        color: var(--ink);
    }

    /* Contenedor del gráfico */
    #graficoVentas{
        background:#fff;
        border:1px solid rgba(0,0,0,.08);
        border-radius:12px;
    }
</style>
    <div class="card-report">

        {{-- Header --}}
        <div class="card-header">
            <h2>Reporte de Ventas</h2>
            <div class="sub">Resumen por cajero y gestión</div>
        </div>

        {{-- Toolbar / Filtros --}}
        <div class="toolbar">
            <div class="container py-3">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label">Cajero</label>
                        <select wire:model="usuario_id" class="form-select">
                            <option value="">-- Seleccione --</option>
                            @foreach($usuarios as $usuario)
                                <option value="{{ $usuario->id }}">{{ $usuario->nombre }} {{ $usuario->apellido1 }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Gestión</label>
                        <select wire:model="gestion" class="form-select">
                            <option value="">-- Seleccione --</option>
                            @for($y = now()->year; $y >= now()->year -1; $y--)
                                <option value="{{ $y }}">{{ $y }}</option>
                            @endfor
                        </select>
                    </div>

                    {{-- Si luego reactivas Mes, quedará alineado
                    <div class="col-md-4">
                        <label class="form-label">Mes</label>
                        <select wire:model="mes" class="form-select">
                            <option value="">Todos</option>
                            @foreach(range(1, 12) as $m)
                                <option value="{{ $m }}">{{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}</option>
                            @endforeach
                        </select>
                    </div>
                    --}}

                    <div class="col-md-4 col-lg-3 ms-auto">
                        <button wire:click="generar" class="btn btn-accent w-100 py-2 fw-semibold">
                            Generar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Contenido --}}
        <div class="card-body p-4">

            @if($ventas)
                <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                    <h4 class="fw-bold mb-2 mb-sm-0">
                        Vendedor: <span class="stat-chip">{{ $nombreCajero }}</span>
                    </h4>
                    <div class="stat-chip">Gestión: {{ $gestion }}</div>
                </div>

                <div class="table-responsive rounded">
                    <table class="table table-bordered table-hover align-middle mb-3">
                        <thead class="table-dark text-center">
                            <tr>
                                <th>Incubadora</th>
                                <th>Cliente</th>
                                <th>Precio</th>
                                <th>Fecha</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($ventas as $venta)
                            @foreach($venta->detalleVentas as $detalle)
                                <tr class="text-center">
                                    <td class="fw-semibold">{{ $detalle->incubadora->codigo }}</td>
                                    <td>{{ $venta->avicultor->nombre ?? 'No registrado' }}</td>
                                    <td class="td-money">Bs {{ number_format($detalle->precio_unitario, 2) }}</td>
                                    <td>{{ \Carbon\Carbon::parse($venta->fecha_venta)->format('d/m/Y') }}</td>
                                </tr>
                            @endforeach
                        @empty
                            <tr><td colspan="4" class="text-danger text-center py-4">No hay resultados</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    <h5 class="text-center fw-bold mb-3">Total de Ventas</h5>
                    <div id="graficoVentas" style="height: 350px;"></div>
                </div>

                @push('scripts')
                <script>
                    let ventasChart = null;

                    window.addEventListener('renderVentasChart', (event) => {
                        const data = event.detail;
                        const el = document.querySelector("#graficoVentas");
                        if (!el) return;

                        if (ventasChart) { ventasChart.destroy(); ventasChart = null; }

                        const options = {
                            chart: { type: 'bar', height: 350, toolbar: { show: false } },
                            series: [{ name: 'Total Bs', data: data.montos }],
                            xaxis: { categories: data.dias, labels:{ style:{ fontWeight: 600 } } },
                            yaxis: { labels:{ formatter: (v)=> v.toFixed(0) } },
                            dataLabels: { enabled: false },
                            plotOptions: { bar: { borderRadius: 6, columnWidth: '45%' } },
                            grid: { borderColor: 'rgba(0,0,0,.08)' },
                            colors: ['#A1887F'] /* tu paleta */
                        };

                        ventasChart = new ApexCharts(el, options);
                        ventasChart.render();
                    });
                </script>
                @endpush

            @endif
        </div>
    </div>
</div>
