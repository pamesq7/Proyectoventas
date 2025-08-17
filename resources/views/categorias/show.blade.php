@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Detalles de Categoría</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('categorias.index') }}">Categorías</a></li>
        <li class="breadcrumb-item active">{{ $categoria->nombreCategoria }}</li>
    </ol>

    {{-- Encabezado con información principal --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow">
                <div class="card-header bg-gradient-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-0">
                                <i class="fas fa-tag me-2"></i>
                                {{ $categoria->nombreCategoria }}
                            </h4>
                            <small class="opacity-75">ID: {{ $categoria->idCategoria }}</small>
                        </div>
                        <div>
                            @if($categoria->estado == 1)
                                <span class="badge bg-success fs-6">
                                    <i class="fas fa-check me-1"></i>Activo
                                </span>
                            @else
                                <span class="badge bg-danger fs-6">
                                    <i class="fas fa-times me-1"></i>Inactivo
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <h6 class="text-muted mb-2">Descripción:</h6>
                            <p class="fs-5 mb-3">
                                {{ $categoria->descripcion ?: 'Sin descripción proporcionada.' }}
                            </p>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center">
                                <div class="display-4 text-primary mb-2">
                                    <i class="fas fa-boxes"></i>
                                </div>
                                <h3 class="text-primary mb-1">{{ $categoria->productos->count() }}</h3>
                                <p class="text-muted mb-0">Productos asociados</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Información detallada --}}
        <div class="col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        Información Detallada
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted">ID de Categoría:</label>
                        <p class="fs-5">{{ $categoria->idCategoria }}</p>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted">Nombre:</label>
                        <p class="fs-5">{{ $categoria->nombreCategoria }}</p>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted">Estado:</label>
                        <p>
                            @if($categoria->estado == 1)
                                <span class="badge bg-success">
                                    <i class="fas fa-check me-1"></i>Activo
                                </span>
                            @else
                                <span class="badge bg-danger">
                                    <i class="fas fa-times me-1"></i>Inactivo
                                </span>
                            @endif
                        </p>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted">Fecha de Creación:</label>
                        <p class="fs-6">
                            <i class="fas fa-calendar-plus me-1 text-success"></i>
                            @if($categoria->created_at)
                                {{ $categoria->created_at->format('d/m/Y') }}
                                <br>
                                <small class="text-muted">{{ $categoria->created_at->format('H:i:s') }}</small>
                            @else
                                <span class="text-muted">No disponible</span>
                            @endif
                        </p>
                    </div>
                    
                    <div class="mb-0">
                        <label class="form-label fw-bold text-muted">Última Actualización:</label>
                        <p class="fs-6">
                            <i class="fas fa-calendar-edit me-1 text-warning"></i>
                            @if($categoria->updated_at)
                                {{ $categoria->updated_at->format('d/m/Y') }}
                                <br>
                                <small class="text-muted">{{ $categoria->updated_at->format('H:i:s') }}</small>
                            @else
                                <span class="text-muted">No disponible</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Productos asociados --}}
        <div class="col-lg-8 mb-4">
            <div class="card h-100">
                <div class="card-header bg-success text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-boxes me-2"></i>
                            Productos Asociados ({{ $categoria->productos->count() }})
                        </h5>
                        @if($categoria->productos->count() > 0)
                            <small>Total de productos en esta categoría</small>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    @if($categoria->productos->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>SKU</th>
                                        <th>Nombre</th>
                                        <th>Precio Venta</th>
                                        <th>Stock</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($categoria->productos as $producto)
                                        <tr>
                                            <td>
                                                <code>{{ $producto->SKU }}</code>
                                            </td>
                                            <td>
                                                <strong>{{ $producto->nombre }}</strong>
                                                @if($producto->descripcion)
                                                    <br>
                                                    <small class="text-muted">{{ Str::limit($producto->descripcion, 50) }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="fw-bold text-success">
                                                    ${{ number_format($producto->precioVenta, 2) }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($producto->stock > 10)
                                                    <span class="badge bg-success">{{ $producto->stock }}</span>
                                                @elseif($producto->stock > 0)
                                                    <span class="badge bg-warning">{{ $producto->stock }}</span>
                                                @else
                                                    <span class="badge bg-danger">{{ $producto->stock }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($producto->estado == 1)
                                                    <span class="badge bg-success">Activo</span>
                                                @else
                                                    <span class="badge bg-danger">Inactivo</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('productos.show', $producto->idProducto) }}" 
                                                   class="btn btn-sm btn-outline-primary" 
                                                   title="Ver producto">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="mb-3">
                                <i class="fas fa-box-open fa-3x text-muted"></i>
                            </div>
                            <h5 class="text-muted">No hay productos en esta categoría</h5>
                            <p class="text-muted mb-3">Aún no se han asignado productos a esta categoría.</p>
                            <a href="{{ route('productos.create') }}" class="btn btn-success">
                                <i class="fas fa-plus me-1"></i>
                                Agregar Primer Producto
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Estadísticas adicionales --}}
    @if($categoria->productos->count() > 0)
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-dark text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-chart-bar me-2"></i>
                            Estadísticas de la Categoría
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-md-3 mb-3">
                                <div class="border rounded p-3">
                                    <h4 class="text-primary">{{ $categoria->productos->count() }}</h4>
                                    <p class="text-muted mb-0">Total Productos</p>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="border rounded p-3">
                                    <h4 class="text-success">{{ $categoria->productos->where('estado', 1)->count() }}</h4>
                                    <p class="text-muted mb-0">Productos Activos</p>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="border rounded p-3">
                                    <h4 class="text-warning">{{ $categoria->productos->where('stock', '!=', null)->sum('stock') ?: 0 }}</h4>
                                    <p class="text-muted mb-0">Stock Total</p>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="border rounded p-3">
                                    <h4 class="text-info">${{ $categoria->productos->where('precioVenta', '!=', null)->avg('precioVenta') ? number_format($categoria->productos->where('precioVenta', '!=', null)->avg('precioVenta'), 2) : '0.00' }}</h4>
                                    <p class="text-muted mb-0">Precio Promedio</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Botones de acción --}}
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <a href="{{ route('categorias.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i>
                                Volver a la Lista
                            </a>
                        </div>
                        <div>
                            <a href="{{ route('categorias.edit', $categoria->idCategoria) }}" class="btn btn-warning me-2">
                                <i class="fas fa-edit me-1"></i>
                                Editar Categoría
                            </a>
                            @if($categoria->productos->count() == 0)
                                <button type="button" 
                                        class="btn btn-danger"
                                        onclick="confirmarEliminacion()"
                                        title="Eliminar categoría">
                                    <i class="fas fa-trash me-1"></i>
                                    Eliminar
                                </button>
                            @else
                                <button type="button" 
                                        class="btn btn-danger" 
                                        disabled
                                        title="No se puede eliminar: tiene productos asociados">
                                    <i class="fas fa-trash me-1"></i>
                                    Eliminar
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal de confirmación para eliminar --}}
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Eliminación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>¿Estás seguro de que deseas eliminar la categoría <strong>{{ $categoria->nombreCategoria }}</strong>?</p>
                <p class="text-danger"><small><i class="fas fa-exclamation-triangle"></i> Esta acción no se puede deshacer.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form action="{{ route('categorias.destroy', $categoria->idCategoria) }}" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Eliminar</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Función para confirmar eliminación
    function confirmarEliminacion() {
        var deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        deleteModal.show();
    }

    // Tooltip para botones deshabilitados
    $(document).ready(function() {
        $('[data-bs-toggle="tooltip"]').tooltip();
        
        // Inicializar tooltips para botones deshabilitados
        $('button[disabled]').tooltip();
    });
</script>
@endpush