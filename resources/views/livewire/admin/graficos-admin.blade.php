<div class="container py-3">
    <h3 class="mb-3 fw-bold">Gráficos</h3>

    {{-- Selector de usuario + Cargar --}}
    <div class="mb-3">
        <label class="form-label">Avicultor</label>
        <div class="input-group">
            <select class="form-select" wire:model="usuario_id">
                <option value="">— Seleccione un avicultor —</option>
                @foreach($usuarios as $u)
                    <option value="{{ $u->id }}">{{ $u->nombre }} {{ $u->apellido1 }}</option>
                @endforeach
            </select>
            <button class="btn btn-oliva"
                wire:click="cargar"
                >
                <span wire:loading.remove>Cargar</span>
                <span wire:loading> Cargando… </span>
            </button>

        </div>
        @error('usuario_id') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    {{-- Tabla de incubadoras + procesos (del usuario seleccionado) --}}
    <div class="card mb-4">
        <div class="card-header fw-semibold">Incubadoras y procesos</div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="width:60px;">#</th>
                            <th>Incubadora</th>
                            <th>Proceso</th>
                            <th>Gestión (Inicio — Fin estimada)</th>
                            <th>Huevos</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($usuario_id && !empty($filas))
                            @foreach($filas as $i => $fila)
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td class="fw-semibold">{{ $fila['incubadora_codigo'] }}</td>
                                    <td>{{ $fila['nombre'] }}</td>
                                    <td>{{ $fila['gestion_str'] }}</td>
                                    <td>{{ $fila['cantidad_total_huevos'] }}</td>
                                    <td> <div class="row g-3 align-items-center mb-2">
                                        <div class="col-auto">
                                                <label class="col-form-label fw-semibold">Rango del gráfico</label>
                                            </div>
                                            <div class="col-auto">
                                                <select class="form-select" wire:model="rango">
                                                    <option value="realtime">Tiempo real (días restantes)</option>
                                                    <option value="day">Día (procesos iniciados hoy)</option>
                                                    <option value="full">Todo el proceso (histórico por incubadora)</option>
                                                </select>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="5" class="text-center text-muted py-3">
                                    @if(!$usuario_id)
                                        Selecciona un avicultor y pulsa <strong>Cargar</strong>.
                                    @else
                                        No se encontraron procesos para este usuario.
                                    @endif
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>


    {{-- Gráfico principal --}}
    <div class="card">
        <div class="card-header fw-semibold" id="chartTitle">Gráfico</div>
        <div class="card-body">
            <div id="adminMainChart"></div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let adminChart = null;

function renderAdminChart(cfg) {
    const el = document.querySelector('#adminMainChart');
    const titleEl = document.querySelector('#chartTitle');

    if (adminChart) {
        adminChart.destroy();
        adminChart = null;
    }

    titleEl.textContent = cfg.title || 'Gráfico';

    const options = {
        chart: { type: cfg.type || 'line', height: 320 },
        series: cfg.series || [],
        xaxis: { categories: cfg.categories || [] },
        stroke: { width: cfg.type === 'line' ? 3 : 1 },
        markers: { size: cfg.type === 'line' ? 4 : 0 },
        noData: { text: 'Sin datos' },
        legend: { position: 'bottom' },
        tooltip: {
            y: {
                formatter: function (val) {
                    return `${val} Huevos / Errores`;
                }
            }
        }
    };

    adminChart = new ApexCharts(el, options);
    adminChart.render();
}

// Recibe datos desde Livewire
document.addEventListener('livewire:initialized', () => {
    Livewire.on('grafData', (payload) => {
        // payload: {type, title, series, categories}
        renderAdminChart(payload);
    });
});
</script>
@endpush
