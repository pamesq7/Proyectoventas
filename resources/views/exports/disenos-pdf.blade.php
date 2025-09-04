<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de Diseños</title>
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
    </style>
</head>
<body>
    <div class="header">
        <h1>Lista de Diseños</h1>
        <p>Sistema de Gestión de Ventas Deportivas</p>
        <p>Generado el: {{ $fecha_generacion }}</p>
    </div>

    <div class="stats-line">
        <strong>Total de Diseños:</strong> {{ $estadisticas['total_disenos'] }}<br>
        <strong>Diseños Activos:</strong> {{ $estadisticas['disenos_activos'] }}<br>
        <strong>Diseños Inactivos:</strong> {{ $estadisticas['disenos_inactivos'] }}<br>
        <strong>Con Empleado Asignado:</strong> {{ $estadisticas['con_empleado'] }}
    </div>

    @if($disenos->count() > 0)
    <table>
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 25%;">Nombre</th>
                <th style="width: 30%;">Descripción</th>
                <th style="width: 15%;">Empleado</th>
                <th style="width: 10%;">Estado</th>
                <th style="width: 15%;">Fecha Registro</th>
            </tr>
        </thead>
        <tbody>
            @foreach($disenos as $index => $diseno)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>
                    <strong>{{ $diseno->nombre }}</strong>
                </td>
                <td>{{ Str::limit($diseno->descripcion ?? 'Sin descripción', 60) }}</td>
                <td>
                    @if($diseno->empleado && $diseno->empleado->user)
                        {{ $diseno->empleado->user->name }}
                        @if($diseno->empleado->user->primerApellido)
                            {{ $diseno->empleado->user->primerApellido }}
                        @endif
                    @else
                        <span class="badge badge-info">Sin asignar</span>
                    @endif
                </td>
                <td>
                    @if($diseno->estado)
                        <span class="badge badge-success">Activo</span>
                    @else
                        <span class="badge badge-danger">Inactivo</span>
                    @endif
                </td>
                <td>{{ $diseno->created_at ? $diseno->created_at->format('d/m/Y') : 'N/A' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <div class="no-data">
        <p>No se encontraron diseños.</p>
    </div>
    @endif

    <div class="footer">
        <p>Documento generado automáticamente por el Sistema de Gestión de Ventas Deportivas</p>
        <p>Página 1 de 1 | Total de registros: {{ $disenos->count() }}</p>
    </div>
</body>
</html>
