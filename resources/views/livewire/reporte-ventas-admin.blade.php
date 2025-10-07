<link rel="stylesheet" href="{{ asset('css/ventasadmin.css') }}">
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
