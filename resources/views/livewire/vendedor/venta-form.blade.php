
<link rel="stylesheet" href="{{ asset('css/vendedor1.css') }}">
<div class="container py-3">

  <div class="card venta-card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
      <div class="d-flex align-items-center gap-2">
        <i class="bi bi-receipt-cutoff fs-5"></i>
        <h5 class="mb-0 fw-bold">Venta</h5>
      </div>
      <span class="small">Módulo de registro</span>
    </div>

    <div class="card-body">
      <div class="row g-4">
        {{-- ===== Columna principal ===== --}}
        <div class="col-lg-7">

          {{-- Fecha --}}
          <div class="mb-3">
            <label class="form-label">Fecha</label>
            <div class="input-group">
              <span class="input-group-text"><i class="bi bi-calendar-event"></i></span>
              <input type="datetime-local" class="form-control" wire:model="fecha_venta">
            </div>
            @error('fecha_venta') <small class="text-danger">{{ $message }}</small> @enderror
          </div>

          {{-- Avicultor con Autocomplete --}}
          <div class="mb-3 position-relative">
            <label class="form-label">Avicultor</label>
            <div class="input-group">
              <span class="input-group-text"><i class="bi bi-person"></i></span>

              <input
                type="text"
                class="form-control"
                placeholder="Escribe 2+ letras: nombre, usuario o CI/NIT…"
                wire:model.debounce.300ms="buscador"
                wire:keydown.arrow-down.prevent="moveHighlight(1)"
                wire:keydown.arrow-up.prevent="moveHighlight(-1)"
                wire:keydown.enter.prevent="selectHighlight"
                wire:keydown.escape="$set('showSug', false)"
                id="inputBuscadorAvicultor"
              >

              @if($avicultor_id)
                <button class="btn btn-outline-secondary" type="button" title="Limpiar" wire:click="limpiarAvicultor">
                  <i class="bi bi-x-lg"></i>
                </button>
              @endif

              <button class="btn btn-outline-secondary" type="button" title="Buscar" wire:click="abrirSugerencias">
                <i class="bi bi-search"></i>
              </button>
            </div>
           {{-- <small class="text-muted">Usa ↑ ↓ y Enter para seleccionar.</small>

             Lista de sugerencias --}}
            @if($showSug)
              <div class="list-group position-absolute w-100 mt-1 sug-list"
                   style="max-height: 260px; overflow:auto;"
                   wire:click.outside="$set('showSug', false)">
                @forelse($sugerencias as $idx => $sug)
                  <button type="button"
                          class="list-group-item list-group-item-action d-flex justify-content-between align-items-center @if($focusIndex === $idx) active @endif"
                          wire:key="sug-{{ $sug['id'] }}"
                          wire:click="seleccionarAvicultor({{ $sug['id'] }})">
                    <span>{{ $sug['label'] }}</span>
                    <i class="bi bi-arrow-return-left small opacity-75"></i>
                  </button>
                @empty
                  <div class="list-group-item text-muted">Sin resultados…</div>
                @endforelse
              </div>
            @endif

            @error('avicultor_id') <small class="text-danger">{{ $message }}</small> @enderror
            @if($nombre_avicultor)
              <small class="text-success d-block mt-1">Seleccionado: <strong>{{ $nombre_avicultor }}</strong></small>
            @endif
          </div>

          {{-- Incubadora --}}
          <div class="mb-3">
            <label class="form-label">Incubadora (no asignada)</label>
            <div class="input-group">
              <span class="input-group-text"><i class="bi bi-cpu"></i></span>
              <select class="form-select" wire:model.live="incubadora_id">
                <option value="">— Seleccione —</option>
                @foreach($incubadoras as $i)
                  <option value="{{ $i['id'] }}">{{ $i['codigo'] }}</option>
                @endforeach
              </select>
            </div>
            @error('incubadora_id') <small class="text-danger">{{ $message }}</small> @enderror
          </div>

          {{-- Precio --}}
          <div class="mb-1">
            <label class="form-label">Precio Bs.</label>
            <div class="input-group">
              <span class="input-group-text"><i class="bi bi-currency-dollar"></i></span>
              <input type="number" class="form-control" wire:model.live="precio_bs" min="0" step="0.01">
            </div>
            @error('precio_bs') <small class="text-danger">{{ $message }}</small> @enderror
          </div>

        </div>

        {{-- ===== Resumen lateral ===== --}}
        <div class="col-lg-5">
          <div class="p-3 h-100 summary">
            <div class="d-flex align-items-center gap-2 mb-2">
              <i class="bi bi-clipboard-check fs-5 text-success"></i>
              <span class="fw-semibold">Resumen</span>
            </div>

            @php
              $incSel = collect($incubadoras)->firstWhere('id', (int)$incubadora_id);
            @endphp

            <div class="mb-2">
              <span class="text-muted d-block small">Avicultor</span>
              <div class="lead">{{ $nombre_avicultor ?: '—' }}</div>
            </div>

            <div class="mb-2">
              <span class="text-muted d-block small">Incubadora</span>
              <div class="lead">
                {{ $incSel['codigo'] ?? '—' }}
                @if($incubadora_id)
                  <span class="badge badge-soft ms-2">Libre</span>
                @endif
              </div>
            </div>

            <div class="mt-3 pt-2 border-top">
              <div class="d-flex justify-content-between align-items-center">
                <span class="fw-semibold">Total</span>
                <span class="fs-4 fw-bold text-success">
                  {{ $precio_bs !== null && $precio_bs !== '' ? number_format((float)$precio_bs, 2, '.', ',') : '0.00' }} Bs
                </span>
              </div>
            </div>
          </div>
        </div>

      </div> {{-- row --}}
    </div>

    <div class="card-footer d-flex justify-content-end gap-2">
      <button class="btn btn-success btn-cta"
              wire:click="guardar"
              @disabled(!$avicultor_id || !$incubadora_id || !$precio_bs)
              wire:loading.attr="disabled"
              wire:target="guardar">
        <span wire:loading wire:target="guardar" class="spinner-border spinner-border-sm me-2"></span>
        <i class="bi bi-check2-circle me-1"></i> Guardar
      </button>
    </div>
  </div>

  {{-- Autofocus (opcional) --}}
  @push('scripts')
  <script>
    document.addEventListener('livewire:initialized', () => {
      // si quieres autofocus al cargar la pantalla:
      setTimeout(() => {
        const i = document.getElementById('inputBuscadorAvicultor');
        if (i) i.focus();
      }, 150);
    });
  </script>
  @endpush
</div>
