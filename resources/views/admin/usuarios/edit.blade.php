@extends('admin.inicioadmin')

@section('titulo', 'Editar Usuario')
@section('contenido')
<div class="container mt-4">
    <h2 class="mb-4 fw-bold">Editar Usuario</h2>

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

    <form action="{{ route('usuarios.update', $usuario->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row">
            <div class="col-md-4 mb-3">
                <label for="nombre" class="form-label">Nombre *</label>
                <input
                    type="text" name="nombre" id="nombre" class="form-control"
                    value="{{ old('nombre', $usuario->nombre) }}" required
                    pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+"
                    maxlength="60" autocomplete="given-name"
                    title="Solo letras y espacios"
                >
            </div>
            <div class="col-md-4 mb-3">
                <label for="apellido1" class="form-label">Primer Apellido *</label>
                <input
                    type="text" name="apellido1" id="apellido1" class="form-control"
                    value="{{ old('apellido1', $usuario->apellido1) }}" required
                    pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+"
                    maxlength="60" autocomplete="family-name"
                    title="Solo letras y espacios"
                >
            </div>
            <div class="col-md-4 mb-3">
                <label for="apellido2" class="form-label">Segundo Apellido</label>
                <input
                    type="text" name="apellido2" id="apellido2" class="form-control"
                    value="{{ old('apellido2', $usuario->apellido2) }}"
                    pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+"
                    maxlength="60" autocomplete="additional-name"
                    title="Solo letras y espacios"
                >
            </div>
        </div>

        <div class="row">
            <div class="col-md-4 mb-3">
                <label for="usuario" class="form-label">Usuario *</label>
                <input
                    type="text" name="usuario" id="usuario" class="form-control"
                    value="{{ old('usuario', $usuario->usuario) }}" required
                    readonly
                >
                {{-- Si quieres permitir editar, quita readonly. Ten en cuenta que tu update() lo vuelve a generar. --}}
            </div>
            <div class="col-md-4 mb-3">
                <label for="correo" class="form-label">Correo</label>
                <input
                    type="email" name="correo" id="correo" class="form-control"
                    value="{{ old('correo', $usuario->correo) }}"
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
                    value="{{ old('ci_nit', $usuario->ci_nit) }}" required
                    maxlength="15" autocomplete="off"
                >
                {{-- Si lo quieres solo numérico en frontend: pattern="\d{5,15}" title="Solo números (5 a 15 dígitos)" --}}
            </div>
        </div>

        <div class="row">
            <div class="col-md-4 mb-3">
                <label for="telefono" class="form-label">Teléfono *</label>
                <input
                    type="text" name="telefono" id="telefono" class="form-control"
                    value="{{ old('telefono', $usuario->telefono) }}" required
                    inputmode="numeric" pattern="\d{7,15}" maxlength="15" autocomplete="tel"
                    title="Solo números (7 a 15 dígitos)"
                >
            </div>
            <div class="col-md-8 mb-3">
                <label for="direccion" class="form-label">Dirección</label>
                <input
                    type="text" name="direccion" id="direccion" class="form-control"
                    value="{{ old('direccion', $usuario->direccion) }}"
                    maxlength="150" autocomplete="street-address"
                >
            </div>
        </div>

        <div class="row">
            <div class="col-md-4 mb-3">
                <label for="rol" class="form-label">Rol *</label>
                <select name="rol" id="rol" class="form-select" required>
                    <option value="admin"     {{ old('rol', $usuario->rol) === 'admin' ? 'selected' : '' }}>Administrador</option>
                    <option value="avicultor" {{ old('rol', $usuario->rol) === 'avicultor' ? 'selected' : '' }}>Avicultor</option>
                    <option value="vendedor"  {{ old('rol', $usuario->rol) === 'vendedor' ? 'selected' : '' }}>Vendedor</option>
                </select>
            </div>
            <!--<div class="col-md-4 mb-3">
                <label for="contraseña" class="form-label">Contraseña Nueva (opcional)</label>
                <input
                    type="password" name="contraseña" id="contraseña" class="form-control"
                    minlength="6" autocomplete="new-password"
                >
            </div>
            <div class="col-md-4 mb-3">
                <label for="contraseña_confirmation" class="form-label">Confirmar Contraseña</label>
                <input
                    type="password" name="contraseña_confirmation" id="contraseña_confirmation" class="form-control"
                    minlength="6" autocomplete="new-password"
                >
            </div>-->
        </div>

        <div class="mt-3">
            <a href="{{ route('admin.usuarios') }}" class="btn btn-secondary">Cancelar</a>
            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
        </div>
    </form>
    <div class="form-text">
        La contraseña no se edita en esta pantalla. Se gestiona automáticamente o desde “Reiniciar contraseña”.
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const nombreInput    = document.getElementById('nombre');
    const apellido1Input = document.getElementById('apellido1');
    const usuarioInput   = document.getElementById('usuario');
    const correoInput    = document.getElementById('correo');

    function actualizarUsuario() {
        const nombre    = (nombreInput.value || '').trim();
        const apellido1 = (apellido1Input.value || '').trim();
        if (nombre && apellido1 && usuarioInput && usuarioInput.hasAttribute('readonly')) {
            const usuarioGenerado = nombre.charAt(0).toUpperCase()
                                  + apellido1.charAt(0).toUpperCase()
                                  + apellido1.slice(1).toLowerCase();
            usuarioInput.value = usuarioGenerado;
        }
    }

    function normalizarCorreo() {
        if (correoInput) correoInput.value = correoInput.value.toLowerCase().trim();
    }

    nombreInput.addEventListener('input', actualizarUsuario);
    apellido1Input.addEventListener('input', actualizarUsuario);
    if (correoInput) correoInput.addEventListener('input', normalizarCorreo);
});
</script>

@endsection
