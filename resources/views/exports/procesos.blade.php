<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <style>
    body{ font-family: DejaVu Sans, sans-serif; font-size:12px; }
    h3{ margin:0 0 6px }
    table{ width:100%; border-collapse:collapse; margin-top:8px }
    th,td{ border:1px solid #ccc; padding:6px; text-align:left }
    th{ background:#f2f2f2 }
    .muted{ color:#666 }
  </style>
</head>
<body>
  <h3>Reporte de Proceso de Incubación</h3>
  <div class="muted">
    Generado: {{ $fecha }} — Por: {{ $generadoPor }}<br>
    Gestión: {{ $gestion ?: '—' }} —
    Usuario: {{ $usuario?->apellidos }}, {{ $usuario?->nombre }} —
    Incubadora: {{ $incubadora?->codigo ?? '—' }}
  </div>

  @if($proceso)
  <table>
    <thead>
      <tr>
        <th>Incubadora</th>
        <th>Fecha inicio</th>
        <th>Huevos inicio</th>
        <th>Huevos eclosionados</th>
        <th>Errores motor</th>
        <th>Errores lámpara</th>
        <th>Errores sensor</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>{{ $proceso->incubadora->codigo ?? '' }}</td>
        <td>{{ \Illuminate\Support\Carbon::parse($proceso->fecha_inicio)->format('d/m/Y') }}</td>
        <td>{{ $proceso->huevos_inicio }}</td>
        <td>{{ $proceso->huevos_eclosionados }}</td>
        <td>{{ $proceso->errores_motor }}</td>
        <td>{{ $proceso->errores_lampara }}</td>
        <td>{{ $proceso->errores_sensor }}</td>
      </tr>
    </tbody>
  </table>
  @else
    <p>No se encontraron datos con los filtros seleccionados.</p>
  @endif
</body>
</html>
