<div class="container mt-4">
    <h2 class="text-center mb-4 fw-bold">📊 Reporte de Ventas</h2>

    <div class="row mb-3">
        <div class="col-md-4">
            <label>Cajero</label>
            <select wire:model="usuario_id" class="form-select">
                <option value="">-- Seleccione --</option>
                @foreach($usuarios as $usuario)
                    <option value="{{ $usuario->id }}">{{ $usuario->nombre }} {{ $usuario->apellido1 }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-4">
            <label>Gestión</label>
            <select wire:model="gestion" class="form-select">
                <option value="">-- Seleccione --</option>
                @for($y = now()->year; $y >= now()->year - 5; $y--)
                    <option value="{{ $y }}">{{ $y }}</option>
                @endfor
            </select>
        </div>

      {{--  <div class="col-md-4">
            <label>Mes</label>
            <select wire:model="mes" class="form-select">
                <option value="">Todos</option>
                @foreach(range(1, 12) as $m)
                    <option value="{{ $m }}">{{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}</option>
                @endforeach
            </select>
        </div>--}}
    </div>

    <div class="text-center">
       <button wire:click="generar" class="btn btn-oliva px-5">Generar</button>

    </div>

    @if($ventas)
        <div class="mt-4">
            <h4 class="mb-3 fw-bold">Vendedor: {{ $nombreCajero }} | Gestión: {{ $gestion }}</h4>

            <table class="table table-bordered">
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
            <td>{{ $detalle->incubadora->codigo }}</td>
            <td>{{ $venta->avicultor->nombre ?? 'No registrado' }}</td>
            <td>Bs {{ number_format($detalle->precio_unitario, 2) }}</td>
            <td>{{ \Carbon\Carbon::parse($venta->fecha_venta)->format('d/m/Y') }}</td>
        </tr>
    @endforeach
@empty
    <tr><td colspan="4" class="text-danger text-center">No hay resultados</td></tr>
@endforelse

                </tbody>
            </table>
            <div class="mt-5">
    <h5 class="text-center">Total de Ventas </h5>
    <div id="graficoVentas" style="height: 350px;"></div>
</div>

        @push('scripts')
            <script>
            let ventasChart = null;

            window.addEventListener('renderVentasChart', (event) => {
                const data = event.detail;

                const el = document.querySelector("#graficoVentas");
                if (!el) return;

                // Destruir gráfico previo si existe
                if (ventasChart) {
                    ventasChart.destroy();
                    ventasChart = null;
                }

                const options = {
                    chart: { type: 'bar', height: 350 },
                    series: [{ name: 'Total Bs', data: data.montos }],
                    xaxis: { categories: data.dias },
                    colors: ['#A1887F']
                };

                ventasChart = new ApexCharts(el, options);
                ventasChart.render();
            });
            </script>
            @endpush


                </div>
            @endif
</div>

