@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex align-items-center justify-content-between mt-4">
        <h1 class="mb-0">
            <i class="fas fa-shopping-cart me-2"></i>Catálogo de Productos
        </h1>
        <a href="{{ route('pedidos.personalizar') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>PERSONALIZAR MI DISEÑO
        </a>
    </div>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Catálogo</li>
    </ol>

    {{-- Carrito flotante --}}
    <div class="position-fixed" style="top: 100px; right: 20px; z-index: 1000;">
        <a href="{{ route('pedidos.carrito') }}" class="btn btn-primary btn-lg rounded-circle shadow">
            <i class="fas fa-shopping-cart"></i>
            @if(session('carrito') && count(session('carrito')) > 0)
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                    {{ count(session('carrito')) }}
                </span>
            @endif
        </a>
    </div>

    {{-- Filtros por categoría --}}
    <div class="card mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h5 class="mb-0">Filtrar por categoría:</h5>
                </div>
                <div class="col-md-6">
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-outline-primary active" data-categoria="all">
                            Todas
                        </button>
                        @foreach($categorias as $categoria)
                            <button type="button" class="btn btn-outline-primary" data-categoria="{{ $categoria->idCategoria }}">
                                {{ $categoria->nombreCategoria }}
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Grid de productos --}}
    <div class="row" id="productosGrid">
        @foreach($productos as $producto)
            <div class="col-lg-4 col-md-6 mb-4 producto-card" data-categoria="{{ $producto->idCategoria }}">
                <div class="card h-100 shadow-sm">
                    {{-- Imagen del producto --}}
                    <div class="position-relative">
                        @php
                            $imagenSrc = null;
                            if ($producto->foto) {
                                $imagenSrc = asset('storage/' . $producto->foto);
                            } elseif ($producto->diseno && $producto->diseno->archivo) {
                                $imagenSrc = asset('storage/' . $producto->diseno->archivo);
                            }
                        @endphp
                        
                        @if($imagenSrc)
                            <img src="{{ $imagenSrc }}" 
                                 class="card-img-top" 
                                 style="height: 250px; object-fit: cover;"
                                 alt="{{ $producto->nombre }}">
                        @else
                            <div class="bg-light d-flex align-items-center justify-content-center" 
                                 style="height: 250px;">
                                <i class="fas fa-image fa-3x text-muted"></i>
                            </div>
                        @endif
                        
                        {{-- Badge de categoría --}}
                        @if($producto->categoria)
                            <span class="position-absolute top-0 end-0 m-2 badge bg-primary">
                                {{ $producto->categoria->nombreCategoria }}
                            </span>
                        @endif
                    </div>

                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">{{ $producto->nombre }}</h5>
                        
                        @if($producto->descripcion)
                            <p class="card-text text-muted small">
                                {{ Str::limit($producto->descripcion, 100) }}
                            </p>
                        @endif

                        {{-- Información de variante --}}
                        @if($producto->variante)
                            <div class="mb-2">
                                <span class="badge bg-info">
                                    <i class="fas fa-tag me-1"></i>{{ $producto->variante->nombre }}
                                </span>
                            </div>
                        @endif

                        {{-- Precio --}}
                        <div class="mb-3">
                            <h4 class="text-success mb-0">
                                ${{ number_format($producto->precioVenta, 0) }}
                            </h4>
                            @if($producto->precioProduccion)
                                <small class="text-muted">
                                    Costo: ${{ number_format($producto->precioProduccion, 0) }}
                                </small>
                            @endif
                        </div>

                        {{-- Botones de acción --}}
                        <div class="mt-auto">
                            <div class="d-grid gap-2">
                                <a href="{{ route('pedidos.configurar', $producto->idProducto) }}" 
                                   class="btn btn-primary">
                                    <i class="fas fa-cog me-2"></i>Configurar y Pedir
                                </a>
                                <button type="button" 
                                        class="btn btn-outline-info btn-sm" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#modalDetalles{{ $producto->idProducto }}">
                                    <i class="fas fa-info-circle me-1"></i>Ver Detalles
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Modal de detalles del producto --}}
            <div class="modal fade" id="modalDetalles{{ $producto->idProducto }}" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">{{ $producto->nombre }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    @if($imagenSrc)
                                        <img src="{{ $imagenSrc }}" 
                                             class="img-fluid rounded" 
                                             alt="{{ $producto->nombre }}">
                                    @else
                                        <div class="bg-light d-flex align-items-center justify-content-center rounded" 
                                             style="height: 300px;">
                                            <i class="fas fa-image fa-4x text-muted"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    <h4 class="text-success mb-3">
                                        ${{ number_format($producto->precioVenta, 0) }}
                                    </h4>
                                    
                                    @if($producto->descripcion)
                                        <p><strong>Descripción:</strong></p>
                                        <p>{{ $producto->descripcion }}</p>
                                    @endif

                                    @if($producto->categoria)
                                        <p><strong>Categoría:</strong> {{ $producto->categoria->nombreCategoria }}</p>
                                    @endif

                                    @if($producto->variante)
                                        <p><strong>Variante:</strong> {{ $producto->variante->nombre }}</p>
                                        
                                        {{-- Mostrar características de la variante --}}
                                        @if($producto->variante->varianteCaracteristicas->count() > 0)
                                            <p><strong>Opciones disponibles:</strong></p>
                                            <ul class="list-unstyled">
                                                @foreach($producto->variante->varianteCaracteristicas->groupBy('caracteristica.opcion.nombre') as $opcionNombre => $caracteristicas)
                                                    <li class="mb-2">
                                                        <strong>{{ $opcionNombre }}:</strong>
                                                        <div class="ms-3">
                                                            @foreach($caracteristicas as $varCaracteristica)
                                                                <span class="badge bg-light text-dark me-1">
                                                                    {{ $varCaracteristica->caracteristica->nombre }}
                                                                </span>
                                                            @endforeach
                                                        </div>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    @endif

                                    <p><strong>SKU:</strong> {{ $producto->SKU }}</p>
                                    
                                    @if($producto->pedidoMinimo)
                                        <p><strong>Pedido mínimo:</strong> {{ $producto->pedidoMinimo }} unidades</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                            <a href="{{ route('pedidos.configurar', $producto->idProducto) }}" 
                               class="btn btn-primary">
                                <i class="fas fa-cog me-2"></i>Configurar y Pedir
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Mensaje si no hay productos --}}
    @if($productos->count() === 0)
        <div class="text-center py-5">
            <i class="fas fa-box-open fa-4x text-muted mb-3"></i>
            <h4 class="text-muted">No hay productos disponibles</h4>
            <p class="text-muted">Vuelve más tarde para ver nuestros productos.</p>
        </div>
    @endif
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Filtro por categorías
    const botonesCategorias = document.querySelectorAll('[data-categoria]');
    const productosCards = document.querySelectorAll('.producto-card');

    botonesCategorias.forEach(boton => {
        boton.addEventListener('click', function() {
            // Actualizar botones activos
            botonesCategorias.forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            const categoriaSeleccionada = this.dataset.categoria;

            // Filtrar productos
            productosCards.forEach(card => {
                if (categoriaSeleccionada === 'all' || card.dataset.categoria === categoriaSeleccionada) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });
});
</script>
@endpush
@endsection
