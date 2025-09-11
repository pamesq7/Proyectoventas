<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de Productos</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            margin: 0;
            padding: 15px;
        }
        .header {
            text-align: center;
            margin-bottom: 25px;
            border-bottom: 2px solid #6f42c1;
            padding-bottom: 15px;
        }
        .header h1 {
            color: #6f42c1;
            margin: 0;
            font-size: 22px;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .stats-line {
            margin-bottom: 20px;
            font-size: 12px;
            line-height: 1.5;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #dee2e6;
            padding: 6px;
            text-align: left;
        }
        th {
            background-color: #6f42c1;
            color: white;
            font-weight: bold;
            font-size: 10px;
        }
        tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        .badge {
            padding: 2px 6px;
            border-radius: 3px;
            color: white;
            font-size: 9px;
            font-weight: bold;
        }
        .badge-success {
            background-color: #28a745;
        }
        .badge-danger {
            background-color: #dc3545;
        }
        .badge-warning {
            background-color: #ffc107;
            color: #212529;
        }
        .badge-info {
            background-color: #17a2b8;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 9px;
            color: #666;
            border-top: 1px solid #dee2e6;
            padding-top: 10px;
        }
        .no-data {
            text-align: center;
            padding: 30px;
            color: #666;
            font-style: italic;
        }
        .precio {
            font-weight: bold;
            color: #28a745;
        }
        .stock-bajo {
            color: #dc3545;
            font-weight: bold;
        }
        .stock-normal {
            color: #28a745;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Lista de Productos</h1>
        <p>Sistema de Gestión de Ventas Deportivas</p>
        <p>Generado el: {{ $fecha_generacion }}</p>
    </div>

    <div class="stats-line">
        <strong>Total de Productos:</strong> {{ $estadisticas['total_productos'] }}<br>
        <strong>Productos Activos:</strong> {{ $estadisticas['productos_activos'] }}<br>
        <strong>Productos Inactivos:</strong> {{ $estadisticas['productos_inactivos'] }}<br>
        <strong>Con Stock:</strong> {{ $estadisticas['con_stock'] }}
    </div>

    @if($productos->count() > 0)
    <table>
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 10%;">SKU</th>
                <th style="width: 25%;">Nombre</th>
                <th style="width: 15%;">Categoría</th>
                <th style="width: 12%;">Precio</th>
                <th style="width: 8%;">Stock</th>
                <th style="width: 10%;">Estado</th>
                <th style="width: 15%;">Fecha Registro</th>
            </tr>
        </thead>
        <tbody>
            @foreach($productos as $index => $producto)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $producto->SKU ?? 'N/A' }}</td>
                <td>
                    <strong>{{ $producto->nombre }}</strong>
                    @if($producto->descripcion)
                        <br><small style="color: #666; font-size: 8px;">{{ Str::limit($producto->descripcion, 40) }}</small>
                    @endif
                </td>
                <td>{{ $producto->categoria->nombre ?? 'Sin categoría' }}</td>
                <td class="precio">Bs. {{ number_format($producto->precioVenta ?? 0, 2) }}</td>
                <td class="{{ $producto->cantidad <= 5 ? 'stock-bajo' : 'stock-normal' }}">
                    {{ $producto->cantidad ?? 0 }}
                </td>
                <td>
                    @if($producto->estado)
                        <span class="badge badge-success">Activo</span>
                    @else
                        <span class="badge badge-danger">Inactivo</span>
                    @endif
                </td>
                <td>{{ $producto->created_at ? $producto->created_at->format('d/m/Y') : 'N/A' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <div class="no-data">
        <p>No se encontraron productos.</p>
    </div>
    @endif

    <div class="footer">
        <p>Documento generado automáticamente por el Sistema de Gestión de Ventas Deportivas</p>
        <p>Página 1 de 1 | Total de registros: {{ $productos->count() }}</p>
    </div>
</body>
</html>
