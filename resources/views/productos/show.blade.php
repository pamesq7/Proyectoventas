@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Detalles del Producto</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('productos.index') }}">Productos</a></li>
        <li class="breadcrumb-item active">{{ $producto->nombre }}</li>
    </ol>

    <div class="row">
        {{-- Información Principal del Producto --}}
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-box me-2"></i>
                        {{ $producto->nombre }}
                        @if($producto->estado == 1)
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
                        {{-- Imagen del Producto --}}
                        <div class="col-md-4">
                            @if($producto->foto)
                                <img src="{{ asset('storage/' . $producto->foto) }}" 
                                     alt="{{ $producto->nombre }}" 
                                     class="img-fluid rounded shadow-sm mb-3"
                                     style="max-height: 300px; width: 100%; object-fit: cover;">
                            @else
                                <div class="bg-light d-flex align-items-center justify-content-center rounded" 
                                     style="height: 300px;">
                                    <div class="text-center text-muted">
                                        <i class="fas fa-image fa-3x mb-2"></i>
                                        <p>Sin imagen</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                        
                        {{-- Información Básica --}}
                        <div class="col-md-8">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="mb-3">
                                        <label class="form-label text-muted">SKU:</label>
                                        <div>
                                            <code class="bg-light px-2 py-1 rounded fs-6">{{ $producto->SKU }}</code>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="mb-3">
                                        <label class="form-label text-muted">Categoría:</label>
                                        <div>
                                            @if($producto->categoria)
                                                <span class="badge bg-secondary fs-6">
                                                    <i class="fas fa-folder me-1"></i>
                                                    {{ $producto->categoria->nombreCategoria }}
                                                </span>
                                            @else
                                                <span class="badge bg-warning fs-6">Sin categoría</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            @if($producto->descripcion)
                                <div class="mb-3">
                                    <label class="form-label text-muted">Descripción:</label>
                                    <p class="mb-0">{{ $producto->descripcion }}</p>
                                </div>
                            @endif
                            
                            {{-- Precios --}}
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="mb-3">
                                        <label class="form-label text-muted">Precio de Venta:</label>
                                        <div class="h4 text-success mb-0">
                                            ${{ number_format($producto->precioVenta, 2) }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="mb-3">
                                        <label class="form-label text-muted">Precio de Producción:</label>
                                        <div class="h5 text-info mb-0">
                                            ${{ number_format($producto->precioProduccion, 2) }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            {{-- Margen de Ganancia --}}
                            @if($producto->precioVenta > 0 && $producto->precioProduccion > 0)
                                @php
                                    $margen = (($producto->precioVenta - $producto->precioProduccion) / $producto->precioVenta) * 100;
                                    $ganancia = $producto->precioVenta - $producto->precioProduccion;
                                    $alertClass = $margen >= 40 ? 'success' : ($margen >= 20 ? 'warning' : 'danger');
                                @endphp
                                <div class="alert alert-{{ $alertClass }} mb-3">
                                    <i class="fas fa-chart-line me-2"></i>
                                    <strong>Margen de Ganancia:</strong> {{ number_format($margen, 2) }}% 
                                    | <strong>Ganancia:</strong> ${{ number_format($ganancia, 2) }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        {{-- Panel de Estadísticas --}}
        <div class="col-lg-4">
            {{-- Inventario --}}
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-warehouse me-1"></i>
                        Inventario
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="mb-2">
                                <div class="h4 mb-0 text-primary">{{ $producto->cantidad }}</div>
                                <small class="text-muted">Cantidad</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="mb-2">
                                <div class="h4 mb-0 text-info">{{ $producto->pedidoMinimo }}</div>
                                <small class="text-muted">Mín. Pedido</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
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
                        <label class="form-label text-muted">Fecha de Creación:</label>
                        <div>{{ $producto->created_at ? $producto->created_at->format('d/m/Y H:i') : 'No disponible' }}</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted">Última Actualización:</label>
                        <div>{{ $producto->updated_at ? $producto->updated_at->format('d/m/Y H:i') : 'No disponible' }}</div>
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
                        <a href="{{ route('productos.edit', $producto->idProducto) }}" class="btn btn-warning">
                            <i class="fas fa-edit me-1"></i>
                            Editar Producto
                        </a>
                        <a href="{{ route('productos.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i>
                            Volver a la Lista
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
</div>
@endsection