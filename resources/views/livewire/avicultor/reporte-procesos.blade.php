<div class="container mt-4">

    <div class="report-shell">
        {{-- Header --}}
        <div class="report-head">
            <h3 class="fw-bold">Reporte de Procesos de Incubación</h3>
            <div class="report-sub">Filtra por incubadora y gestión para ver los procesos registrados.</div>
        </div>

        {{-- Filtros --}}
        <div class="toolbar">
            <div class="container py-3">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Incubadora</label>
                        <select wire:model="incubadora_id" class="form-select">
                            <option value="">Seleccione incubadora</option>
                            @foreach($incubadoras as $i)
                                <option value="{{ $i->id }}">{{ $i->codigo }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Gestión</label>
                        <select wire:model="gestion" class="form-select">
                            <option value="">Seleccione gestión</option>
                            @for ($y = now()->year; $y >= 2024; $y--)
                                <option value="{{ $y }}">{{ $y }}</option>
                            @endfor
                        </select>
                    </div>

                    {{-- Si reactivas MES, este slot queda alineado
                    <div class="col-md-4">
                        <label class="form-label">Mes</label>
                        <select wire:model="mes" class="form-select">
                            <option value="">Seleccione mes</option>
                            @foreach ([1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto', 9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'] as $num => $nombre)
                                <option value="{{ $num }}">{{ $nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    --}}

                    <div class="col-md-4 ms-auto d-flex align-items-end">
                        <button wire:click="generar" class="btn btn-oliva px-4 fw-semibold w-100">
                            Generar Reporte
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @php
  $qs = [
    'incubadora_id' => $incubadora_id,
    'gestion'       => $gestion,
    'mes'           => $mes,
  ];
  $disabled = (!$incubadora_id || !$gestion) ? 'disabled' : '';
@endphp

<div class="d-flex gap-2 mb-2">
  <a href="{{ route('avicultor.procesos.pdf', $qs) }}"
     class="btn btn-outline-danger {{ $disabled }}">
     Exportar PDF
  </a>
  <a href="{{ route('avicultor.procesos.xls', $qs) }}"
     class="btn btn-outline-success {{ $disabled }}">
     Exportar XLS
  </a>
</div>


        {{-- Tabla / Resultados --}}
        <div class="section">
            <div class="section-title">Resultados</div>

            @if(count($procesos))
                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead>
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
                                    <td class="fw-semibold">{{ $p['codigo_incubadora'] }}</td>
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
                <div class="alert alert-warning mt-2">
                    No hay procesos para los filtros seleccionados.
                </div>
            @endif

            <div class="mt-3">
                <button type="button" class="btn btn-back" onclick="history.back();">
                    <i class="bi bi-arrow-left"></i> Volver
                </button>
            </div>
        </div>
    </div>
</div>
