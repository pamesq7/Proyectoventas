<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Lista de Clientes Naturales</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #28a745;
            padding-bottom: 15px;
        }
        .header h1 {
            color: #28a745;
            margin: 0;
            font-size: 24px;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .info-section {
            background-color: #f8f9fa;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .info-row {
            display: inline-block;
            width: 48%;
            margin-right: 2%;
        }
        .filters {
            margin-bottom: 20px;
        }
        .filters h3 {
            color: #495057;
            margin-bottom: 10px;
        }
        .filter-item {
            background-color: #e9ecef;
            padding: 5px 10px;
            margin: 5px;
            border-radius: 3px;
            display: inline-block;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #dee2e6;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #28a745;
            color: white;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        .badge {
            padding: 3px 8px;
            border-radius: 3px;
            color: white;
            font-size: 10px;
            font-weight: bold;
        }
        .badge-success {
            background-color: #28a745;
        }
        .badge-danger {
            background-color: #dc3545;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #dee2e6;
            padding-top: 10px;
        }
        .no-data {
            text-align: center;
            padding: 40px;
            color: #666;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Lista de Clientes Naturales</h1>
        <p>Sistema de Gestión de Ventas Deportivas</p>
        <p>Generado el: {{ $fecha_generacion }}</p>
    </div>

    <div class="info-section">
        <div class="info-row">
            <strong>Total de Clientes:</strong> {{ $total_clientes }}
        </div>
        <div class="info-row">
            <strong>Clientes Activos:</strong> {{ $clientes->where('estado', 1)->count() }}
        </div>
        <div class="info-row">
            <strong>Clientes Inactivos:</strong> {{ $clientes->where('estado', 0)->count() }}
        </div>
        <div class="info-row">
            <strong>Con Email:</strong> {{ $clientes->filter(function($cliente) { return $cliente->user && $cliente->user->email; })->count() }}
        </div>
    </div>

    @if(count($filtros_aplicados) > 0)
    <div class="filters">
        <h3>🔍 Filtros Aplicados:</h3>
        @foreach($filtros_aplicados as $filtro)
            <span class="filter-item">{{ $filtro }}</span>
        @endforeach
    </div>
    @endif

    @if($clientes->count() > 0)
    <table>
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 25%;">Nombre Completo</th>
                <th style="width: 15%;">CI</th>
                <th style="width: 25%;">Email</th>
                <th style="width: 15%;">Teléfono</th>
                <th style="width: 10%;">Estado</th>
                <th style="width: 15%;">Fecha Registro</th>
            </tr>
        </thead>
        <tbody>
            @foreach($clientes as $index => $cliente)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>
                    <strong>{{ $cliente->user->name ?? 'N/A' }}</strong>
                    @if($cliente->user && $cliente->user->primerApellido)
                        {{ $cliente->user->primerApellido }}
                    @endif
                    @if($cliente->user && $cliente->user->segundApellido)
                        {{ $cliente->user->segundApellido }}
                    @endif
                </td>
                <td>{{ $cliente->user->ci ?? 'N/A' }}</td>
                <td>{{ $cliente->user->email ?? 'N/A' }}</td>
                <td>{{ $cliente->user->telefono ?? 'N/A' }}</td>
                <td>
                    @if($cliente->estado)
                        <span class="badge badge-success">Activo</span>
                    @else
                        <span class="badge badge-danger">Inactivo</span>
                    @endif
                </td>
                <td>{{ $cliente->created_at ? $cliente->created_at->format('d/m/Y') : 'N/A' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <div class="no-data">
        <p>No se encontraron clientes naturales con los filtros aplicados.</p>
    </div>
    @endif

    <div class="footer">
        <p>Documento generado automáticamente por el Sistema de Gestión de Ventas Deportivas</p>
        <p>Página 1 de 1 | Total de registros: {{ $total_clientes }}</p>
    </div>
</body>
</html>
