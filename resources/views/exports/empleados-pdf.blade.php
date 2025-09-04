<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Lista de Empleados</title>
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
            border-bottom: 2px solid #007bff;
            padding-bottom: 15px;
        }
        .header h1 {
            color: #007bff;
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
            margin-bottom: 10px;
        }
        .stats-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 20px;
        }
        .stat-card {
            background-color: #e9ecef;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
            min-width: 120px;
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
            background-color: #007bff;
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
        .badge-warning {
            background-color: #ffc107;
            color: #212529;
        }
        .badge-info {
            background-color: #17a2b8;
        }
        .badge-secondary {
            background-color: #6c757d;
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
        <h1>Lista de Empleados</h1>
        <p>Sistema de Gestión de Ventas Deportivas</p>
        <p>Generado el: {{ $fecha_generacion }}</p>
    </div>

    <div class="info-section">
        <div class="info-row">
            <strong>Total de Empleados:</strong> {{ $estadisticas['total'] }}
        </div>
        <div class="info-row">
            <strong>Empleados Activos:</strong> {{ $estadisticas['activos'] }}
        </div>
        <div class="info-row">
            <strong>Empleados Inactivos:</strong> {{ $estadisticas['inactivos'] }}
        </div>
        <div class="info-row">
            <strong>Porcentaje Activos:</strong> {{ $estadisticas['total'] > 0 ? round(($estadisticas['activos'] / $estadisticas['total']) * 100, 1) : 0 }}%
        </div>
    </div>

    @if(count($estadisticas['por_rol']) > 0)
    <div class="stats-grid">
        <h3 style="width: 100%; margin-bottom: 10px;">📊 Distribución por Rol:</h3>
        @foreach($estadisticas['por_rol'] as $rol => $cantidad)
        <div class="stat-card">
            <strong>{{ ucfirst($rol) }}</strong><br>
            <span style="font-size: 16px; color: #007bff;">{{ $cantidad }}</span>
        </div>
        @endforeach
    </div>
    @endif

    @if(count($filtros_aplicados) > 0)
    <div class="filters">
        <h3>🔍 Filtros Aplicados:</h3>
        @foreach($filtros_aplicados as $filtro)
            <span class="filter-item">{{ $filtro }}</span>
        @endforeach
    </div>
    @endif

    @if($empleados->count() > 0)
    <table>
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 25%;">Empleado</th>
                <th style="width: 15%;">Cargo</th>
                <th style="width: 12%;">Rol</th>
                <th style="width: 20%;">Contacto</th>
                <th style="width: 8%;">Estado</th>
                <th style="width: 15%;">Fecha Registro</th>
            </tr>
        </thead>
        <tbody>
            @foreach($empleados as $index => $empleado)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>
                    <strong>{{ $empleado->user->name ?? 'N/A' }}</strong>
                    @if($empleado->user && $empleado->user->primerApellido)
                        {{ $empleado->user->primerApellido }}
                    @endif
                    @if($empleado->user && $empleado->user->segundApellido)
                        {{ $empleado->user->segundApellido }}
                    @endif
                    @if($empleado->user && $empleado->user->ci)
                        <br><small>CI: {{ $empleado->user->ci }}</small>
                    @endif
                </td>
                <td>{{ $empleado->cargo }}</td>
                <td>
                    @php
                        $rolColors = [
                            'administrador' => 'badge-danger',
                            'vendedor' => 'badge-success',
                            'diseñador' => 'badge-warning',
                            'operador' => 'badge-info',
                            'cliente' => 'badge-secondary'
                        ];
                        $colorClass = $rolColors[$empleado->rol] ?? 'badge-secondary';
                    @endphp
                    <span class="badge {{ $colorClass }}">{{ ucfirst($empleado->rol) }}</span>
                </td>
                <td>
                    @if($empleado->user && $empleado->user->email)
                        <div style="font-size: 10px;">{{ $empleado->user->email }}</div>
                    @endif
                    @if($empleado->user && $empleado->user->telefono)
                        <div style="font-size: 10px;">📞 {{ $empleado->user->telefono }}</div>
                    @endif
                    @if(!$empleado->user || (!$empleado->user->email && !$empleado->user->telefono))
                        <span style="color: #999;">N/A</span>
                    @endif
                </td>
                <td>
                    @if($empleado->estado)
                        <span class="badge badge-success">Activo</span>
                    @else
                        <span class="badge badge-danger">Inactivo</span>
                    @endif
                </td>
                <td>{{ $empleado->created_at ? $empleado->created_at->format('d/m/Y') : 'N/A' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <div class="no-data">
        <p>No se encontraron empleados con los filtros aplicados.</p>
    </div>
    @endif

    <div class="footer">
        <p>Documento generado automáticamente por el Sistema de Gestión de Ventas Deportivas</p>
        <p>Página 1 de 1 | Total de registros: {{ $estadisticas['total'] }}</p>
    </div>
</body>
</html>
