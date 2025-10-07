<link rel="stylesheet" href="{{ asset('css/vendedor.css') }}">
<div class="container-fluid py-3">
   <style>
    /* ===== Paleta coherente con tu app ===== */
    
</style>

    {{-- KPIs --}}
    <div class="row g-3">
        <div class="col-12 col-lg-3">
            <div class="kpi-card">
                <div class="kpi-label">Ventas hoy</div>
                <div class="kpi-value">{{ number_format($kpis['ventas_hoy'] ?? 0, 0, '.', ',') }}</div>
                <div class="text-muted small">Registros del día</div>
            </div>
        </div>
        <div class="col-12 col-lg-3">
            <div class="kpi-card">
                <div class="kpi-label">Monto hoy (Bs.)</div>
                <div class="kpi-value">{{ number_format($kpis['ingresos_hoy'] ?? 0, 2, '.', ',') }}</div>
                <div class="text-muted small">Total del día</div>
            </div>
        </div>
        <div class="col-12 col-lg-3">
            <div class="kpi-card">
                <div class="kpi-label">Ventas del mes</div>
                <div class="kpi-value">{{ number_format($kpis['ventas_mes'] ?? 0, 0, '.', ',') }}</div>
                <div class="text-muted small">Acumulado {{ now('America/La_Paz')->format('M Y') }}</div>
            </div>
        </div>
        <div class="col-12 col-lg-3">
            <div class="kpi-card">
                <div class="kpi-label">Incubadoras libres</div>
                <div class="kpi-value">{{ number_format($kpis['incubadoras_libres'] ?? 0, 0, '.', ',') }}</div>
                <div class="text-muted small">Disponibles para venta</div>
            </div>
        </div>
    </div>

    {{-- Gráfico (últimos N días) + Top incubadoras --}}
    <div class="row g-3 mt-1">
        <div class="col-12 col-lg-8">
            <div class="section-card p-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="section-title">Ventas (últimos {{ $range }} días)</div>
                </div>
                <div id="chart-ventas" style="height:320px;"></div>
            </div>
        </div>

        <div class="col-12 col-lg-4">
            <div class="section-card p-3">
                <div class="section-title mb-2">Top incubadoras ({{ $range }} días)</div>
                @php
                    // Armar pares código->cantidad desde chartPorIncubadora
                    $cats = $chartPorIncubadora['categories'] ?? [];
                    $vals = ($chartPorIncubadora['series'][0]['data'] ?? []);
                @endphp
                @if(empty($cats))
                    <div class="text-muted small">Aún no hay datos en este rango.</div>
                @else
                    <ul class="list-group">
                        @foreach($cats as $i => $cod)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>{{ $cod }}</span>
                                <span class="badge badge-soft">{{ (int)($vals[$i] ?? 0) }}</span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>

    {{-- Ventas recientes --}}
    <div class="row g-3 mt-1">
        <div class="col-12">
            <div class="section-card p-3">
                <div class="section-title mb-2">Ventas recientes</div>
                <div class="table-responsive">
                    <table class="table table-sm align-middle">
                        <thead>
                            <tr>
                                <th style="width:120px;">Fecha</th>
                                <th style="width:120px;">Hora</th>
                                <th>Estado</th>
                                <th class="text-end">Total (Bs.)</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($recientes as $v)
                            @php $f = \Carbon\Carbon::parse($v->fecha_venta)->timezone('America/La_Paz'); @endphp
                            <tr>
                                <td>{{ $f->format('d/m/Y') }}</td>
                                <td>{{ $f->format('H:i') }}</td>
                                <td>
                                    @if($v->estado == 1)
                                        <span class="badge text-bg-success">Completada</span>
                                    @else
                                        <span class="badge text-bg-warning">Pendiente</span>
                                    @endif
                                </td>
                                <td class="text-end">{{ number_format((float)$v->total_bs, 2, '.', ',') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-muted">Sin registros</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
        <script>
        window.addEventListener('livewire:init', () => {
            // Render inicial con datos de PHP
            const el = document.querySelector('#chart-ventas');
            if (!el) return;

            const options = {
                chart: { type: 'area', height: 320, toolbar: { show: false } },
                dataLabels: { enabled: false },
                stroke: { curve: 'smooth', width: 3 },
                xaxis: { categories: @json($chartVentasPorDia['categories'] ?? []) },
                series: @json($chartVentasPorDia['series'] ?? []),
                fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.35, opacityTo: 0.05, stops: [0,90,100] } },
                tooltip: { y: { formatter: (v)=> new Intl.NumberFormat('es-BO',{minimumFractionDigits:2,maximumFractionDigits:2}).format(v)+' Bs.' } }
            };
            const chart = new ApexCharts(el, options);
            chart.render();

            // Actualizaciones desde Livewire (cuando cambie $range u otros)
            Livewire.on('charts:update', (payload) => {
                const v = payload.ventasPorDia ?? { categories:[], series:[] };
                chart.updateOptions({ xaxis: { categories: v.categories } });
                chart.updateSeries(v.series);
            });
        });
        </script>
    @endpush
</div>
