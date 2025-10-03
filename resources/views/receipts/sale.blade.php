<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Recibo #{{ $venta->id }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #333; }
        .header {
            text-align: center;
            border-bottom: 2px solid #4CAF50;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header img {
            max-height: 60px;
            margin-bottom: 5px;
        }
        .header h2 {
            margin: 0;
            font-size: 20px;
            color: #4CAF50;
        }
        .company-info {
            text-align: center;
            font-size: 11px;
            margin-bottom: 20px;
        }
        .info {
            margin-bottom: 15px;
        }
        .info p {
            margin: 2px 0;
        }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td {
            border: 1px solid #ccc;
            padding: 6px;
            text-align: center;
        }
        th {
            background: #f5f5f5;
            font-weight: bold;
        }
        tfoot td {
            border-top: 2px solid #4CAF50;
            font-weight: bold;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 11px;
            color: #555;
        }
    </style>
</head>
<body>
    {{-- Cabecera con logo --}}
    <div class="header">
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
    <div class="info">
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
    <div class="footer">
        <p>Gracias por su compra</p>
        <p>Este recibo no es válido como factura fiscal</p>
    </div>
</body>
</html>
