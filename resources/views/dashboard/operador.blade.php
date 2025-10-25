@extends('layouts.app')

@section('title', 'Panel del Operador')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Panel del Operador</h1>
    </div>

    <!-- Estadísticas -->
    <div class="row">
        <!-- Pedidos Pendientes -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Pedidos Pendientes</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ App\Models\Venta::where('estado', 'pendiente')->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pedidos en Proceso -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                En Proceso</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ App\Models\Venta::where('estado', 'en proceso')->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-tasks fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pedidos Completados -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Completados</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ App\Models\Venta::where('estado', 'completado')->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Acciones Rápidas -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Acciones Rápidas</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <a href="{{ route('pedidos.index') }}" class="btn btn-primary btn-lg w-100">
                                <i class="fas fa-clipboard-list fa-2x mb-2"></i><br>
                                Gestionar Pedidos
                            </a>
                        </div>
                        <div class="col-md-4 mb-3">
                            <a href="{{ route('pedidos.catalogo') }}" class="btn btn-info btn-lg w-100">
                                <i class="fas fa-shopping-cart fa-2x mb-2"></i><br>
                                Ver Catálogo
                            </a>
                        </div>
                        <div class="col-md-4 mb-3">
                            <a href="{{ route('ventas.index') }}" class="btn btn-success btn-lg w-100">
                                <i class="fas fa-chart-bar fa-2x mb-2"></i><br>
                                Ver Ventas
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pedidos Recientes -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Pedidos Recientes</h6>
                    <a href="{{ route('pedidos.index') }}" class="btn btn-sm btn-primary">Ver Todos</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Cliente</th>
                                    <th>Total</th>
                                    <th>Estado</th>
                                    <th>Fecha</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>

                                @php
                                $pedidosRecientes = App\Models\Venta::latest()->take(5)->get();
                                @endphp

                                @forelse ($pedidosRecientes as $pedido)
                                <tr>
                                    <td>#{{ $pedido->idVenta }}</td>

                                    <td>
                                        {{ $pedido->cliente_id ? 'Cliente ID: ' . $pedido->cliente_id : 'N/A' }}
                                    </td>
                                    <td>${{ number_format($pedido->total, 2) }}</td>

                                    <td>
                                        @php
                                        $estadoClases = [
                                        'pendiente' => 'warning',
                                        'en proceso' => 'info',
                                        'completado' => 'success',
                                        'cancelado' => 'danger'
                                        ][$pedido->estado] ?? 'secondary';
                                        @endphp
                                        <span class="badge badge-{{ $estadoClases }}">
                                            {{ ucfirst($pedido->estado) }}
                                        </span>
                                    </td>
                                    <td>{{ $pedido->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <a href="{{ route('pedidos.show', $pedido->idVenta) }}"
                                            class="btn btn-info btn-sm"
                                            title="Ver detalles">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center">No hay pedidos registrados.</td>
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
@endsection