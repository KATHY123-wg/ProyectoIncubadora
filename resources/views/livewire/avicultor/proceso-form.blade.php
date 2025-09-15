<div> 
    <h4 class="mb-3">Iniciar Proceso de Incubación</h4>

    {{-- Selección de incubadora (filtra la tabla y se usa para crear) --}}
    
    <div class="row mb-3">
    <!-- Columna: Incubadora -->
    <div class="col-md-4">
        <label class="form-label">Incubadora</label>
        <div class="input-group align-items-center">
            <select class="form-select form-select-sm w-100"
                    style="max-width: 240px;"
                    wire:model="incubadora_id"
                    wire:change="cargarProcesos">
                <option value="">Seleccione una incubadora...</option>
                @foreach($incubadoras as $incubadora)
                    <option value="{{ $incubadora->id }}">{{ $incubadora->codigo }}</option>
                @endforeach
            </select>
        </div>
        @error('incubadora_id') 
            <span class="text-danger">{{ $message }}</span> 
        @enderror
    </div>

    <!-- Columna: Buscador -->
    <div class="col-md-4 d-flex align-items-end">
        <input type="text" placeholder="Buscar..." 
               class="form-control bg-transparent" 
               id="in-busc" 
               onkeyup="BuscadorTabla('list')">
    </div>

    <!-- Columna: Botón -->
    <div class="col-md-4 d-flex align-items-end justify-content-end">
        <button class="btn btn-success px-4" wire:click="irACrear">
            <i class="bi bi-plus-lg me-1"></i> Crear nuevo proceso
        </button>
    </div>
