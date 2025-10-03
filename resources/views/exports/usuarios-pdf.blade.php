<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de Usuarios</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        h2 { text-align: center; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ccc; padding: 6px; text-align: left; }
        .muted { color: #666; font-size: 11px; }
    </style>
</head>
<body>
    <h2>Reporte de Usuarios</h2>
    <p class="muted">
        Generado: {{ now()->format('Y-m-d H:i') }} |
        Filtros:
        Rol={{ request('rol', '—') }},
        Estado={{ request()->has('estado') ? (request('estado') ? 'Activo' : 'Inactivo') : '—' }}
    </p>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre completo</th>
                <th>Usuario</th>
                <th>Correo</th>
                <th>Rol</th>
                <th>CI/NIT</th>
                <th>Teléfono</th>
                <th>Dirección</th>
                <th>Estado</th>
                <th>Creado</th>
            </tr>
        </thead>
        <tbody>
        @foreach ($usuarios as $u)
            @php
                $nombre = trim($u->nombre.' '.$u->apellido1.' '.($u->apellido2 ?? ''));
            @endphp
            <tr>
                <td>{{ $u->id }}</td>
                <td>{{ $nombre }}</td>
                <td>{{ $u->usuario }}</td>
                <td>{{ $u->correo }}</td>
                <td>{{ $u->rol }}</td>
                <td>{{ $u->ci_nit }}</td>
                <td>{{ $u->telefono }}</td>
                <td>{{ $u->direccion }}</td>
                <td>{{ $u->estado ? 'Activo' : 'Inactivo' }}</td>
                <td>{{ optional($u->created_at)->format('Y-m-d H:i') }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</body>
</html>
