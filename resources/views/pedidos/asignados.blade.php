@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3 class="card-title mb-0">Pedidos Asignados</h3>
                </div>

                <div class="card-body">
                    <!-- Filtros -->
                    <form method="GET" action="{{ route('pedidos.asignados') }}" class="mb-3">
                        <div class="row">
                            <div class="col-md-3">
                                <select name="estado" class="form-control">
                                    <option value="">Todos los estados</option>
                                    <option value="pendiente" {{ request('estado') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                                    <option value="en_proceso" {{ request('estado') == 'en_proceso' ? 'selected' : '' }}>En Proceso</option>
                                    <option value="completado" {{ request('estado') == 'completado' ? 'selected' : '' }}>Completado</option>
                                    <option value="cancelado" {{ request('estado') == 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <div class="input-group">
                                    <input type="text" name="buscar" class="form-control" placeholder="Buscar por cliente o ID..." value="{{ request('buscar') }}">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- Tabla de pedidos -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID Pedido</th>
                                    <th>Cliente</th>
                                    <th>Fecha</th>
                                    <th>Total</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pedidos as $pedido)
                                    <tr>
                                        <td>#{{ str_pad($pedido->idVenta, 6, '0', STR_PAD_LEFT) }}</td>
                                        <td>
                                            @if($pedido->clienteNatural)
                                                {{ $pedido->clienteNatural->nombreCompleto() }}
                                            @elseif($pedido->clienteEstablecimiento)
                                                {{ $pedido->clienteEstablecimiento->nombreEstablecimiento }}
                                            @else
                                                Cliente no especificado
                                            @endif
                                        </td>
                                        <td>{{ $pedido->fechaEntrega->format('d/m/Y H:i') }}</td>
                                        <td>S/ {{ number_format($pedido->total, 2) }}</td>
                                        <td>
                                            @php
                                                $badgeClass = [
                                                    'pendiente' => 'warning',
                                                    'en_proceso' => 'info',
                                                    'completado' => 'success',
                                                    'cancelado' => 'danger'
                                                ][$pedido->estado] ?? 'secondary';
                                            @endphp
                                            <span class="badge bg-{{ $badgeClass }}">
                                                {{ ucfirst(str_replace('_', ' ', $pedido->estado)) }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('pedidos.show', $pedido->idVenta) }}" 
                                                   class="btn btn-info btn-sm" 
                                                   title="Ver detalles">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @if($pedido->estado != 'completado' && $pedido->estado != 'cancelado')
                                                    <a href="{{ route('pedidos.edit', $pedido->idVenta) }}" 
                                                       class="btn btn-warning btn-sm" 
                                                       title="Editar">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">No tienes pedidos asignados.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginación -->
                    @if($pedidos->hasPages())
                        <div class="d-flex justify-content-center mt-3">
                            {{ $pedidos->appends(request()->query())->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Estilos adicionales -->
<style>
    .table th {
        background-color: #f8f9fa;
    }
    .card-header {
        border-bottom: 1px solid rgba(0, 0, 0, 0.125);
    }
    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
        line-height: 1.5;
        border-radius: 0.2rem;
    }
    .badge {
        font-size: 0.85em;
        padding: 0.35em 0.65em;
    }
</style>
@endsection
