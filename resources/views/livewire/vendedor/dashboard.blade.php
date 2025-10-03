<div class="container-fluid py-3">
   <style>
    /* ===== Paleta coherente con tu app ===== */
    :root{
        --olive-dark1: #556B2F;  /* encabezados/acentos oscuros */
        --olive1:      #6C7A3D;  /* base sidebar / bordes */
        --brown:      #5D4037;  /* marrón de marca */
        --gold:       #F3AF43;  /* dorado de acento */
        --cream:      #FFF8E1;  /* fondo cálido */
        --ink:        #1f2937;  /* texto */
        --muted:      #6b7280;  /* texto suave */
        --card:       #FFFFFF;  /* superficies */
        --line:       rgba(0,0,0,.06);
    }

    /* fondo general más cálido (opcional, contenedor actual) */
    .container-fluid{ background: linear-gradient(180deg, #fff 0%, var(--cream) 100%); border-radius: 12px; }

    /* ===== Tarjetas KPI ===== */
    .kpi-card{
        background: var(--card);
        border: 1px solid var(--line);
        border-radius: 14px;
        padding: 18px;
        box-shadow: 0 10px 24px rgba(0,0,0,.06);
        height: 100%;
        position: relative;
        overflow: hidden;
    }
    /* banda de color arriba */
    .kpi-card::before{
        content:""; position:absolute; inset:0 0 auto 0; height:6px;
        background: linear-gradient(90deg, var(--olive-dark1), var(--olive1), var(--gold));
    }
    /* burbuja decorativa */
    .kpi-card::after{
        content:""; position:absolute; right:-30px; bottom:-30px; width:120px; height:120px;
        border-radius: 50%;
        background: radial-gradient(closest-side, rgba(243,175,67,.18), transparent 70%);
    }
    .kpi-label{ color: var(--muted); font-size:.9rem; letter-spacing:.2px; }
    .kpi-value{ font-size:1.7rem; font-weight:800; color: var(--ink); }

    /* ===== Secciones ===== */
    .section-card{
        background: var(--card);
        border: 1px solid var(--line);
        border-radius: 14px;
        box-shadow: 0 10px 24px rgba(0,0,0,.06);
        position: relative;
    }
    /* borde superior de color */
    .section-card::before{
        content:""; position:absolute; inset:0 0 auto 0; height:4px;
        background: linear-gradient(90deg, var(--brown), var(--olive));
        border-top-left-radius:14px; border-top-right-radius:14px;
    }
    .section-title{
        font-weight: 800; color: var(--brown);
        display: inline-flex; align-items: center; gap:.5rem;
    }
    .section-title::before{
        content:""; width:10px; height:10px; border-radius:3px; background: var(--gold);
        box-shadow: 0 0 0 3px rgba(243,175,67,.25);
    }

    /* ===== Badges ===== */
    .badge-soft{
        background: rgba(243,175,67,.12);
        color: var(--brown);
        border: 1px solid rgba(243,175,67,.35);
        font-weight: 700;
        border-radius: 999px;
        padding: .25rem .55rem;
        min-width: 36px; text-align: center;
    }
    /* armonizar los de estado Bootstrap */
    .badge.text-bg-success{
        background: linear-gradient(180deg, #88b04b, #6C7A3D) !important;
        color:#fff !important; border:0 !important;
        box-shadow: 0 4px 10px rgba(108,122,61,.25);
    }
    .badge.text-bg-warning{
        background: linear-gradient(180deg, #ffd36a, var(--gold)) !important;
        color:#5b3e14 !important; border:0 !important;
        box-shadow: 0 4px 10px rgba(243,175,67,.25);
    }

    /* ===== Tabla ===== */
    .table thead th{
        background: linear-gradient(180deg, var(--olive-dark1), var(--olive1));
        color:#fff; font-weight:700; letter-spacing:.2px;
        border-bottom: 0;
    }
    .table tbody tr:hover{ background: rgba(255,248,225,.5); }
    .table td, .table th{ vertical-align: middle; }

    /* ===== Chart wrapper ===== */
    #chart-ventas{
        background: var(--card);
        border: 1px solid var(--line);
        border-radius: 12px;
        box-shadow: inset 0 1px 0 rgba(255,255,255,.4);
    }

    /* ===== List group (top incubadoras) ===== */
    .list-group-item{
        border: 1px solid var(--line) !important;
    }
    .list-group-item:hover{
        background: rgba(108,122,61,.06);
    }
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
