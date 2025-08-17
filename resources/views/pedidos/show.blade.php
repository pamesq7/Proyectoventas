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
                    <div class="mb-0">
                        <label class="form-label text-muted">ID del Producto:</label>
                        <div><code>#{{ $producto->idProducto }}</code></div>
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
                        
                        {{-- Botón Eliminar --}}
                        <form action="{{ route('productos.destroy', $producto->idProducto) }}" 
                              method="POST" 
                              class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="btn btn-danger w-100"
                                    onclick="return confirm('¿Estás seguro de que deseas eliminar el producto {{ addslashes($producto->nombre) }}? Se marcará como inactivo.')">
                                <i class="fas fa-trash me-1"></i>
                                Eliminar Producto
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    {{-- Sección de Información de Variante --}}
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-gradient-info text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="card-title mb-0">
                            <i class="fas fa-tag me-1"></i>
                            Variante del Producto
                        </h6>
                        <div class="d-flex gap-2">
                            @if($producto->variante)
                                <a href="{{ route('productos.edit', $producto->idProducto) }}" class="btn btn-outline-light btn-sm">
                                    <i class="fas fa-edit me-1"></i>
                                    Cambiar Variante
                                </a>
                            @else
                                <a href="{{ route('productos.edit', $producto->idProducto) }}" class="btn btn-light btn-sm">
                                    <i class="fas fa-plus me-1"></i>
                                    Asignar Variante
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if($producto->variante)
                        {{-- Producto CON variante --}}
                        <div class="row">
                            <div class="col-md-8">
                                <div class="card border-success">
                                    <div class="card-header bg-success text-white py-2">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h6 class="mb-0">
                                                <i class="fas fa-cube me-1"></i>
                                                {{ $producto->variante->nombre }}
                                            </h6>
                                            <span class="badge bg-light text-success">
                                                ID: {{ $producto->variante->idVariante }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        @if($producto->variante->descripcion)
                                            <p class="text-muted mb-3">{{ $producto->variante->descripcion }}</p>
                                        @endif
                                        
                                        {{-- Estado de la variante --}}
                                        <div class="mb-3">
                                            <strong>Estado:</strong>
                                            @if($producto->variante->estado == 1)
                                                <span class="badge bg-success ms-1">
                                                    <i class="fas fa-check me-1"></i>Activa
                                                </span>
                                            @else
                                                <span class="badge bg-warning ms-1">
                                                    <i class="fas fa-pause me-1"></i>Inactiva
                                                </span>
                                            @endif
                                        </div>
                                        
                                        {{-- Características de la variante --}}
                                        @if($producto->variante->varianteCaracteristicas && $producto->variante->varianteCaracteristicas->count() > 0)
                                            <div class="mb-3">
                                                <strong>Características:</strong>
                                                <div class="mt-2">
                                                    @foreach($producto->variante->varianteCaracteristicas as $varianteCaracteristica)
                                                        @if($varianteCaracteristica->caracteristica && $varianteCaracteristica->caracteristica->opcion)
                                                            <span class="badge bg-primary me-1 mb-1" style="font-size: 0.8rem;">
                                                                {{ $varianteCaracteristica->caracteristica->opcion->nombre }}: 
                                                                <strong>{{ $varianteCaracteristica->caracteristica->nombre }}</strong>
                                                                @if($varianteCaracteristica->precioAdicional > 0)
                                                                    <small class="text-warning">(+${{ number_format($varianteCaracteristica->precioAdicional, 2) }})</small>
                                                                @endif
                                                            </span>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            </div>
                                        @else
                                            <div class="alert alert-warning">
                                                <i class="fas fa-exclamation-triangle me-1"></i>
                                                Esta variante no tiene características asignadas.
                                            </div>
                                        @endif
                                        
                                        {{-- Fechas --}}
                                        <div class="row text-muted small">
                                            <div class="col-md-6">
                                                <strong>Creada:</strong> {{ $producto->variante->created_at->format('d/m/Y H:i') }}
                                            </div>
                                            <div class="col-md-6">
                                                <strong>Actualizada:</strong> {{ $producto->variante->updated_at->format('d/m/Y H:i') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                {{-- Estadísticas de la variante --}}
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title">
                                            <i class="fas fa-chart-bar me-1"></i>
                                            Estadísticas
                                        </h6>
                                        
                                        <div class="mb-3">
                                            <div class="d-flex justify-content-between">
                                                <span>Características:</span>
                                                <span class="badge bg-primary">
                                                    {{ $producto->variante->varianteCaracteristicas->count() }}
                                                </span>
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <div class="d-flex justify-content-between">
                                                <span>Precio adicional:</span>
                                                <span class="text-success fw-bold">
                                                    ${{ number_format($producto->variante->varianteCaracteristicas->sum('precioAdicional'), 2) }}
                                                </span>
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <div class="d-flex justify-content-between">
                                                <span>Productos con esta variante:</span>
                                                <span class="badge bg-info">
                                                    {{ $producto->variante->productos->count() }}
                                                </span>
                                            </div>
                                        </div>
                                        
                                        <hr>
                                        <div class="d-grid gap-2">
                                            <a href="{{ route('configuracion.index') }}" class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-cogs me-1"></i>
                                                Gestionar Variantes
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        {{-- Producto SIN variante --}}
                        <div class="text-center py-4">
                            <i class="fas fa-minus-circle fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Este producto no tiene variante asignada</h5>
                            <p class="text-muted mb-4">
                                Para aprovechar las características y opciones disponibles, asigna una variante a este producto.
                            </p>
                            
                            <div class="row justify-content-center">
                                <div class="col-md-6">
                                    <div class="card border-info">
                                        <div class="card-body">
                                            <h6 class="card-title text-info">
                                                <i class="fas fa-lightbulb me-1"></i>
                                                ¿Cómo asignar una variante?
                                            </h6>
                                            <p class="card-text small text-muted">
                                                1. Haz clic en "Asignar Variante" arriba<br>
                                                2. En el formulario de edición, selecciona una variante existente<br>
                                                3. Guarda los cambios
                                            </p>
                                            <a href="{{ route('productos.edit', $producto->idProducto) }}" class="btn btn-info">
                                                <i class="fas fa-plus me-1"></i>
                                                Asignar Variante Ahora
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    {{-- Modal para Nueva Variante Manual --}}
    <div class="modal fade" id="nuevaVarianteModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-plus me-2"></i>
                        Nueva Variante Manual
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="{{ route('productos.storeVariante') }}">
                    @csrf
                    <input type="hidden" name="idProducto" value="{{ $producto->idProducto }}">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Precio <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" 
                                               class="form-control" 
                                               name="precio" 
                                               step="0.01"
                                               value="{{ $producto->precio }}"
                                               required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Stock Inicial</label>
                                    <input type="number" 
                                           class="form-control" 
                                           name="stock" 
                                           value="0"
                                           min="0">
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Seleccionar Características</label>
                            <div id="caracteristicasSelector">
                                @foreach($opciones as $opcion)
                                    @if($opcion->caracteristicas->count() > 0)
                                    <div class="mb-3">
                                        <label class="form-label text-primary">{{ $opcion->nombre }}</label>
                                        <select class="form-select" name="caracteristicas[{{ $opcion->idOpcion }}]" required>
                                            <option value="">Selecciona {{ $opcion->nombre }}...</option>
                                            @foreach($opcion->caracteristicas as $caracteristica)
                                                <option value="{{ $caracteristica->idCaracteristica }}">
                                                    {{ $caracteristica->nombre }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                        
                        <div class="form-check form-switch">
                            <input class="form-check-input" 
                                   type="checkbox" 
                                   name="estado" 
                                   value="1" 
                                   checked>
                            <label class="form-check-label fw-bold">
                                Variante activa
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i>
                            Cancelar
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>
                            Crear Variante
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection