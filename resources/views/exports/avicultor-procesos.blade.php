<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Reporte de Procesos (Avicultor)</title>
  <style>
    body{ font-family: DejaVu Sans, sans-serif; font-size:12px; }
    h3{ margin:0 0 6px }
    .muted{ color:#666; margin-bottom:8px }
    table{ width:100%; border-collapse:collapse; margin-top:8px }
    th,td{ border:1px solid #ccc; padding:6px; text-align:left }
    th{ background:#f2f2f2 }
  </style>
</head>
<body>
  <h3>Reporte de Procesos de Incubación — Avicultor</h3>
  <div class="muted">
    Generado: {{ $fecha }} — Por: {{ $generadoPor }}<br>
    Incubadora: {{ $incubadora->codigo }} — Gestión: {{ $gestion ?: '—' }} @if($mes) — Mes: {{ $mes }} @endif
  </div>

  <table>
    <thead>
      <tr>
        <th>Incubadora</th>
        <th>Nombre del Proceso</th>
        <th>Fecha Inicio</th>
        <th>Fecha Fin</th>
        <th>Huevos Eclosionados</th>
        <th>Error Motor</th>
        <th>Error Lámpara</th>
        <th>Error Sensor</th>
      </tr>
    </thead>
    <tbody>
      @forelse($rows as $r)
        <tr>
          <td>{{ $r['codigo_incubadora'] }}</td>
          <td>{{ $r['nombre'] }}</td>
          <td>{{ $r['fecha_inicio'] }}</td>
          <td>{{ $r['fecha_fin'] }}</td>
          <td>{{ $r['huevos_eclosionados'] }}</td>
          <td>{{ $r['errores_motor'] }}</td>
          <td>{{ $r['errores_lampara'] }}</td>
          <td>{{ $r['errores_sensor'] }}</td>
        </tr>
      @empty
        <tr><td colspan="8">Sin datos para los filtros seleccionados.</td></tr>
      @endforelse
    </tbody>
  </table>
</body>
</html>
