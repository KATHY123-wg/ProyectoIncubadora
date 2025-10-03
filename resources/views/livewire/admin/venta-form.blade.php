<div class="container py-3">
  <style>
    :root{
      --olive:#5f663a;
      --olive-600:#505733;
      --olive-50:#f5f7ef;
      --ink:#1f2a37;
    }
    .venta-card .card-header{
      background:linear-gradient(180deg, var(--olive), var(--olive-600));
      color:#fff; border-bottom:none;
    }
    .venta-card .card-header .small{
      opacity:.9;
    }
    .venta-card .card-body{ background:#fff; }
    .venta-card .form-label{ font-weight:600; letter-spacing:.2px; }
    .venta-card .input-group-text{ background:#f8fafc; }
    .sug-list{
      border-radius:.6rem; overflow:hidden; z-index:1061;  /* sobre modales */
      box-shadow:0 10px 26px rgba(0,0,0,.12);
    }
    .summary{
      background:var(--olive-50);
      border:1px dashed rgba(95,102,58,.35);
      border-radius:14px;
    }
    .summary .lead{ font-weight:700; color:var(--ink); }
    .badge-soft{
      background:#eef2ff; color:#3b82f6; border:1px solid #dbeafe; font-weight:600;
    }
    .btn-cta{
      padding:.625rem 1.2rem; border-radius:12px; font-weight:700;
      box-shadow:0 8px 18px rgba(95,102,58,.18);
    }
  </style>

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
            
              wire:loading.attr="disabled"
              wire:target="guardar">
        <span wire:loading wire:target="guardar" class="spinner-border spinner-border-sm me-2"></span>
        <i class="bi bi-check2-circle me-1"></i> Guardar
      </button>
  {{-- Botón Descargar recibo (solo si ya existe una venta) --}}
      @if($ventaGuardadaId)
        <form action="{{ route('ventas.recibo', ['venta' => $ventaGuardadaId]) }}" method="GET" class="d-inline me-2">
            <button type="submit" class="btn btn-outline-primary btn-cta">
                <i class="bi bi-filetype-pdf me-1"></i> Descargar Recibo
            </button>
        </form>
      @endif




    </div>
  </div>
  <div id="toastContainer"
     class="toast-container position-fixed bottom-0 end-0 p-3"
     style="z-index:3000;"></div>


  @push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  // Autofocus inicial
  setTimeout(() => document.getElementById('inputBuscadorAvicultor')?.focus(), 150);

  // Asegurar contenedor de toasts
  let container = document.getElementById('toastContainer');
  if (!container) {
    container = document.createElement('div');
    container.id = 'toastContainer';
    container.className = 'toast-container position-fixed bottom-0 end-0 p-3';
    container.style.zIndex = 3000;
    document.body.appendChild(container);
  }

  // Mostrar toast
  const showToast = (payload = {}) => {
    const tipo = (payload.tipo || 'info').toLowerCase();
    const msg  = String(payload.msg ?? '').trim();

    const cls =
      tipo === 'success' ? 'text-bg-success' :
      (tipo === 'error' || tipo === 'danger') ? 'text-bg-danger' :
      tipo === 'warning' ? 'text-bg-warning text-dark' :
      tipo === 'info'    ? 'text-bg-info text-dark' :
      'text-bg-primary';

    const el = document.createElement('div');
    el.className = `toast ${cls} border-0`;
    el.setAttribute('role','alert');
    el.setAttribute('aria-live','assertive');
    el.setAttribute('aria-atomic','true');
    el.style.minWidth = '320px';
    el.style.borderRadius = '12px';
    el.innerHTML = `
      <div class="d-flex align-items-center">
        <div class="toast-body" style="font-size:.95rem;line-height:1.35;">${msg}</div>
        <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
      </div>
    `;
    container.appendChild(el);
    new bootstrap.Toast(el, { delay: 3500 }).show();
    el.addEventListener('hidden.bs.toast', () => el.remove());
  };

  // Livewire v3 -> Browser Event ($this->dispatch('toast', ...))
  window.addEventListener('toast', (e) => showToast(e.detail || {}));

  // (Opcional) por si en algún sitio usas Livewire.on('toast', ...)
  if (window.Livewire?.on) {
    Livewire.on('toast', showToast);
    Livewire.on('focus', (p={}) => p.id && document.getElementById(p.id)?.focus());
  }
});
</script>
{{--factura de venta--}}
<script>
window.addEventListener('venta-guardada', event => {
    const url = event.detail.url;
    const msg = event.detail.message ?? 'Venta guardada';
    // mostrar toast (si ya tienes toast con Livewire, puedes usarlo)
    // Abrir factura en nueva pestaña
    if (url) {
        window.open(url, '_blank'); // abrir en nueva pestaña
    } else {
        alert(msg);
    }
});
</script>

@endpush

</div>
