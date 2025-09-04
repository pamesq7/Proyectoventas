<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte Consolidado de Clientes</title>
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
        .stats-section {
            background-color: #f8f9fa;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            display: flex;
            justify-content: space-around;
        }
        .stat-card {
            text-align: center;
            padding: 10px;
            background-color: white;
            border-radius: 5px;
            min-width: 120px;
        }
        .stat-number {
            font-size: 24px;
            font-weight: bold;
            color: #6f42c1;
        }
        .section-title {
            background-color: #e9ecef;
            padding: 10px;
            margin: 20px 0 10px 0;
            border-left: 4px solid #6f42c1;
            font-weight: bold;
            font-size: 14px;
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
    </style>
</head>
<body>
    <div class="header">
        <h1>Reporte Consolidado de Clientes</h1>
        <p>Sistema de Gestión de Ventas Deportivas</p>
        <p>Generado el: {{ $fecha_generacion }}</p>
    </div>

    <div class="stats-section">
        <div class="stat-card">
            <div class="stat-number">{{ $estadisticas['total_naturales'] }}</div>
            <div>Clientes Naturales</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">{{ $estadisticas['total_establecimientos'] }}</div>
            <div>Establecimientos</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">{{ $estadisticas['total_general'] }}</div>
            <div>Total General</div>
        </div>
    </div>

    <!-- Sección Clientes Naturales -->
    @if($clientes_naturales->count() > 0)
    <div class="section-title">CLIENTES NATURALES ({{ $clientes_naturales->count() }})</div>
    <table>
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 30%;">Nombre Completo</th>
                <th style="width: 15%;">CI</th>
                <th style="width: 25%;">Email</th>
                <th style="width: 15%;">Teléfono</th>
                <th style="width: 10%;">Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($clientes_naturales as $index => $cliente)
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
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <div class="section-title">CLIENTES NATURALES</div>
    <div class="no-data">
        <p>No hay clientes naturales registrados.</p>
    </div>
    @endif

    <!-- Sección Clientes Establecimientos -->
    @if($clientes_establecimientos->count() > 0)
    <div class="section-title">CLIENTES ESTABLECIMIENTOS ({{ $clientes_establecimientos->count() }})</div>
    <table>
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 35%;">Razón Social</th>
                <th style="width: 15%;">NIT</th>
                <th style="width: 20%;">Email</th>
                <th style="width: 15%;">Teléfono</th>
                <th style="width: 10%;">Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($clientes_establecimientos as $index => $cliente)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>
                    <strong>{{ $cliente->razonSocial }}</strong>
                    @if($cliente->direccion)
                        <br><small style="color: #666; font-size: 8px;">{{ $cliente->direccion }}</small>
                    @endif
                </td>
                <td>{{ $cliente->nit ?? 'N/A' }}</td>
                <td>{{ $cliente->email ?? 'N/A' }}</td>
                <td>{{ $cliente->telefono ?? 'N/A' }}</td>
                <td>
                    @if($cliente->estado)
                        <span class="badge badge-success">Activo</span>
                    @else
                        <span class="badge badge-danger">Inactivo</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <div class="section-title">CLIENTES ESTABLECIMIENTOS</div>
    <div class="no-data">
        <p>No hay clientes establecimientos registrados.</p>
    </div>
    @endif

    <div class="footer">
        <p>Documento generado automáticamente por el Sistema de Gestión de Ventas Deportivas</p>
        <p>Reporte consolidado | Total de clientes: {{ $estadisticas['total_general'] }}</p>
    </div>
</body>
</html>
