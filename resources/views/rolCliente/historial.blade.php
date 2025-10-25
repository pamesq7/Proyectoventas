@extends('layouts.app')

@section('title', 'Mis Pedidos - Historial')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Mis Pedidos</h1>
    
    <!-- Información de seguridad -->
    <div class="alert alert-success mb-4">
        <i class="fas fa-shield-alt me-2"></i>
        <strong>Modo seguro activado:</strong> Solo puedes ver tus propios pedidos.
        <small class="d-block mt-1">Usuario: {{ Auth::user()->name }} (ID: {{ Auth::user()->idUser }})</small>
    </div>

    <div class="row">
        <!-- Estadísticas -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Pedidos Totales
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $estadisticas->total_pedidos }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Pedidos Activos
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $estadisticas->pedidos_activos }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Completados
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $estadisticas->pedidos_completados }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Total Gastado
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">${{ number_format($estadisticas->total_gastado, 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-shopping-bag me-2"></i>
                        Mis Pedidos
                        <span class="badge bg-primary ms-2">{{ $pedidos->total() }}</span>
                    </h5>
                </div>
                <div class="card-body">
                    @if($pedidos->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>N° PEDIDO</th>
                                        <th>FECHA</th>
                                        <th>TOTAL</th>
                                        <th>ESTADO</th>
                                        <th>SALDO</th>
                                        <th>ACCIONES</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pedidos as $pedido)
                                    <tr>
                                        <td class="fw-bold">#{{ $pedido->idVenta }}</td>
                                        <td>{{ $pedido->created_at->format('d/m/Y H:i') }}</td>
                                        <td>${{ number_format($pedido->total, 2) }}</td>
                                        <td>
                                            @php
                                            $estados = [
                                                0 => ['texto' => 'Pendiente', 'color' => 'warning'],
                                                1 => ['texto' => 'En proceso', 'color' => 'info'],
                                                2 => ['texto' => 'Completado', 'color' => 'success'],
                                                3 => ['texto' => 'Entregado', 'color' => 'primary']
                                            ];
                                            $estado = $estados[$pedido->estadoPedido] ?? ['texto' => 'Desconocido', 'color' => 'secondary'];
                                            @endphp
                                            <span class="badge bg-{{ $estado['color'] }}">{{ $estado['texto'] }}</span>
                                        </td>
                                        <td>
                                            @if($pedido->saldo > 0)
                                                <span class="badge bg-danger">${{ number_format($pedido->saldo, 2) }}</span>
                                            @else
                                                <span class="badge bg-success">Pagado</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('rolCliente.detalle-pedido', $pedido->idVenta) }}" 
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye me-1"></i>Ver
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Paginación -->
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <div class="text-muted">
                                Mostrando {{ $pedidos->firstItem() }} a {{ $pedidos->lastItem() }} de {{ $pedidos->total() }} registros
                            </div>
                            <div>
                                {{ $pedidos->links() }}
                            </div>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-shopping-bag fa-4x text-muted mb-4"></i>
                            <h4 class="text-muted">No tienes pedidos registrados</h4>
                            <p class="text-muted mb-4">Realiza tu primer pedido en nuestra tienda</p>
                            <a href="{{ route('cliente.productos') }}" class="btn btn-primary">
                                <i class="fas fa-store me-2"></i>Ir a Productos
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection