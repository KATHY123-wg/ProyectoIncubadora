<div class="container py-4">
    <h2 class="fw-bold mb-4">Gestión de Incubadoras</h2>

    {{-- Filtros --}}
    <div class="row g-2 mb-3">
        {{-- 🔒 Solo creación de incubadoras: se quita filtro por avicultor --}}
        {{-- 
        <div class="col-md-3">
            <div class="input-group">
                <select wire:model.defer="filtroUsuario" class="form-select">
                    <option value="">— Todos los avicultores —</option>
                    @foreach($usuariosAvicultores as $u)
                        <option value="{{ $u->id }}">{{ $u->nombre }} {{ $u->apellido1 }}</option>
                    @endforeach
                </select>
                <button type="button" class="btn-olive btn-sm btn-primary" wire:click="aplicarFiltroUsuario" title="Cargar">
                    Cargar
                </button>
            </div>
        </div>
        --}}
        <div style="width: 100%; max-width: 300px;" class="p-3">
            <input type="text" placeholder="Buscar..." class="form-control bg-transparent" id="in-busc" onkeyup="BuscadorTabla('list')">
        </div>

        <div class="col-md-2 ms-auto">
            <button class="btn btn-success w-100" wire:click="crear">REGISTRA NUEVA INCUBADORA</button>
        </div>
    </div>

    {{-- Tabla --}}
    <div class="table-responsive">
        <table class="table table-bordered align-middle">
            <thead class="table-dark">
                <tr>
                    <th>N°</th>
                    <th>Código</th>
                    <th>Descripción</th>
                    <th>Avicultor</th>
                    <th>Estado</th>
                    <th class="text-center" style="width:220px">Acciones</th>
                </tr>
            </thead>
            <tbody id="list">
                @forelse($incubadoras as $inc)
                    <tr>
                        <td>{{ $incubadoras->firstItem() + $loop->index }}</td>
                        <td>{{ $inc->codigo }}</td>
                        <td>{{ $inc->descripcion }}</td>
                        <td>
                            @if($inc->usuario_id)
                                {{ $inc->usuario->nombre ?? '-' }} {{ $inc->usuario->apellido1 ?? '' }}
                            @else
                                <span class="badge bg-info-subtle text-info border border-info">Sin asignar</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge {{ $inc->estado ? 'bg-success' : 'bg-secondary' }}">
                                {{ $inc->estado ? 'Activa' : 'Inactiva' }}
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-2">
                                <div>
                                    <button class="btn btn-sm btn-primary w-100" style="min-width: 40px;"
                                        wire:click="editar({{ $inc->id }})"
                                        title="Editar incubadora">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                </div>
                                <div>
                                    <button class="btn btn-sm btn-warning w-100" style="min-width: 40px;"
                                        wire:click="cambiarEstado({{ $inc->id }})"
                                        title="{{ $inc->estado ? 'Desactivar incubadora' : 'Activar incubadora' }}">
                                        @if($inc->estado)
                                            <i class="bi bi-toggle-off"></i>
                                        @else
                                            <i class="bi bi-toggle-on"></i>
                                        @endif
                                    </button>
                                </div>

                                <div>
                                    {{-- Eliminar (si lo habilitas) --}}
                                    {{-- 
                                    <button class="btn btn-sm btn-danger w-100" style="min-width: 100px;"
                                        wire:click="eliminar({{ $inc->id }})"
                                        onclick="return confirm('¿Eliminar incubadora?')">
                                        Eliminar
                                    </button>
                                    --}}
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted">Sin resultados</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $incubadoras->links() }}

    {{-- Modal Crear/Editar --}}
    @if($mostrarModal)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,.5);">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ $incubadoraId ? 'Editar Incubadora' : 'Nueva Incubadora' }}</h5>
                        <button type="button" class="btn-close" wire:click="$set('mostrarModal', false)"></button>
                    </div>
                    <div class="modal-body">

                        {{-- Aviso: sin asignación aún 
                        <div class="alert alert-info py-2">
                            Esta incubadora se creará <strong>sin asignar</strong> a un avicultor. La asignación se realizará en el módulo correspondiente.
                        </div>--}}

                        <div class="mb-3">
                            <label class="form-label">Código</label>
                            <input
                                type="text"
                                class="form-control {{ $incubadoraId ? 'bg-light' : '' }}"
                                wire:model.defer="codigo"
                                oninput="this.value=this.value.replace(/\s+/g,'').toUpperCase()"
                                maxlength="20"
                                @if($incubadoraId) readonly tabindex="-1" @endif
                                placeholder="EJ: INCU001"
                            >
                            @error('codigo') <small class="text-danger">{{ $message }}</small> @enderror
                            <small class="text-muted">En mayúsculas, sin espacios. Debe ser único.</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Descripción</label>
                            <textarea class="form-control" wire:model.defer="descripcion" maxlength="255" placeholder="Descripción breve"></textarea>
                            @error('descripcion') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        {{-- 🔒 Campo Avicultor oculto temporalmente (no asignar aún) --}}
                        {{-- 
                        <div class="mb-3">
                            <label class="form-label">Avicultor</label>
                            <select class="form-select" wire:model.defer="usuario_id">
                                <option value="">— Seleccione —</option>
                                @foreach($usuariosAvicultores as $u)
                                    <option value="{{ $u->id }}">{{ $u->nombre }} {{ $u->apellido1 }}</option>
                                @endforeach
                            </select>
                            @error('usuario_id') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        --}}

                        {{-- Estado (opcional) --}}
                        {{-- 
                        <div class="mb-2">
                            <label class="form-label">Estado</label>
                            <select class="form-select" wire:model.defer="estado">
                                <option value="1">Activa</option>
                                <option value="0">Inactiva</option>
                            </select>
                            @error('estado') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        --}}
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-light" wire:click="$set('mostrarModal', false)">Cancelar</button>
                        @if($incubadoraId)
                            <button class="btn btn-primary" wire:click="actualizar">Actualizar</button>
                        @else
                            <button class="btn btn-success" wire:click="guardar">Guardar</button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Toast simple --}}
   <script>
  window.addEventListener('toast', e => {
      const { tipo = 'success', msg = '' } = e.detail || {};
      if (!msg) return;

      // Si SweetAlert2 está cargado (lo tienes en tu layout), usa un toast bonito
      if (window.Swal) {
          const Toast = Swal.mixin({
              toast: true,
              position: 'top-end',
              showConfirmButton: false,
              timer: 2500,
              timerProgressBar: true,
          });
          Toast.fire({
              icon: tipo,   // 'success' | 'error' | 'info' | 'warning' | 'question'
              title: msg
          });
      } else {
          // Respaldo: alerta del navegador
          alert((tipo ? (tipo.toUpperCase()+': ') : '') + msg);
      }
  });
