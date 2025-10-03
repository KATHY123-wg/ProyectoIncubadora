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
  <h3>Reporte de Ventas</h3>
  <div class="muted">
    Generado: {{ $fecha }} — Por: {{ $generadoPor }}<br>
    Gestión: {{ $gestion ?: '—' }} —
    Vendedor: {{ $usuario?->nombre }} {{ $usuario?->apellido1 }}
  </div>

  {{-- Resumen por mes --}}
  @php
    $meses = [1=>'Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];
    $sumAnual = 0;
  @endphp
  <table>
    <thead>
      <tr><th>Mes</th><th>Total (Bs.)</th></tr>
    </thead>
    <tbody>
      @for($i=1;$i<=12;$i++)
        @php
          $key = str_pad($i,2,'0',STR_PAD_LEFT);
          $monto = (float)($porMes[$key] ?? 0);
          $sumAnual += $monto;
        @endphp
        <tr>
          <td>{{ $meses[$i] }}</td>
          <td>{{ number_format($monto,2,',','.') }}</td>
        </tr>
      @endfor
      <tr>
        <th>Total Anual</th>
        <th>{{ number_format($sumAnual,2,',','.') }}</th>
      </tr>
    </tbody>
  </table>

  {{-- Detalle de ventas --}}
  <table>
    <thead>
      <tr>
        <th>Fecha</th>
        <th>Cliente</th>
        <th># Ítems</th>
        <th>Total (Bs.)</th>
      </tr>
    </thead>
    <tbody>
      @forelse($ventas as $v)
        <tr>
          <td>{{ \Illuminate\Support\Carbon::parse($v->fecha_venta)->format('d/m/Y') }}</td>
          <td>{{ $v->avicultor?->nombre }} {{ $v->avicultor?->apellido1 }}</td>
          <td>{{ $v->detalleVentas?->count() ?? 0 }}</td>
          <td>{{ number_format($v->total_bs,2,',','.') }}</td>
        </tr>
      @empty
        <tr><td colspan="4">Sin ventas en la gestión seleccionada.</td></tr>
      @endforelse
    </tbody>
  </table>
</body>
</html>
