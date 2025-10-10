@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Catálogo de Productos</h1>
    
    <!-- Filtros por categoría -->
    <div class="mb-4">
        @foreach($categorias as $categoria)
            <a href="{{ route('tienda.categoria', $categoria->slug) }}" 
               class="btn btn-outline-primary me-2">
                {{ $categoria->nombre }}
            </a>
        @endforeach
    </div>
    
    <!-- Grid de productos -->
    <div class="row">
        @foreach($productos as $producto)
            <div class="col-md-3 mb-4">
                <div class="card">
                    <img src="{{ asset('storage/' . $producto->imagen) }}" 
                         class="card-img-top" alt="{{ $producto->nombre }}">
                    <div class="card-body">
                        <h5 class="card-title">{{ $producto->nombre }}</h5>
                        <p class="card-text">{{ Str::limit($producto->descripcion, 100) }}</p>
                        <a href="{{ route('producto.show', $producto->idProducto) }}" 
                           class="btn btn-primary">Ver Detalles</a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    
    <!-- Paginación -->
    {{ $productos->links() }}
</div>
@endsection