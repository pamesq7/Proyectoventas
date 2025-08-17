@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Gestión de Productos</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Productos</li>
    </ol>

    {{-- Mensajes de éxito o error --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Mensaje específico para eliminación exitosa --}}
    @if(session('successdelete'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-trash me-2"></i>
            {{ session('successdelete') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card mb-4">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <i class="fas fa-boxes me-1"></i>
                    Lista de Productos
                </div>
                <a href="{{ route('productos.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus me-1"></i>
                    Nuevo Producto
                </a>
            </div>
        </div>
        <div class="card-body">
            @if($productos->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="productosTable">
                        <thead class="table-dark">
                            <tr>
                                <th>Foto</th>
                                <th>SKU</th>
                                <th>Nombre</th>
                                <th>Categoría</th>
                                <th>Variante</th>
                                <th>Precio Venta</th>
                                <th>Estado</th>
                                <th>Fecha Creación</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($productos as $producto)
                                <tr>
                                    <td>
                                        @if($producto->foto)
                                            <img src="{{ asset('storage/' . $producto->foto) }}" 
                                                 alt="{{ $producto->nombre }}" 
                                                 class="img-thumbnail" 
                                                 style="width: 50px; height: 50px; object-fit: cover;">
                                        @else
                                            <div class="bg-light d-flex align-items-center justify-content-center" 
                                                 style="width: 50px; height: 50px; border-radius: 4px;">
                                                <i class="fas fa-image text-muted"></i>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <code class="bg-light px-2 py-1 rounded">{{ $producto->SKU }}</code>
                                    </td>
                                    <td>
                                        <strong>{{ $producto->nombre }}</strong>
                                        @if($producto->descripcion)
                                            <br>
                                            <small class="text-muted">{{ Str::limit($producto->descripcion, 40) }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($producto->categoria)
                                            <span class="badge bg-secondary">
                                                {{ $producto->categoria->nombreCategoria }}
                                            </span>
                                        @else
                                            <span class="badge bg-warning">Sin categoría</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($producto->variante)
                                            <span class="badge bg-primary">
                                                <i class="fas fa-tag me-1"></i>{{ $producto->variante->nombre }}
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">
                                                <i class="fas fa-minus me-1"></i>Sin variante
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="fw-bold text-success">
                                            ${{ number_format($producto->precioVenta, 0) }}
                                        </span>
                                        @if($producto->precioProduccion)
                                            <br>
                                            <small class="text-muted">
                                                Costo: ${{ number_format($producto->precioProduccion, 0) }}
                                            </small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($producto->estado == 1)
                                            <span class="badge bg-success">
                                                <i class="fas fa-check me-1"></i>Activo
                                            </span>
                                        @else
                                            <span class="badge bg-danger">
                                                <i class="fas fa-times me-1"></i>Inactivo
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $producto->created_at ? $producto->created_at->format('d/m/Y') : 'No disponible' }}
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('productos.show', $producto->idProducto) }}" 
                                               class="btn btn-info btn-sm" 
                                               title="Ver detalles">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('productos.edit', $producto->idProducto) }}" 
                                               class="btn btn-warning btn-sm" 
                                               title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <!-- Botón Eliminar con Formulario DELETE -->
                                            <form action="{{ route('productos.destroy', $producto->idProducto) }}" 
                                                  method="POST" 
                                                  style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="btn btn-danger btn-sm"
                                                        title="Eliminar"
                                                        onclick="return confirm('¿Estás seguro de que deseas eliminar el producto {{ addslashes($producto->nombre) }}? Se marcará como inactivo.')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No hay productos registrados</h5>
                    <p class="text-muted">Comienza agregando tu primer producto</p>
                    <a href="{{ route('productos.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>
                        Crear Primer Producto
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Inicializar DataTable
    $(document).ready(function() {
        $('#productosTable').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json"
            },
            "responsive": true,
            "order": [[ 2, "asc" ]], // Ordenar por nombre
            "pageLength": 25
        });
    });
</script>
@endpush