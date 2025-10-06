@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Panel del Operador</h4>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-4">
                            <div class="card bg-warning text-dark">
                                <div class="card-body">
                                    <h5 class="card-title">Pedidos Pendientes</h5>
                                    <p class="card-text display-4">{{ App\Models\Venta::where('estado', 'pendiente')->count() }}</p>
                                    <a href="{{ route('pedidos.index', ['estado' => 'pendiente']) }}" class="btn btn-dark btn-sm">Ver pendientes</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-4">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <h5 class="card-title">En Proceso</h5>
                                    <p class="card-text display-4">{{ App\Models\Venta::where('estado', 'procesando')->count() }}</p>
                                    <a href="{{ route('pedidos.index', ['estado' => 'procesando']) }}" class="btn btn-light btn-sm">Ver en proceso</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-4">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Completados Hoy</h5>
                                    <p class="card-text display-4">
                                        {{ App\Models\Venta::whereDate('fecha_entrega', today())
                                            ->where('estado', 'completado')
                                            ->count() }}
                                    </p>
                                    <a href="{{ route('pedidos.index', ['estado' => 'completado', 'fecha' => today()->format('Y-m-d')]) }}" class="btn btn-light btn-sm">Ver completados</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-4">
                            <div class="card bg-danger text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Atrasados</h5>
                                    <p class="card-text display-4">
                                        {{ App\Models\Venta::where('fecha_entrega', '<', today())
                                            ->whereIn('estado', ['pendiente', 'procesando'])
                                            ->count() }}
                                    </p>
                                    <a href="{{ route('pedidos.atrasados') }}" class="btn btn-light btn-sm">Ver atrasados</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Próximas Entregas</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Pedido #</th>
                                                    <th>Cliente</th>
                                                    <th>Fecha Entrega</th>
                                                    <th>Estado</th>
                                                    <th>Total</th>
                                                    <th>Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $proximasEntregas = App\Models\Venta::whereIn('estado', ['pendiente', 'procesando'])
                                                        ->whereDate('fecha_entrega', '>=', today())
                                                        ->orderBy('fecha_entrega')
                                                        ->take(5)
                                                        ->get();
                                                @endphp
                                                
                                                @forelse ($proximasEntregas as $pedido)
                                                    <tr class="{{ $pedido->fecha_entrega->isToday() ? 'table-warning' : '' }}">
                                                        <td>#{{ $pedido->idVenta }}</td>
                                                        <td>{{ $pedido->cliente->nombre ?? 'Cliente ocasional' }}</td>
                                                        <td>{{ $pedido->fecha_entrega->format('d/m/Y') }}</td>
                                                        <td>
                                                            <span class="badge bg-{{ $pedido->estado == 'pendiente' ? 'warning' : ($pedido->estado == 'procesando' ? 'info' : 'success') }}">
                                                                {{ ucfirst($pedido->estado) }}
                                                            </span>
                                                        </td>
                                                        <td>{{ number_format($pedido->total, 2) }} Bs.</td>
                                                        <td>
                                                            <a href="{{ route('pedidos.show', $pedido->idVenta) }}" class="btn btn-sm btn-primary">
                                                                <i class="fas fa-eye"></i> Ver
                                                            </a>
                                                            @if($pedido->estado == 'pendiente' || $pedido->estado == 'procesando')
                                                                <button class="btn btn-sm btn-success" onclick="actualizarEstado({{ $pedido->idVenta }})">
                                                                    <i class="fas fa-check"></i> Actualizar
                                                                </button>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="6" class="text-center">No hay entregas programadas</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="mt-3">
                                        <a href="{{ route('pedidos.index') }}" class="btn btn-outline-secondary">
                                            Ver todos los pedidos
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function actualizarEstado(idVenta) {
    if (confirm('¿Estás seguro de actualizar el estado de este pedido?')) {
        fetch(`/pedidos/${idVenta}/estado`, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                estado: '{{ $pedido->estado == 'pendiente' ? 'procesando' : 'completado' }}',
                _token: '{{ csrf_token() }}'
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error al actualizar el estado del pedido');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al actualizar el estado del pedido');
        });
    }
}
</script>
@endpush
@endsection
