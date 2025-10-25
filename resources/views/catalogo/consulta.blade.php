@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3 class="card-title mb-0">Catálogo de Productos </h3>
                </div>

                <div class="card-body">
                    <!-- Filtros -->
                    <form method="GET" action="{{ route('catalogo.consulta') }}" class="mb-4">
                        <div class="row">
                            <div class="col-md-5">
                                <div class="input-group">
                                    <input type="text" name="buscar" class="form-control" 
                                           placeholder="Buscar por nombre, descripción o código..." 
                                           value="{{ request('buscar') }}">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search"></i> Buscar
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <select name="categoria_id" class="form-select" onchange="this.form.submit()">
                                    <option value="todas">Todas las categorías</option>
                                    @foreach($categorias as $categoria)
                                        <option value="{{ $categoria->idCategoria }}" 
                                            {{ request('categoria_id') == $categoria->idCategoria ? 'selected' : '' }}>
                                            {{ $categoria->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </form>

                    <!-- Listado de productos -->
                    @if($productos->count() > 0)
                        <div class="row">
                            @foreach($productos as $producto)
                                @if($loop->iteration > 4)
                                    <div class="col-md-3 mb-4">
                                        <div class="card h-100 shadow-sm">
                                            @if($producto->foto)
                                                <img src="{{ asset('storage/' . $producto->foto) }}" 
                                                     class="card-img-top" 
                                                     alt="{{ $producto->nombre }}"
                                                     style="height: 200px; object-fit: cover;">
                                            @else
                                                <div class="text-center py-5 bg-light">
                                                    <i class="fas fa-box-open fa-4x text-muted"></i>
                                                    <p class="mt-2 mb-0">Sin imagen</p>
                                                </div>
                                            @endif
                                            
                                            <div class="card-body">
                                                <h5 class="card-title">{{ $producto->nombre }}</h5>
                                                <p class="card-text text-muted small">
                                                    {{ Str::limit($producto->descripcion, 100, '...') }}
                                                </p>
                                                <p class="card-text">
                                                    <strong>Código:</strong> {{ $producto->codigo }}<br>
                                                    <strong>Categoría:</strong> {{ $producto->categoria->nombre ?? 'Sin categoría' }}
                                                </p>
                                            </div>
                                            <div class="card-footer bg-white">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span class="h5 mb-0 text-primary">
                                                        S/ {{ number_format($producto->precio, 2) }}
                                                    </span>
                                                    <span class="badge bg-{{ $producto->estado ? 'success' : 'secondary' }}">
                                                        {{ $producto->estado ? 'Disponible' : 'No disponible' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>

                        <!-- Paginación -->
                        <div class="d-flex justify-content-center mt-4">
                            {{ $productos->appends(request()->query())->links() }}
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            No se encontraron productos que coincidan con los criterios de búsqueda.
                        </div>
                    @endif
                </div>
                
                <div class="card-footer text-muted">
                    <div class="row">
                        <div class="col-md-6">
                            Mostrando {{ $productos->firstItem() ?? 0 }} a {{ $productos->lastItem() ?? 0 }} de {{ $productos->total() }} productos
                        </div>
                        <div class="col-md-6 text-end">
                            <span class="badge bg-info">Modo de solo lectura</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Estilos adicionales -->
<style>
    .card {
        transition: transform 0.2s ease-in-out;
    }
    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }
    .card-img-top {
        border-bottom: 1px solid rgba(0, 0, 0, 0.125);
    }
    .card-title {
        font-weight: 600;
        margin-bottom: 0.75rem;
    }
    .badge {
        font-size: 0.8em;
        padding: 0.4em 0.65em;
    }
</style>
@endsection
