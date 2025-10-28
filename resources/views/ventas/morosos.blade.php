@extends('layouts.app')

@section('title', 'Clientes Morosos')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">
                        <i class="fas fa-exclamation-triangle"></i> Clientes Morosos
                    </h3>
                    <div class="btn-group">
                        <a href="{{ route('ventas.index') }}" class="btn btn-info">
                            <i class="fas fa-arrow-left"></i> Volver a Ventas
                        </a>
                    </div>
                </div>

                <!-- Estadísticas -->
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <div class="info-box bg-danger">
                                <span class="info-box-icon"><i class="fas fa-dollar-sign"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Deuda Total</span>
                                    <span class="info-box-number">Bs. {{ number_format($clientesMorosos->sum('saldo_total'), 2) }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box bg-warning">
                                <span class="info-box-icon"><i class="fas fa-user"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Clientes Naturales</span>
                                    <span class="info-box-number">{{ $clientesMorosos->where('tipo_cliente', 'Natural')->count() }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box bg-success">
                                <span class="info-box-icon"><i class="fas fa-store"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Clientes Establecimiento</span>
                                    <span class="info-box-number">{{ $clientesMorosos->where('tipo_cliente', 'Establecimiento')->count() }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box bg-info">
                                <span class="info-box-icon"><i class="fas fa-exclamation-triangle"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Clientes Morosos</span>
                                    <span class="info-box-number">{{ $clientesMorosos->count() }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="thead-dark">
                                <tr>
                                    <th>N°</th>
                                    <th>Fecha</th>
                                    <th>Cliente</th>
                                    <th>Total</th>
                                    <th>Estado Pago</th>
                                    <th>Vendedor</th>
                                    <th>Fecha Creación</th>
                                    <th>Fecha Actualización</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $contador = 1; @endphp
                                @forelse($clientesMorosos as $cliente)
                                <tr>
                                    <td>{{ $contador++ }}</td>
                                    <td>{{ $cliente->ultima_venta_fecha ? \Carbon\Carbon::parse($cliente->ultima_venta_fecha)->format('d/m/Y') : 'N/A' }}</td>
                                    <td>
                                        <strong>{{ $cliente->nombre_cliente ?? 'N/A' }}</strong>
                                        <br><small class="text-muted">{{ $cliente->tipo_cliente ?? 'N/A' }}</small>
                                    </td>
                                    <td>
                                        <strong>Bs. {{ number_format($cliente->saldo_total ?? 0, 2) }}</strong>
                                    </td>
                                    <td>
                                        @if(($cliente->ventas_pendientes ?? 0) > 0)
                                        <span class="badge badge-danger">🔴 MOROSO</span>
                                        <br><small>{{ $cliente->ventas_pendientes ?? 0 }} venta(s)</small>
                                        @endif
                                    </td>
                                    <td>
                                        <strong>N/A</strong>
                                    </td>
                                    <td>{{ $cliente->ultima_venta_fecha ? \Carbon\Carbon::parse($cliente->ultima_venta_fecha)->format('d/m/Y H:i') : 'N/A' }}</td>
                                    <td>{{ $cliente->ultima_venta_fecha ? \Carbon\Carbon::parse($cliente->ultima_venta_fecha)->format('d/m/Y H:i') : 'N/A' }}</td>
                                    <td>
                                        <div class="btn-group btn-group-sm">

                                        

                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center py-4">
                                        <i class="fas fa-check-circle fa-2x text-success mb-2"></i><br>
                                        ¡Excelente! No hay clientes morosos
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Estilos para la tabla -->
<style>
    .table {
        color: #000000;
        font-size: 0.9rem;
    }

    .table thead th {
        background-color: #343a40;
        color: white;
        padding: 8px 12px;
        vertical-align: middle;
    }

    .table tbody td {
        padding: 6px 12px;
        vertical-align: middle;
    }

    .table-striped tbody tr:nth-of-type(odd) {
        background-color: rgba(0, 0, 0, 0.02);
    }

    .table-hover tbody tr:hover {
        background-color: rgba(0, 0, 0, 0.05);
    }

    /* Ajuste para los botones de acción */
    .btn-group-sm>.btn,
    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.8rem;
    }

    /* Ajuste para los badges de estado */
    .badge-success,
    .badge-warning,
    .badge-danger,
    .badge-secondary {
        color: #000000 !important;
        font-weight: 500;
    }

    .badge-success {
        background-color: #a3e4a3;
    }

    .badge-warning {
        background-color: #f9e79f;
    }

    .badge-danger {
        background-color: #f5b7b1;
    }

    .badge-secondary {
        background-color: #d5dbdb;
    }

    /* Ajuste para los textos pequeños */
    small {
        font-size: 0.8rem;
    }

    /* Estilos para las info-box */
    .info-box {
        box-shadow: 0 0 1px rgba(0, 0, 0, .125), 0 1px 3px rgba(0, 0, 0, .2);
        border-radius: 0.25rem;
        background: #fff;
        display: flex;
        margin-bottom: 1rem;
        min-height: 80px;
        padding: 0.5rem;
        position: relative;
    }

    .info-box .info-box-icon {
        border-radius: 0.25rem;
        align-items: center;
        display: flex;
        font-size: 1.875rem;
        justify-content: center;
        text-align: center;
        width: 70px;
    }

    .info-box .info-box-content {
        display: flex;
        flex-direction: column;
        justify-content: center;
        line-height: 1.8;
        flex: 1;
        padding: 0 10px;
    }

    .info-box .info-box-text {
        display: block;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        text-transform: uppercase;
        font-size: 0.875rem;
    }

    .info-box .info-box-number {
        display: block;
        margin-top: 0.25rem;
        font-weight: 700;
        font-size: 1.5rem;
    }

    .info-box.bg-danger {
        color: #fff;
    }

    .info-box.bg-warning {
        color: #1f2d3d;
    }

    .info-box.bg-success {
        color: #fff;
    }

    .info-box.bg-info {
        color: #fff;
    }
</style>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('.table').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json"
            },
            "order": [
                [4, "desc"]
            ], // Ordenar por saldo total descendente
            "pageLength": 25,
            "responsive": true,
            "dom": '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rtip',
            "pagingType": "simple_numbers"
        });
    });
</script>
@endpush