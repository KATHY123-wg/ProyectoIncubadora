<div class="container mt-4">
    <h3 class="mb-3 fw-bold">Reporte de Procesos de Incubación</h3>
    


    <div class="row mb-3">
        <div class="col-md-4">
            <label class="form-label fw-bold">Incubadora</label>
            <select wire:model="incubadora_id" class="form-select">
                <option value="">Seleccione incubadora</option>
                @foreach($incubadoras as $i)
                    <option value="{{ $i->id }}">{{ $i->codigo }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-4">
            <label class="form-label fw-bold">Gestión</label>
            <select wire:model="gestion" class="form-select">
                <option value="">Seleccione gestión</option>
                @for ($y = now()->year; $y >= 2020; $y--)
                    <option value="{{ $y }}">{{ $y }}</option>
                @endfor
            </select>
        </div>

       {{-- <div class="col-md-4">
            <label class="form-label fw-bold">Mes</label>
            <select wire:model="mes" class="form-select">
                <option value="">Seleccione mes</option>
                @foreach ([1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto', 9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'] as $num => $nombre)
                    <option value="{{ $num }}">{{ $nombre }}</option>
                @endforeach
            </select>
        </div>--}}
    </div>
    <div class="mb-3">
        <button wire:click="generar" class="btn btn-oliva px-5">Generar Reporte</button>
    </div>

    @if(count($procesos))
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>Incubadora</th>
                    <th>Nombre del Proceso</th>
                    <th>Fecha Inicio</th>
                    <th>Fecha Fin</th>
                    <th>Huevos Eclosionados</th>
                    <th>Error Motor</th>
                    <th>Error Lámpara</th>
                    <th>Error Sensor</th>
                </tr>
            </thead>
            <tbody>
                @foreach($procesos as $p)
                    <tr>
                        <td>{{ $p['codigo_incubadora'] }}</td>
                        <td>{{ $p['nombre'] }}</td>
                        <td>{{ $p['fecha_inicio'] }}</td>
                        <td>{{ $p['fecha_fin'] }}</td>
                        <td>{{ $p['huevos_eclosionados'] }}</td>
                        <td>{{ $p['errores_motor'] }}</td>
                        <td>{{ $p['errores_lampara'] }}</td>
                        <td>{{ $p['errores_sensor'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
        <div class="alert alert-warning">No hay procesos para los filtros seleccionados.</div>
    @endif
    <button type="button" class="btn btn-secondary" onclick="history.back();">
         <i class="bi bi-arrow-left"></i> Volver
    </button>
</div>

