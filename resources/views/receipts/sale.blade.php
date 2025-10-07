<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Recibo #{{ $venta->id }}</title>
    <link rel="stylesheet" href="{{ asset('css/sale.css') }}">
</head>
<body class="body1">
    {{-- Cabecera con logo --}}
    <div class="header1">
        <img src="{{ public_path('images/logo (2).png') }}" alt="Logo">
        <h2>Recibo de Venta</h2>
    </div>

    {{-- Datos de la empresa --}}
    <div class="company-info">
        <p><strong>{{ $empresa['nombre'] }}</strong></p>
        <p>{{ $empresa['direccion'] }}</p>
        <p>Tel: {{ $empresa['telefono'] }} | NIT: {{ $empresa['nit'] }}</p>
    </div>

    {{-- Datos del recibo --}}
    <div class="info1">
        <p><strong>Recibo Nº:</strong> {{ $venta->id }}</p>
        <p><strong>Fecha emisión:</strong> {{ $fecha_emision }}</p>
        <p><strong>Cliente:</strong> {{ $venta->avicultor->nombre }} {{ $venta->avicultor->apellido1 }}</p>
    </div>

    {{-- Tabla de detalle --}}
    <table>
        <thead>
            <tr>
                <th>Incubadora</th>
                <th>Cantidad</th>
                <th>Precio (Bs)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($venta->detalleVentas as $det)
            <tr>
                <td>{{ $det->incubadora->codigo }}</td>
                <td>{{ $det->cantidad }}</td>
                <td>{{ number_format($det->precio_unitario, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2" align="right">Total:</td>
                <td>{{ number_format($venta->total_bs, 2) }} Bs</td>
            </tr>
        </tfoot>
    </table>

    {{-- Pie de página --}}
    <div class="footer1">
        <p>Gracias por su compra</p>
        <p>Este recibo no es válido como factura fiscal</p>
    </div>
</body>
</html>
