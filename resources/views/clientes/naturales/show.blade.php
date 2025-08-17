@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Detalles del Cliente Natural</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('clienteNatural.index') }}">Clientes Naturales</a></li>
        <li class="breadcrumb-item active">{{ $clienteNatural->user->nombre_completo }}</li>
    </ol>

    <div class="row">
        {{-- Información Principal del Cliente --}}
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-user me-2"></i>
                        {{ $clienteNatural->user->nombre_completo }}
                        @if($clienteNatural->estado == 1 && $clienteNatural->user->estado == 1)
                            <span class="badge bg-success ms-2">
                                <i class="fas fa-check me-1"></i>Activo
                            </span>
                        @else
                            <span class="badge bg-danger ms-2">
                                <i class="fas fa-times me-1"></i>Inactivo
                            </span>
                        @endif
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        {{-- Información Personal --}}
                        <div class="col-md-6">
                            <h6 class="border-bottom pb-2 mb-3">Información Personal</h6>
                            
                            <div class="mb-3">
                                <label class="form-label text-muted">Cédula de Identidad:</label>
                                <div>
                                    <code class="bg-light px-2 py-1 rounded fs-6">{{ $clienteNatural->user->ci }}</code>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-muted">Nombre Completo:</label>
                                <div class="fw-bold">{{ $clienteNatural->user->nombre_completo }}</div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-muted">Email:</label>
                                <div>
                                    <a href="mailto:{{ $clienteNatural->user->email }}" class="text-decoration-none">
                                        {{ $clienteNatural->user->email }}
                                    </a>
                                    @if($clienteNatural->user->email_verified_at)
                                        <span class="badge bg-success ms-2">
                                            <i class="fas fa-check-circle me-1"></i>Verificado
                                        </span>
                                    @endif
                                </div>
                            </div>

                            @if($clienteNatural->user->telefono)
                                <div class="mb-3">
                                    <label class="form-label text-muted">Teléfono:</label>
                                    <div>
                                        <a href="tel:{{ $clienteNatural->user->telefono }}" class="text-decoration-none">
                                            {{ $clienteNatural->user->telefono }}
                                        </a>
                                    </div>
                                </div>
                            @endif

                            @if($clienteNatural->nit)
                                <div class="mb-3">
                                    <label class="form-label text-muted">NIT:</label>
                                    <div>
                                        <code class="bg-light px-2 py-1 rounded">{{ $clienteNatural->nit }}</code>
                                    </div>
                                </div>
                            @endif
                        </div>
                        
                        {{-- Estadísticas de Ventas --}}
                        <div class="col-md-6">
                            <h6 class="border-bottom pb-2 mb-3">Estadísticas de Ventas</h6>
                            
                            <div class="mb-3">
                                <label class="form-label text-muted">Total de Ventas:</label>
                                <div>
                                    <span class="badge bg-info fs-6">{{ $estadisticas['total_ventas'] }} ventas</span>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-muted">Monto Total Comprado:</label>
                                <div class="fw-bold text-success fs-5">
                                    Bs. {{ number_format($estadisticas['monto_total'], 2) }}
                                </div>
                            </div>

                            @if($estadisticas['total_ventas'] > 0)
                                <div class="mb-3">
                                    <label class="form-label text-muted">Venta Promedio:</label>
                                    <div>
                                        Bs. {{ number_format($estadisticas['venta_promedio'], 2) }}
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label text-muted">Última Venta:</label>
                                    <div>
                                        {{ $estadisticas['ultima_venta'] ? \Carbon\Carbon::parse($estadisticas['ultima_venta'])->format('d/m/Y') : 'No disponible' }}
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label text-muted">Productos Diferentes Comprados:</label>
                                    <div>
                                        <span class="badge bg-secondary">{{ $estadisticas['productos_comprados'] }} productos</span>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Historial de Ventas --}}
            @if($ultimasVentas->count() > 0)
                <div class="card">
                    <div class="card-header">
                        <h6 class="card-title mb-0">
                            <i class="fas fa-shopping-cart me-1"></i>
                            Últimas Ventas ({{ $ultimasVentas->count() }})
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Productos</th>
                                        <th>Subtotal</th>
                                        <th>Total</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($ultimasVentas as $venta)
                                        <tr>
                                            <td>
                                                <div>{{ \Carbon\Carbon::parse($venta->fechaVenta)->format('d/m/Y') }}</div>
                                                <small class="text-muted">{{ \Carbon\Carbon::parse($venta->fechaVenta)->format('H:i') }}</small>
                                            </td>
                                            <td>
                                                <div>
                                                    @foreach($venta->detalleVentas->take(3) as $detalle)
                                                        <div class="small">
                                                            {{ $detalle->cantidad }}x {{ $detalle->producto->nombre }}
                                                        </div>
                                                    @endforeach
                                                    @if($venta->detalleVentas->count() > 3)
                                                        <small class="text-muted">
                                                            +{{ $venta->detalleVentas->count() - 3 }} más...
                                                        </small>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>Bs. {{ number_format($venta->subtotal, 2) }}</td>
                                            <td>
                                                <strong>Bs. {{ number_format($venta->total, 2) }}</strong>
                                            </td>
                                            <td>
                                                @switch($venta->estado)
                                                    @case('pendiente')
                                                        <span class="badge bg-warning">Pendiente</span>
                                                        @break
                                                    @case('confirmado')
                                                        <span class="badge bg-info">Confirmado</span>
                                                        @break
                                                    @case('entregado')
                                                        <span class="badge bg-success">Entregado</span>
                                                        @break
                                                    @case('cancelado')
                                                        <span class="badge bg-danger">Cancelado</span>
                                                        @break
                                                    @default
                                                        <span class="badge bg-secondary">{{ ucfirst($venta->estado) }}</span>
                                                @endswitch
                                            </td>
                                            <td>
                                                <a href="#" class="btn btn-sm btn-outline-info" title="Ver detalles">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @if($estadisticas['total_ventas'] > 5)
                            <div class="text-center mt-3">
                                <a href="#" class="btn btn-outline-primary">
                                    <i class="fas fa-list me-1"></i>
                                    Ver Todas las Ventas ({{ $estadisticas['total_ventas'] }})
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
        
        {{-- Panel de Información del Sistema --}}
        <div class="col-lg-4">
            {{-- Información del Sistema --}}
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-info-circle me-1"></i>
                        Información del Sistema
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label text-muted">ID del Cliente:</label>
                        <div><code>#{{ $clienteNatural->idCliente }}</code></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted">ID del Usuario:</label>
                        <div><code>#{{ $clienteNatural->user->idUser }}</code></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted">Fecha de Registro:</label>
                        <div>{{ $clienteNatural->created_at ? $clienteNatural->created_at->format('d/m/Y H:i') : 'No disponible' }}</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted">Última Actualización:</label>
                        <div>{{ $clienteNatural->updated_at ? $clienteNatural->updated_at->format('d/m/Y H:i') : 'No disponible' }}</div>
                    </div>
                    <div class="mb-0">
                        <label class="form-label text-muted">Estado del Cliente:</label>
                        <div>
                            @if($clienteNatural->estado == 1)
                                <span class="badge bg-success">Activo</span>
                            @else
                                <span class="badge bg-danger">Inactivo</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- Acciones Rápidas --}}
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-cogs me-1"></i>
                        Acciones
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('clienteNatural.edit', $clienteNatural->idCliente) }}" class="btn btn-warning">
                            <i class="fas fa-edit me-1"></i>
                            Editar Cliente
                        </a>
                        
                        {{-- Botón Nueva Venta --}}
                        <a href="#" class="btn btn-success">
                            <i class="fas fa-plus me-1"></i>
                            Nueva Venta
                        </a>
                        
                        {{-- Botón Ver Todas las Ventas --}}
                        @if($estadisticas['total_ventas'] > 0)
                            <a href="#" class="btn btn-info">
                                <i class="fas fa-list me-1"></i>
                                Ver Todas las Ventas
                            </a>
                        @endif
                        
                        <a href="{{ route('clienteNatural.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i>
                            Volver a la Lista
                        </a>
                        
                        {{-- Botón Eliminar --}}
                        <form action="{{ route('clienteNatural.destroy', $clienteNatural->idCliente) }}" 
                              method="POST" 
                              class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="btn btn-danger w-100"
                                    onclick="return confirm('¿Estás seguro de que deseas eliminar el cliente {{ addslashes($clienteNatural->user->nombre_completo) }}? Se marcará como inactivo.')">
                                <i class="fas fa-trash me-1"></i>
                                Eliminar Cliente
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Resumen Rápido --}}
            @if($estadisticas['total_ventas'] > 0)
                <div class="card mt-4">
                    <div class="card-header">
                        <h6 class="card-title mb-0">
                            <i class="fas fa-chart-pie me-1"></i>
                            Resumen de Actividad
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-6">
                                <div class="border-end">
                                    <div class="h4 mb-0 text-primary">{{ $estadisticas['total_ventas'] }}</div>
                                    <small class="text-muted">Ventas</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="h4 mb-0 text-success">{{ $estadisticas['productos_comprados'] }}</div>
                                <small class="text-muted">Productos</small>
                            </div>
                        </div>
                        <hr>
                        <div class="text-center">
                            <div class="h5 mb-0 text-success">Bs. {{ number_format($estadisticas['monto_total'], 2) }}</div>
                            <small class="text-muted">Total Gastado</small>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