</div>

    {{-- ======= LISTADO DE PROCESOS DE LA INCUBADORA (con estilo) ======= --}}
    <style>
      :root{
        --olive:#adb57c;
        --olive-50:#f3f6ec;
        --olive-100:#e6edd8;
        --olive-200:#d9e4c4;
      }
      .table-wrap-procesos{
        overflow:hidden;
        box-shadow:0 6px 18px rgba(111, 96, 96, 0.06);
      }
      .table-procesos thead th{
        position:sticky; top:0; z-index:1;
        background:linear-gradient(180deg, var(--olive), #5f663a);
        color:#fff; text-transform:uppercase; letter-spacing:.3px;
        border:none!important;
      }
      .table-procesos td, .table-procesos th{
        padding:.85rem 1rem!important; vertical-align:middle;
      }
      .table-procesos tbody tr{ transition:transform .08s ease, background-color .12s ease; }
      .table-procesos tbody tr:hover{ background:var(--olive-50); transform:scale(1.002); }
      .chip-nro{
        display:inline-block; min-width:2.1rem; text-align:center;
        padding:.25rem .55rem; border-radius:999px; font-weight:700;
        background:var(--olive-100); color:#37401b; border:1px solid var(--olive-200);
      }
      .badge-soft{
        background:var(--olive-50); color:#3b431c; font-weight:600;
        border:1px solid var(--olive-200);
      }
      .acciones .btn{ border-radius:10px; }
    </style>

    <div class="card mb-3">
      <div class="card-header fw-semibold d-flex align-items-center gap-2">
        <i class="bi bi-clock-history"></i> Historial de procesos
      </div>

      <div class="card-body p-0 table-wrap-procesos">
        <div class="table-responsive">
          <table class="table table-hover table-striped mb-0 align-middle table-procesos">
            <thead>
              <tr>
                <th style="width:70px">Nro</th>
                <th>Nombre</th>
                <th>Inicio</th>
                <th>Fin Estimado</th>
                <th class="text-center" style="width:110px">Huevos</th>
                <th style="width:220px" class="text-center">Acciones</th>
              </tr>
            </thead>
            <tbody id="list">
            @if($incubadora_id && !empty($procesos) && count($procesos))
              @foreach($procesos as $i => $p)
                @php
                  $ini = $p->fecha_inicio ? \Carbon\Carbon::parse($p->fecha_inicio) : null;
                  $fin = $p->fecha_fin_estimada
                        ? \Carbon\Carbon::parse($p->fecha_fin_estimada)
                        : ($ini ? $ini->copy()->addDays(21) : null);
                @endphp
                <tr>
                  <td><span class="chip-nro">{{ $i + 1 }}</span></td>
                  <td class="fw-semibold">{{ $p->nombre }}</td>
                  <td><i class="bi bi-calendar2 me-1 opacity-75"></i>{{ $ini ? $ini->format('d/m/Y H:i') : '—' }}</td>
                  <td><i class="bi bi-egg-fried me-1 opacity-75"></i>{{ $fin ? $fin->format('d/m/Y H:i') : '—' }}</td>
                  <td class="text-center"><span class="badge badge-soft">{{ $p->cantidad_total_huevos }}</span></td>
                  <td class="text-center acciones">
                    <button class="btn btn-sm btn-primary me-1"
                            wire:click="editarProceso({{ $p->id }})"
                            data-bs-toggle="tooltip" data-bs-title="Editar proceso">
                      <i class="bi bi-pencil-square"></i>
                    </button>
                    <button class="btn btn-sm btn-info me-1"
                            wire:click="verProceso({{ $p->id }})"
                            data-bs-toggle="tooltip" data-bs-title="Ver detalle">
                      <i class="bi bi-eye"></i>
                    </button>
                    <button class="btn btn-sm btn-danger"
                            wire:click="eliminarProceso({{ $p->id }})"
                            onclick="return confirm('¿Eliminar proceso?')"
                            data-bs-toggle="tooltip" data-bs-title="Eliminar">
                      <i class="bi bi-trash"></i>
                    </button>
                  </td>
                </tr>
              @endforeach
            @else
              <tr>
                <td colspan="6" class="text-center text-muted py-4">
                  <i class="bi bi-inboxes me-1"></i>
                  @if(!$incubadora_id)
                    Selecciona una incubadora.
                  @else
                    No hay procesos registrados para esta incubadora.
                  @endif
                </td>
              </tr>
            @endif
            </tbody>
          </table>
        </div>
      </div>

      
    </div>

    {{-- Inicializa tooltips Bootstrap si están cargados --}}
    <script>
      document.addEventListener('DOMContentLoaded', () => {
        if (!window.bootstrap) return;
        document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => new bootstrap.Tooltip(el));
      });
    </script>

    {{-- ======= MODAL Crear/Editar Proceso (con selector de incubadora) ======= --}}
    <style>
      .modal { z-index: 2000; }
      .modal-backdrop { z-index: 1990; }
    </style>

    <div wire:ignore.self class="modal fade" id="modalProceso" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="border-radius: 16px;">
          <div class="modal-header">
            <h5 class="modal-title">
                {{ ($modoEdicion ?? false) ? 'Editar Proceso de Incubación' : 'Iniciar Proceso de Incubación' }}
            </h5>
            <button type="button" class="btn-close" aria-label="Close"
                    wire:click="cancelarCrear"></button>
          </div>

          <div class="modal-body">
            @if($modoEdicion ?? false)
              <div class="alert alert-warning py-2 mb-3">
                Editando proceso: <strong>{{ $nombre }}</strong>
              </div>
            @endif

            {{-- ⬇️ NUEVO: elegir incubadora dentro del modal (requerido al crear) --}}
            <div class="mb-3">
              <label class="form-label">Incubadora</label>
              <select class="form-select"
                      wire:model="incubadora_id"
                      @disabled($modoEdicion)>
                <option value="">Seleccione una incubadora...</option>
                @foreach($incubadoras as $inc)
                  <option value="{{ $inc->id }}">{{ $inc->codigo }}</option>
                @endforeach
              </select>
              @if($modoEdicion)
                <small class="text-muted">En edición no se permite cambiar la incubadora.</small>
              @endif
              @error('incubadora_id') <span class="text-danger">{{ $message }}</span> @enderror
            </div>

            <div class="mb-3">
              <label class="form-label">Nombre del Proceso</label>
              <input
                type="text"
                id="inputNombreProceso"
                class="form-control input-editable"
                wire:model="nombre"
                maxlength="50"
                pattern="[A-Z0-9\s\-\._]+"
                oninput="this.value = this.value.toUpperCase()"
                placeholder="PROCESO">
              @error('nombre') <span class="text-danger">{{ $message }}</span> @enderror
            </div>

            <div class="mb-3">
              <label class="form-label">Fecha de Inicio</label>
              <input type="datetime-local" class="form-control" wire:model="fecha_inicio">
              @error('fecha_inicio') <span class="text-danger">{{ $message }}</span> @enderror
            </div>

            <div class="mb-3">
              <label class="form-label">Cantidad de Huevos</label>
              <input type="number" class="form-control"
                     wire:model="cantidad_total_huevos"
                     min="1" max="35" step="1" inputmode="numeric"
                     oninput="this.value = this.value.replace(/[^0-9]/g,'')">
              @error('cantidad_total_huevos') <span class="text-danger">{{ $message }}</span> @enderror
            </div>

            <div class="mb-3">
              <label class="form-label">Observaciones</label>
              <textarea class="form-control" wire:model="observaciones"></textarea>
            </div>

            @if ($fecha_eclosion_estimada)
              <div class="alert alert-info mt-2 mb-0">
                Fecha estimada de eclosión: <strong>{{ $fecha_eclosion_estimada }}</strong>
              </div>
            @endif
          </div>

          <div class="modal-footer">
            @if($modoEdicion ?? false)
              <button class="btn btn-primary" wire:click="actualizarProceso" type="button">Actualizar</button>
              <button type="button" class="btn btn-secondary" wire:click="cancelarCrear">Cancelar</button>
            @else
              <button class="btn btn-success" wire:click="iniciarProceso" type="button">Iniciar Proceso</button>
              <button type="button" class="btn btn-secondary" wire:click="cancelarCrear">Cancelar</button>
            @endif
          </div>
        </div>
      </div>
    </div>

    {{-- Control del modal (sin moverlo al body) --}}
    <script>
      document.addEventListener('abrir-modal-proceso', () => {
        const el = document.getElementById('modalProceso');
        if (!el) return;
        if (window.bootstrap?.Modal) {
          const modal = bootstrap.Modal.getOrCreateInstance(el, { backdrop: 'static', keyboard: false });
          modal.show();
        } else {
          el.classList.add('show'); el.style.display = 'block';
          document.body.classList.add('modal-open');
          if (!document.getElementById('bk-modal-proceso')) {
            const bk = document.createElement('div');
            bk.className = 'modal-backdrop fade show';
            bk.id = 'bk-modal-proceso';
            document.body.appendChild(bk);
          }
        }
        setTimeout(() => { const inp = document.getElementById('inputNombreProceso'); if (inp) { inp.focus(); inp.select(); } }, 250);
      });

      document.addEventListener('cerrar-modal-proceso', () => {
        const el = document.getElementById('modalProceso');
        if (!el) return;
        if (window.bootstrap?.Modal) {
          const modal = bootstrap.Modal.getOrCreateInstance(el); modal.hide();
        } else {
          el.classList.remove('show'); el.style.display = 'none';
          document.body.classList.remove('modal-open');
          document.getElementById('bk-modal-proceso')?.remove();
        }
      });
    </script>

    {{-- ===================== EXTRA: CSS anti bloqueo y prioridad de modal ===================== --}}
    <style>
      .modal{ z-index:5000 !important; }
      .modal-backdrop{ z-index:4990 !important; }
      .modal-backdrop.show{ opacity:.25; }
      .modal, .modal *{ pointer-events:auto !important; }
    </style>

    {{-- ===================== EXTRA: Limpieza de backdrops “fantasma” ===================== --}}
    <script>
      (function(){
        const ID='modalProceso';
        const getEl=()=>document.getElementById(ID);
        function limpiarBackdrops(){
          document.querySelectorAll('.modal-backdrop').forEach(b=>b.remove());
          document.body.classList.remove('modal-open');
          document.body.style.removeProperty('padding-right');
        }
        document.addEventListener('abrir-modal-proceso',()=>{
          const el=getEl(); if(!el) return;
          limpiarBackdrops();
          if(window.bootstrap?.Modal){
            const m=bootstrap.Modal.getOrCreateInstance(el,{backdrop:'static',keyboard:false}); m.show();
          }else{
            el.classList.add('show'); el.style.display='block';
            document.body.classList.add('modal-open');
            const bk=document.createElement('div'); bk.className='modal-backdrop fade show'; document.body.appendChild(bk);
          }
          setTimeout(()=>{ const inp=document.getElementById('inputNombreProceso'); if(inp){ inp.focus(); inp.select(); } },250);
        });
        document.addEventListener('cerrar-modal-proceso',()=>{
          const el=getEl(); if(!el) return;
          if(window.bootstrap?.Modal){ const m=bootstrap.Modal.getOrCreateInstance(el); m.hide(); }
          else{ el.classList.remove('show'); el.style.display='none'; }
          limpiarBackdrops();
        });
      })();
    </script>
    <script>
    function BuscadorTabla(tabla){
    var tabla = tabla;
    var input, filter, table, tr, td, i, j, visible;
    input = document.getElementById("in-busc");
    filter = input.value.toUpperCase();
    table = document.getElementById(tabla);
    tr = table.getElementsByTagName("tr");
  
    for (i = 0; i < tr.length; i++) {
      visible = false;
      td = tr[i].getElementsByTagName("td");
      for (j = 0; j < td.length; j++) {
        if (td[j] && td[j].innerHTML.toUpperCase().indexOf(filter) > -1) {
          visible = true;
        }
      }
      if (visible === true) {
        tr[i].style.display = "";
      } else {
        tr[i].style.display = "none";
      }
    }
    var tt = document.getElementById("tt");
    tt.style.display ="";
}
</script>
</div>
