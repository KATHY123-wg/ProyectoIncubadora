<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de Incubadoras</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        h2 { text-align: center; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ccc; padding: 6px; text-align: left; }
        .muted { color: #666; font-size: 11px; }
    </style>
</head>
<body>
    <h2>Reporte de Incubadoras</h2>

    <p class="muted">
        Generado: {{ now()->format('Y-m-d H:i') }} |
        Filtros: Usuario={{ request('usuario_id', '—') }},
        Estado={{ request()->has('estado') ? (request('estado') ? 'Activa' : 'Inactiva') : '—' }}
    </p>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Código</th>
                <th>Descripción</th>
                <th>Asignada a</th>
                <th>Estado</th>
                <th>Modificado por</th>
            </tr>
        </thead>
        <tbody>
        @foreach ($incubadoras as $i)
            @php
                $duenio = $i->usuario
                    ? trim($i->usuario->nombre.' '.$i->usuario->apellido1.' '.($i->usuario->apellido2 ?? ''))
                    : '—';
            @endphp
            <tr>
                <td>{{ $i->id }}</td>
                <td>{{ $i->codigo }}</td>
                <td>{{ $i->descripcion }}</td>
                <td>{{ $duenio }}</td>
                <td>{{ $i->estado ? 'Activa' : 'Inactiva' }}</td>
                <td>{{ $i->modificado_por ?? '—' }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</body>
</html>