</script>

    {{-- ===== Modal: Asignar incubadora a avicultor (NUEVO) ===== --}}
<style>.modal{z-index:2000}.modal-backdrop{z-index:1990}</style>

<div wire:ignore.self class="modal fade" id="modalAsignarInc" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border-radius:16px;">
      <div class="modal-header">
        <h5 class="modal-title">Asignar incubadora</h5>
        <button type="button" class="btn-close" aria-label="Close" wire:click="cerrarAsignar"></button>
      </div>

      <div class="modal-body">
        @if($usuarioAsignarId)
          <div class="mb-3">
            <label class="form-label">Incubadoras disponibles</label>
            <select class="form-select" wire:model="incubadoraSeleccionada">
              <option value="">— Selecciona ..</option>
              @forelse($incubadorasDisponibles as $inc)
                <option value="{{ $inc->id }}">{{ $inc->codigo }} — {{ $inc->descripcion ?? 'Sin descripción' }}</option>
              @empty
                <option value="" disabled>No hay incubadoras disponibles</option>
              @endforelse
            </select>
            @error('incubadoraSeleccionada') <small class="text-danger">{{ $message }}</small> @enderror
          </div>
        @else
          <div class="text-muted">No se recibió el usuario a asignar.</div>
        @endif
      </div>

      <div class="modal-footer">
        <button class="btn btn-secondary" wire:click="cerrarAsignar">Cancelar</button>
        <button class="btn btn-success"
                wire:click="asignarIncubadora"
                wire:loading.attr="disabled"
                wire:target="asignarIncubadora"
                @disabled(!$usuarioAsignarId || empty($incubadorasDisponibles))>
          <span wire:loading.remove wire:target="asignarIncubadora">Asignar</span>
          <span wire:loading wire:target="asignarIncubadora">Asignando...</span>
        </button>
      </div>
    </div>
  </div>
</div>

{{-- Control de apertura/cierre del modal (NUEVO) --}}
<script>
(function(){
  function abrir() {
    const el = document.getElementById('modalAsignarInc');
    if (!el) return;
    if (window.bootstrap?.Modal) {
      bootstrap.Modal.getOrCreateInstance(el, { backdrop:'static', keyboard:false }).show();
    } else {
      el.classList.add('show'); el.style.display='block';
      document.body.classList.add('modal-open');
      if (!document.getElementById('bk-modal-asignar')) {
        const bk=document.createElement('div');
        bk.id='bk-modal-asignar'; bk.className='modal-backdrop fade show';
        document.body.appendChild(bk);
      }
    }
  }
  function cerrar() {
    const el = document.getElementById('modalAsignarInc');
    if (!el) return;
    if (window.bootstrap?.Modal) {
      bootstrap.Modal.getOrCreateInstance(el).hide();
    } else {
      el.classList.remove('show'); el.style.display='none';
      document.body.classList.remove('modal-open');
      document.getElementById('bk-modal-asignar')?.remove();
    }
  }

  // Eventos que dispara tu componente Livewire (this->dispatch(...))
  document.addEventListener('abrir-modal-asignar', abrir);
  document.addEventListener('cerrar-modal-asignar', cerrar);

  // Si el servidor indicó que debe mostrarse al cargar (por ?asignar_para=ID)
  @if(!empty($showAsignarModal))
    document.addEventListener('DOMContentLoaded', abrir);
  @endif
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
