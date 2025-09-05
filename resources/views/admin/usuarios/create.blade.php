@extends('admin.inicioadmin')

@section('titulo', 'Registrar Nuevo Usuario')
@section('contenido')
<div class="container mt-4">
    <h2 class="mb-4 fw-bold">Registrar Nuevo Usuario</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>¡Ups! Hubo algunos problemas:</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('usuarios.store') }}" method="POST">
        @csrf

        <div class="row">
            <div class="col-md-4 mb-3">
                <label for="nombre" class="form-label">Nombre *</label>
                <input
                    type="text" name="nombre" id="nombre" class="form-control"
                    value="{{ old('nombre') }}" required
                    pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+"
                    maxlength="60" autocomplete="given-name"
                    title="Solo letras y espacios"
                >
            </div>
            <div class="col-md-4 mb-3">
                <label for="apellido1" class="form-label">Primer Apellido *</label>
                <input
                    type="text" name="apellido1" id="apellido1" class="form-control"
                    value="{{ old('apellido1') }}" required
                    pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+"
                    maxlength="60" autocomplete="family-name"
                    title="Solo letras y espacios"
                >
            </div>
            <div class="col-md-4 mb-3">
                <label for="apellido2" class="form-label">Segundo Apellido</label>
                <input
                    type="text" name="apellido2" id="apellido2" class="form-control"
                    value="{{ old('apellido2') }}"
                    pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+"
                    maxlength="60" autocomplete="additional-name"
                    title="Solo letras y espacios"
                >
            </div>
        </div>

        <div class="row">
    <div class="col-md-4 mb-3">
        <label for="usuario" class="form-label">Nombre de Usuario</label>
        <input
            type="text"
            name="usuario"
            id="usuario"
            class="form-control"
            value="{{ old('usuario') }}"
            readonly
        >
        @error('usuario') <span class="text-danger">{{ $message }}</span> @enderror
    </div>

    <div class="col-md-4 mb-3">
        <label for="correo" class="form-label">Correo Electrónico</label>
        <input
            type="email" name="correo" id="correo" class="form-control"
            value="{{ old('correo') }}"
            maxlength="60" autocomplete="email"
            pattern="^[^\s@]+@[^\s@]+\.com$"
            oninput="this.value=this.value.toLowerCase().trim()"
            title="Debe ser un correo válido y terminar en .com"
        >
    </div>
    <div class="col-md-4 mb-3">
        <label for="ci_nit" class="form-label">CI/NIT *</label>
        <input
            type="text" name="ci_nit" id="ci_nit" class="form-control"
            value="{{ old('ci_nit') }}" required
            maxlength="15" autocomplete="off"
        >
    </div>
</div>


        <div class="row">
            <div class="col-md-4 mb-3">
                <label for="telefono" class="form-label">Teléfono *</label>
                <input
                    type="text" name="telefono" id="telefono" class="form-control"
                    value="{{ old('telefono') }}" required
                    inputmode="numeric" pattern="\d{7,15}" maxlength="15" autocomplete="tel"
                    title="Solo números (7 a 15 dígitos)"
                >
            </div>
            <div class="col-md-8 mb-3">
                <label for="direccion" class="form-label">Dirección</label>
                <input
                    type="text" name="direccion" id="direccion" class="form-control"
                    value="{{ old('direccion') }}"
                    maxlength="150" autocomplete="street-address"
                >
            </div>
        </div>

        <div class="row">
            <div class="col-md-4 mb-3">
                <label for="rol" class="form-label">Rol *</label>
                <select name="rol" id="rol" class="form-select" required>
                    <option value="" disabled {{ old('rol') ? '' : 'selected' }}>Seleccione un rol</option>
                    <option value="admin" {{ old('rol') == 'admin' ? 'selected' : '' }}>Administrador</option>
                    <option value="avicultor" {{ old('rol') == 'avicultor' ? 'selected' : '' }}>Avicultor</option>
                    <option value="vendedor" {{ old('rol') == 'vendedor' ? 'selected' : '' }}>Vendedor</option>
                </select>
            </div>
        </div>

        <div class="mt-3">
            <a href="{{ route('admin.usuarios') }}" class="btn btn-secondary">Cancelar</a>
            <button type="submit" class="btn btn-oliva px-5">Registrar Usuario</button>
        </div>

        {{-- Script para sugerir usuario automáticamente --}}
       <script>
(function () {
  const nombre   = document.getElementById('nombre');
  const apellido = document.getElementById('apellido1');
  const usuario  = document.getElementById('usuario');

  if (!nombre || !apellido || !usuario) return;

  function actualizar() {
    const n = (nombre.value || '').trim();
    const a = (apellido.value || '').trim();

    if (!n || !a) { usuario.value = ''; return; }

    // Coincide con tu backend: primera letra del nombre (MAYÚS) + Apellido1 (Capitalizado)
    // Ej: Juan Perez -> J + Perez = "JPerez"
    const sugerido = n.charAt(0).toUpperCase() + a.charAt(0).toUpperCase() + a.slice(1).toLowerCase();
    usuario.value = sugerido;
  }

  nombre.addEventListener('input', actualizar);
  apellido.addEventListener('input', actualizar);
  document.addEventListener('DOMContentLoaded', actualizar);
})();
</script>

    </form>
</div>
@endsection
