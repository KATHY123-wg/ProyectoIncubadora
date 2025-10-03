<div class="container mt-4">
    @php
  $qs = [
    'usuarioId'     => $usuarioId,
    'incubadora_id' => $incubadora_id,
    'gestion'       => $gestion,
  ];
@endphp

<div class="d-flex gap-2 mb-3">
  <a href="{{ route('reportes.procesos.pdf', $qs) }}" class="btn btn-outline-danger {{ (!$usuarioId || !$incubadora_id || !$gestion) ? 'disabled' : '' }}">
    Exportar PDF
  </a>
  <a href="{{ route('reportes.procesos.xls', $qs) }}" class="btn btn-outline-success {{ (!$usuarioId || !$incubadora_id || !$gestion) ? 'disabled' : '' }}">
    Exportar XLS
  </a>
</div>

    {{-- ====== SKIN: paleta y estilos visuales ====== --}}
    <style>
        :root{
            --brand-primary:   #556B2F;   /* olivo oscuro (encabezados) */
            --brand-secondary: #6C7A3D;   /* olivo medio (sidebar/bordes) */
            --surface:         #FFF8E1;   /* crema */
            --accent:          #A1887F;   /* botón */
            --card-header:     #5D4037;   /* marrón título panel */
            --ink:             #2b2b2b;
            --blue:            #edfaf5ff; 
        }
        .card-report{border:1px solid rgba(0,0,0,.08); border-radius:16px; overflow:hidden; box-shadow:0 8px 18px rgba(0,0,0,.08); background:#fff;}
        .card-report .card-header{background:var(--brand-secondary); color:#fff; padding:16px 20px;}
        .card-report .card-header h2{font-weight:800; letter-spacing:.3px; margin:0; font-size:clamp(20px,2vw,26px);}
        .card-report .card-header .sub{opacity:.85; font-size:13px}
        .toolbar{background:var(--blue); border-bottom:1px solid var(--brand-secondary)}
        .toolbar .form-label{font-weight:600; font-size:13px; color:var(--brand-secondary)}
        .btn-accent{background:var(--brand-primary); color:#ffffff; border:none;}
        .btn-accent:hover{filter:brightness(.92); color:#50876b;}
        .table thead th{position:sticky; top:0; z-index:1; background:var(--brand-primary); color:#fff; border-bottom:0}
        .table tbody tr:hover{background:rgba(236, 240, 247, 0.6)}
        .table td,.table th{vertical-align:middle}
        .kv{display:flex; gap:.75rem}
        .kv .k{min-width:220px; color:#5f5f5f; font-weight:600}
        #graficoProceso{background:#fff; border:1px solid rgba(0,0,0,.08); border-radius:12px}
        .chip{display:inline-block; padding:.35rem .6rem; border-radius:999px; background:#edf2fa; color:#4e342e; font-weight:600; font-size:.85rem}
    </style>

    <div class="card-report">
        {{-- Header --}}
        <div class="card-header">
            <h2>Reporte de Procesos</h2>
            <div class="sub">Seguimiento por usuario, incubadora y gestión</div>
        </div>

        {{-- Filtros --}}
        <div class="toolbar">
            <div class="container py-3">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label">Seleccionar Usuario</label>
                        <select wire:model="usuarioId" class="form-select">
                            <option value="">Selecciona</option>
                            @foreach($usuarios as $usuario)
                                <option value="{{ $usuario->id }}">{{ $usuario->nombre }} {{ $usuario->apellido1 }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Seleccionar Incubadora</label>
                        <div class="input-group input-group-sm">
                            <select wire:model="incubadora_id" class="form-select" @disabled(empty($incubadoras))>
                                <option value="">Selecciona</option>
                                @foreach($incubadoras as $incubadora)
                                    <option value="{{ $incubadora->id }}">{{ $incubadora->codigo }}</option>
                                @endforeach
                            </select>
                            <button type="button"
                                    class="btn btn-outline-secondary"
                                    wire:click="cargarIncubadoras"
                                    wire:loading.attr="disabled"
                                    wire:target="cargarIncubadoras">
                                <span wire:loading.remove wire:target="cargarIncubadoras">Cargar</span>
                                <span wire:loading wire:target="cargarIncubadoras"
                                      class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            </button>
                        </div>
                        <div class="form-text" wire:loading wire:target="cargarIncubadoras">
                            Actualizando incubadoras…
                        </div>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Gestión</label>
                        <select wire:model="gestion" class="form-select">
                            <option value="">Selecciona</option>
                            @for($y = now()->year; $y >= now()->year - 1; $y--)
                                <option value="{{ $y }}">{{ $y }}</option>
                            @endfor
                        </select>
                    </div>

                    <div class="col-md-3 col-lg-2 ms-auto">
                        <button wire:click="buscarProceso" class="btn btn-accent w-100 py-2 fw-semibold">
                            Generar Reporte
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Contenido --}}
        <div class="p-4">
            @if($proceso)
                <div class="d-flex flex-wrap justify-content-between align-items-center">
                    <h4 class="fw-bold mb-3 mb-md-0">
                        Incubadora: <span class="chip">{{ $proceso->incubadora->codigo }}</span>
                    </h4>
                    @if($gestion)<div class="chip">Gestión: {{ $gestion }}</div>@endif
                </div>

                <div class="table-responsive mt-3">
                    <table class="table table-bordered">
                        <tbody>
                            <tr><th>Fecha de Inicio</th><td>{{ $proceso->fecha_inicio }}</td></tr>
                            <tr><th>Fecha de Fin</th><td>{{ $proceso->fecha_fin }}</td></tr>
                            <tr><th>Cantidad de Huevos Inicializados</th><td>{{ $proceso->huevos_inicio }}</td></tr>
                            <tr><th>Cantidad de Huevos Eclosionados</th><td>{{ $proceso->huevos_eclosionados }}</td></tr>
                            <tr><th>Errores del Motor</th><td>{{ $proceso->errores_motor }}</td></tr>
                            <tr><th>Errores de la Lámpara</th><td>{{ $proceso->errores_lampara }}</td></tr>
                            <tr><th>Errores del Sensor</th><td>{{ $proceso->errores_sensor }}</td></tr>
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    <h5 class="text-center fw-bold mb-3">Visualización del Proceso</h5>
                    <div id="graficoProceso" style="height: 350px;"></div>
                </div>

                @push('scripts')
                <script>
                    document.addEventListener('renderProcesoChart', event => {
                        const data = event.detail;
                        const options = {
                            chart: { type: 'bar', height: 350, toolbar: { show:false } },
                            series: [{
                                name: 'Cantidad',
                                data: [
                                    data.huevos_inicio,
                                    data.huevos_eclosionados,
                                    data.errores_motor,
                                    data.errores_lampara,
                                    data.errores_sensor
                                ]
                            }],
                            xaxis: {
                                categories: [
                                    'Huevos Inicio', 'Huevos Eclosionados',
                                    'Errores Motor', 'Errores Lámpara', 'Errores Sensor'
                                ],
                                labels:{ style:{ fontWeight:600 } }
                            },
                            yaxis:{ labels:{ formatter:(v)=>v.toFixed(0) } },
                            dataLabels:{ enabled:false },
                            plotOptions:{ bar:{ borderRadius:6, columnWidth:'45%' } },
                            grid:{ borderColor:'rgba(0,0,0,.08)' },
                            colors: ['#3e5342ff'] // marrón coherente con el header
                        };
                        const el = document.querySelector("#graficoProceso");
                        if (!el) return;
                        const chart = new ApexCharts(el, options);
                        chart.render();
                    });
                </script>
                @endpush
            @endif
        </div>
    </div>
</div>
