<div>
    <div class="row mb-3">
        <div class="col-md-4">
            <label>Incubadora</label>
            <select wire:model="incubadoraId" class="form-select">
                <option value="">Seleccione</option>
                @foreach($incubadoras as $i)
                    <option value="{{ $i->id }}">{{ $i->codigo }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-4">
            <label>Gestión</label>
            <select wire:model="gestion" class="form-select">
                <option value="">Seleccione</option>
                @for ($year = now()->year; $year >= 2020; $year--)
                    <option value="{{ $year }}">{{ $year }}</option>
                @endfor
            </select>
        </div>

       {{-- <div class="col-md-4">
            <label>Mes</label>
            <select wire:model="mes" class="form-select">
                <option value="">Seleccione</option>
                @foreach ([1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto', 9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'] as $num => $mesNombre)
                    <option value="{{ $num }}">{{ $mesNombre }}</option>
                @endforeach
            </select>
        </div>
        --}}
    </div>
    <div class="mb-3">
            <button wire:click="generarDatos" class="btn btn-oliva px-5">Generar Gráficos</button>
        </div>

    @if($lineChartManana)
        <div class="row">
            <div class="col-md-6">
                @livewireChartsScripts
                <livewire:livewire-line-chart :line-chart-model="$lineChartManana" />
            </div>
            <div class="col-md-6">
                <livewire:livewire-line-chart :line-chart-model="$lineChartNoche" />
            </div>
        </div>
        @livewireChartsScripts
    @endif
</div>

