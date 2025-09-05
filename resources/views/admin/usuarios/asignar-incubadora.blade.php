@extends('admin.inicioadmin')

@section('titulo', 'Asignar Incubadora')
@section('contenido')
<div class="container mt-4">
    <h2 class="mb-4 fw-bold">Asignar incubadora a {{ $usuario->nombre }} {{ $usuario->apellido1 }}</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Revisa:</strong>
            <ul class="mb-0">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if($incubadorasDisponibles->isEmpty())
        <div class="alert alert-info">
            No hay incubadoras disponibles para asignar.
            {{-- Si tienes una ruta para crear incubadoras, puedes enlazarla aquí: --}}
            {{-- <a href="{{ route('admin.incubadoras') }}" class="alert-link">Crear nueva incubadora</a> --}}
        </div>
        <a href="{{ route('admin.usuarios') }}" class="btn btn-secondary">Volver</a>
    @else
        <form action="{{ route('usuarios.asignar-incubadora', $usuario->id) }}" method="POST">
            @csrf

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

            <div class="mt-3 d-flex gap-2">
                <a href="{{ route('admin.usuarios') }}" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-oliva">Asignar</button>
            </div>
        </form>
    @endif
</div>
@endsection
