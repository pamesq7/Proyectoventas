@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Panel del Vendedor</h4>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-4">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Ventas Hoy</h5>
                                    <p class="card-text display-4">{{ App\Models\Venta::whereDate('fechaEntrega', today())->where('idEmpleado', auth()->user()->empleado->idEmpleado)->count() }}</p>
                                    <a href="{{ route('ventas.index') }}" class="btn btn-light btn-sm">Ver más</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-4">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Ventas del Mes</h5>
                                    <p class="card-text display-4">{{ App\Models\Venta::whereMonth('fechaEntrega', now()->month)
                                        ->whereYear('fechaEntrega', now()->year)
                                        ->where('idEmpleado', auth()->user()->empleado->idEmpleado)
                                        ->count() }}</p>
                                    <a href="{{ route('ventas.index') }}" class="btn btn-light btn-sm">Ver más</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-4">
                            <div class="card bg-warning text-dark">
                                <div class="card-body">
                                    <h5 class="card-title">Total Recaudado</h5>
                                    <p class="card-text display-4">{{ number_format(App\Models\Venta::where('idEmpleado', auth()->user()->empleado->idEmpleado)->sum('total'), 2) }} Bs.</p>
                                    <a href="{{ route('reportes.index') }}" class="btn btn-dark btn-sm">Ver reporte</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Ventas Recientes</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Fecha</th>
                                                    <th>Cliente</th>
                                                    <th>Total</th>
                                                    <th>Estado</th>
                                                    <th>Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse (App\Models\Venta::where('idEmpleado', auth()->user()->empleado->idEmpleado)
                                                    ->latest()
                                                    ->take(5)
                                                    ->get() as $venta)
                                                    <tr>
                                                        <td>{{ $venta->idVenta }}</td>
                                                        <td>{{ $venta->fechaEntrega->format('d/m/Y H:i') }}</td>
                                                        <td>{{ $venta->cliente->nombre ?? 'Cliente ocasional' }}</td>
                                                        <td>{{ number_format($venta->total, 2) }} Bs.</td>
                                                        <td>
                                                            <span class="badge bg-{{ $venta->estado == 'completado' ? 'success' : ($venta->estado == 'pendiente' ? 'warning' : 'danger') }}">
                                                                {{ ucfirst($venta->estado) }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <a href="{{ route('ventas.show', $venta->idVenta) }}" class="btn btn-sm btn-primary">
                                                                <i class="fas fa-eye"></i> Ver
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="6" class="text-center">No hay ventas registradas</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="mt-3">
                                        <a href="{{ route('ventas.create') }}" class="btn btn-primary">
                                            <i class="fas fa-plus"></i> Nueva Venta
                                        </a>
                                        <a href="{{ route('ventas.index') }}" class="btn btn-outline-secondary">
                                            Ver todas las ventas
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
@endsection
