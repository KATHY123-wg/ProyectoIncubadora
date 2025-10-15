<link rel="stylesheet" href="{{ asset('css/reportesadmin.css') }}">
<div class="container mt-4">
    <h2 class="text-center mb-4 fw-bold">📊 Reporte de Procesos</h2>

    <div class="row mb-4">
        <div class="col-md-4">
            <label>Seleccionar Usuario</label>
            <select wire:model="usuarioId" class="form-select">
                <option value="">Selecciona </option>
                @foreach($usuarios as $usuario)
                    <option value="{{ $usuario->id }}">{{ $usuario->nombre }} {{ $usuario->apellido1 }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-4">
            <label class="form-label fw-bold">Seleccionar Incubadora</label>

            <div class="input-group input-group-sm">
                <select wire:model="incubadora_id" class="form-select" @disabled(empty($incubadoras))>
                    <option value=""> Selecciona </option>
                    @foreach($incubadoras as $incubadora)
                        <option value="{{ $incubadora->id }}">{{ $incubadora->codigo }}</option>
                    @endforeach
                </select>

                <button type="button"
                        class="btn btn-olive btn-outline-secondary"
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


        <div class="col-md-4">
            <label>Gestión</label>
            <select wire:model="gestion" class="form-select">
                <option value=""> Selecciona </option>
                @for($y = now()->year; $y >= now()->year - 5; $y--)
                    <option value="{{ $y }}">{{ $y }}</option>
                @endfor
            </select>
        </div>
    </div>

    <div class="text-center">
        <button wire:click="buscarProceso" class="btn btn-oliva px-5">Generar Reporte</button>
    </div>

    

    @if($proceso)
        <div class="mt-5">
            <h4 class="text-center">Proceso de Incubadora <span class="text-primary fw-bold">{{ $proceso->incubadora->codigo }}</span></h4>
            <table class="table table-bordered mt-3">
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

            <div class="mt-5">
                <h5 class="text-center">Visualización del Proceso</h5>
                <div id="graficoProceso" style="height: 350px;"></div>
            </div>

            @push('scripts')
            <script>
                document.addEventListener('renderProcesoChart', event => {
                    const data = event.detail;

                    const options = {
                        chart: { type: 'bar', height: 350 },
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
                            ]
                        },
                        colors: ['#5D4037']
                    };

                    const chart = new ApexCharts(document.querySelector("#graficoProceso"), options);
                    chart.render();
                });
            </script>
            @endpush
        </div>
    @endif
</div>
