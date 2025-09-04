@extends('layouts.app')

@section('title', 'Clientes Morosos')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">
        <i class="fas fa-exclamation-triangle text-danger me-2"></i>
        Clientes Morosos
    </h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('ventas.index') }}">Gestión de Ventas</a></li>
        <li class="breadcrumb-item active">Clientes Morosos</li>
    </ol>

    <!-- Estadísticas Generales -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card bg-danger text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="small text-white-50">Total Clientes Morosos</div>
                            <div class="h5 mb-0">{{ $clientesMorosos->count() }}</div>
                        </div>
                        <div class="fa-2x">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-warning text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="small text-white-50">Deuda Total</div>
                            <div class="h5 mb-0">S/. {{ number_format($clientesMorosos->sum('saldo_total'), 2) }}</div>
                        </div>
                        <div class="fa-2x">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-info text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="small text-white-50">Clientes Naturales</div>
                            <div class="h5 mb-0">{{ $clientesMorosos->where('tipo_cliente', 'Natural')->count() }}</div>
                        </div>
                        <div class="fa-2x">
                            <i class="fas fa-user"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-secondary text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="small text-white-50">Establecimientos</div>
                            <div class="h5 mb-0">{{ $clientesMorosos->where('tipo_cliente', 'Establecimiento')->count() }}</div>
                        </div>
                        <div class="fa-2x">
                            <i class="fas fa-building"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de Clientes Morosos -->
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-table me-1"></i>
            Lista de Clientes con Saldo Pendiente
        </div>
        <div class="card-body">
            @if($clientesMorosos->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="dataTable">
                        <thead class="table-dark">
                            <tr>
                                <th>Tipo</th>
                                <th>Cliente</th>
                                <th>Teléfono</th>
                                <th>Ventas Pendientes</th>
                                <th>Saldo Total</th>
                                <th>Días Promedio Atraso</th>
                                <th>Riesgo</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($clientesMorosos as $cliente)
                                @php
                                    $diasAtraso = $cliente->dias_atraso_promedio ?? 0;
                                    $riesgo = 'Bajo';
                                    $colorRiesgo = 'success';
                                    
                                    if ($diasAtraso > 60) {
                                        $riesgo = 'Alto';
                                        $colorRiesgo = 'danger';
                                    } elseif ($diasAtraso > 30) {
                                        $riesgo = 'Medio';
                                        $colorRiesgo = 'warning';
                                    }
                                @endphp
                                <tr>
                                    <td>
                                        @if($cliente->tipo_cliente == 'Natural')
                                            <span class="badge bg-primary">
                                                <i class="fas fa-user me-1"></i>Natural
                                            </span>
                                        @else
                                            <span class="badge bg-info">
                                                <i class="fas fa-building me-1"></i>Establecimiento
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <strong>{{ $cliente->nombre_cliente }}</strong>
                                    </td>
                                    <td>
                                        @if($cliente->telefono)
                                            <a href="tel:{{ $cliente->telefono }}" class="text-decoration-none">
                                                <i class="fas fa-phone text-success me-1"></i>
                                                {{ $cliente->telefono }}
                                            </a>
                                        @else
                                            <span class="text-muted">Sin teléfono</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-warning">{{ $cliente->ventas_pendientes }}</span>
                                    </td>
                                    <td>
                                        <strong class="text-danger">
                                            S/. {{ number_format($cliente->saldo_total, 2) }}
                                        </strong>
                                    </td>
                                    <td>
                                        @if($diasAtraso > 0)
                                            <span class="text-{{ $colorRiesgo }}">
                                                {{ $diasAtraso }} días
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $colorRiesgo }}">{{ $riesgo }}</span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            @if($cliente->telefono)
                                                <a href="https://wa.me/51{{ $cliente->telefono }}?text=Hola {{ $cliente->nombre_cliente }}, te contactamos por el saldo pendiente de S/. {{ number_format($cliente->saldo_total, 2) }}" 
                                                   target="_blank" 
                                                   class="btn btn-success btn-sm" 
                                                   title="Contactar por WhatsApp">
                                                    <i class="fab fa-whatsapp"></i>
                                                </a>
                                            @endif
                                            <a href="{{ route('ventas.index', ['cliente_id' => $cliente->id_cliente, 'estado_pago' => 'pendiente']) }}" 
                                               class="btn btn-primary btn-sm" 
                                               title="Ver ventas pendientes">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('ventas.create', ['cliente_id' => $cliente->id_cliente]) }}" 
                                               class="btn btn-warning btn-sm" 
                                               title="Registrar pago">
                                                <i class="fas fa-money-bill"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="alert alert-success" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    <strong>¡Excelente!</strong> No hay clientes con saldo pendiente en este momento.
                </div>
            @endif
        </div>
    </div>

    <!-- Información adicional -->
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <i class="fas fa-info-circle me-1"></i>
                    Información sobre Clientes Morosos
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li><i class="fas fa-circle text-success me-2"></i><strong>Riesgo Bajo:</strong> Menos de 30 días de atraso</li>
                        <li><i class="fas fa-circle text-warning me-2"></i><strong>Riesgo Medio:</strong> Entre 30 y 60 días de atraso</li>
                        <li><i class="fas fa-circle text-danger me-2"></i><strong>Riesgo Alto:</strong> Más de 60 días de atraso</li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <i class="fas fa-lightbulb me-1"></i>
                    Acciones Recomendadas
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li><i class="fab fa-whatsapp text-success me-2"></i>Contactar por WhatsApp para recordatorio amigable</li>
                        <li><i class="fas fa-eye text-primary me-2"></i>Revisar historial de ventas del cliente</li>
                        <li><i class="fas fa-money-bill text-warning me-2"></i>Registrar pagos parciales o totales</li>
                        <li><i class="fas fa-handshake text-info me-2"></i>Negociar planes de pago flexibles</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    $('#dataTable').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json"
        },
        "order": [[ 4, "desc" ]], // Ordenar por saldo total descendente
        "pageLength": 25,
        "responsive": true
    });
});
</script>
@endpush
@endsection
