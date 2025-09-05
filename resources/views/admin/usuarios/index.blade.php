@extends('admin.inicioadmin')

@section('titulo', 'Usuarios')
@section('contenido')
<div class="container">
    <h2 class="mb-4">Lista de Usuarios</h2>
    <div class="row g-2 mb-3">
        <div style="width: 100%; max-width: 300px;" class="p-3">
            <input type="text" placeholder="Buscar..." class="form-control bg-transparent" id="in-busc" onkeyup="BuscadorTabla('list')">
        </div>
        <div style="width: 100%; max-width: 500px;" class="p-3">
          <a href="{{ route('usuarios.create') }}" class="btn btn-oliva px-5 mb-3">Registrar Nuevo Usuario</a>
        </div>
    </div>
<div class="overflow-x-auto">
    <table  class="table table-bordered">
        <thead class="table-dark">
            <tr>
                <th>Nro</th> {{-- Nueva columna de numeración --}}
                <th>Nombre Completo</th>
                <th>Usuario</th>
                <th>Correo</th>
                <th>Rol</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody id="list">
            @foreach($usuarios as $usuario)
            <tr>
                <td>{{ $loop->iteration }}</td> {{-- Número de fila --}}
                <td>
                    {{ $usuario->nombre }} {{ $usuario->apellido1 }}{{ $usuario->apellido2 ? ' ' . $usuario->apellido2 : '' }}
                </td>
                <td>{{ $usuario->usuario }}</td>
                <td>{{ $usuario->correo }}</td>
                <td>{{ $usuario->rol }}</td>
                <td>{{ $usuario->estado ? 'Activo' : 'Inactivo' }}</td>
                <td>
                   <a href="{{ route('usuarios.edit', $usuario->id) }}" class="btn btn-sm btn-warning" title="Editar">
    <i class="bi bi-pencil-square"></i>
</a>

@if($usuario->estado)
    {{-- Botón DESACTIVAR --}}
    <form action="{{ route('usuarios.destroy', $usuario->id) }}" method="POST" class="d-inline form-eliminar">
        @csrf @method('DELETE')
        <button type="button" class="btn btn-sm btn-olive btn-eliminar"
                data-nombre="{{ $usuario->nombre }} {{ $usuario->apellido1 }}" title="Desactivar">
            <i class="bi bi-person-dash"></i>
        </button>
    </form>
@else
    {{-- Botón ACTIVAR --}}
    <form action="{{ route('usuarios.activar', $usuario->id) }}" method="POST" class="d-inline form-activar">
        @csrf
        <button type="button" class="btn btn-sm btn-success btn-activar"
                data-nombre="{{ $usuario->nombre }} {{ $usuario->apellido1 }}" title="Activar">
            <i class="bi bi-person-check"></i>
        </button>
    </form>
@endif
 {{--
@if($usuario->rol === 'avicultor')
    <button type="button"
            class="btn btn-sm btn-oliva btn-abrir-asignar"
            data-usuario-id="{{ $usuario->id }}"
            data-usuario-nombre="{{ $usuario->nombre }} {{ $usuario->apellido1 }}"
            title="Asignar incubadora">
        <i class="bi bi-hdd-network"></i>
    </button>
@endif--}}



                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Confirmación para DESACTIVAR
    document.querySelectorAll('.btn-eliminar').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            const form = e.target.closest('form');
            const nombre = e.target.dataset.nombre || 'este usuario';

            Swal.fire({
                title: '¿Desactivar usuario?',
                text: `Se desactivará a ${nombre}. No podrá iniciar sesión.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, desactivar',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d'
            }).then((result) => {
                if (result.isConfirmed) form.submit();
            });
        });
    });

    // Confirmación para ACTIVAR
    document.querySelectorAll('.btn-activar').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            const form = e.target.closest('form');
            const nombre = e.target.dataset.nombre || 'este usuario';

            Swal.fire({
                title: '¿Activar usuario?',
                text: `Se activará a ${nombre}. Podrá iniciar sesión nuevamente.`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sí, activar',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#198754', // Bootstrap success
                cancelButtonColor: '#6c757d'
            }).then((result) => {
                if (result.isConfirmed) form.submit();
            });
        });
    });
});
</script>
{{-- MODAL: Asignar incubadora --}}
<div class="modal fade" id="modalAsignarInc" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border-radius:16px;">
      <div class="modal-header">
        <h5 class="modal-title">
            Asignar incubadora a <span id="asignarNombre"></span>
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>

      <form id="formAsignarInc" method="POST">
        @csrf
        <div class="modal-body">

          @if(isset($incubadorasDisponibles) && $incubadorasDisponibles->isNotEmpty())
              <div class="mb-3">
                  <label class="form-label">Incubadora disponible</label>
                  <select name="incubadora_id" class="form-select" required>
                      <option value="">— Selecciona —</option>
                      @foreach($incubadorasDisponibles as $inc)
                          <option value="{{ $inc->id }}">
                              {{ $inc->codigo }} — {{ $inc->descripcion ?? 'Sin descripción' }}
                          </option>
                      @endforeach
                  </select>
              </div>
          @else
              <div class="alert alert-info mb-0">
                No hay incubadoras disponibles en este momento.
              </div>
          @endif

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-oliva"
                  @if(empty($incubadorasDisponibles) || $incubadorasDisponibles->isEmpty()) disabled @endif>
            Asignar
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- JS: abre modal, coloca nombre y setea action del form --}}
<script>
(function(){
  const modalEl = document.getElementById('modalAsignarInc');
  const nombreEl = document.getElementById('asignarNombre');
  const form = document.getElementById('formAsignarInc');

  function abrirModal() {
    if (window.bootstrap?.Modal) {
      bootstrap.Modal.getOrCreateInstance(modalEl, {backdrop:'static', keyboard:false}).show();
    } else {
      modalEl.classList.add('show'); modalEl.style.display='block';
      document.body.classList.add('modal-open');
      if (!document.getElementById('bk-asignar')) {
        const bk=document.createElement('div');
        bk.id='bk-asignar'; bk.className='modal-backdrop fade show';
        document.body.appendChild(bk);
      }
    }
  }
  function cerrarModal() {
    if (window.bootstrap?.Modal) {
      bootstrap.Modal.getOrCreateInstance(modalEl).hide();
    } else {
      modalEl.classList.remove('show'); modalEl.style.display='none';
      document.body.classList.remove('modal-open');
      document.getElementById('bk-asignar')?.remove();
    }
  }

  document.addEventListener('click', function(e){
    const btn = e.target.closest('.btn-abrir-asignar');
    if (!btn) return;

    const id = btn.getAttribute('data-usuario-id');
    const nombre = btn.getAttribute('data-usuario-nombre') || 'este usuario';
    nombreEl.textContent = nombre;
    form.action = "{{ url('/admin/usuarios') }}/" + encodeURIComponent(id) + "/asignar-incubadora";
    abrirModal();
  });

  // ✅ Fallback de cierre (si no hay bootstrap.bundle.js):
  // Clic en botones con data-bs-dismiss="modal"
  modalEl.addEventListener('click', function(e){
    if (e.target.closest('[data-bs-dismiss="modal"]')) cerrarModal();
  });
  // Tecla ESC
  document.addEventListener('keydown', function(e){
    if (e.key === 'Escape' && modalEl.classList.contains('show')) cerrarModal();
  });
  // (opcional) Clic en el fondo del modal (fuera del diálogo)
  modalEl.addEventListener('mousedown', function(e){
    if (e.target === modalEl) cerrarModal();
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

@endsection
