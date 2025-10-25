@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Productos de {{ $categoria->nombre }}</h1>
    
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
    
    {{ $productos->links() }}
</div>
@endsection