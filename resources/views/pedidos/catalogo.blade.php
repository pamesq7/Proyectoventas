@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">

    {{-- Encabezado + CTAs --}}
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-md-between mt-4 gap-3">
        <div>
            <h1 class="mb-1">
                <i class="fas fa-shopping-cart me-2"></i>Catálogo de Productos
            </h1>
            <p class="text-muted mb-0">
                Elige un producto del catálogo o crea el tuyo desde cero con tu propio diseño.
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('pedidos.personalizar') }}" class="btn btn-success">
                <i class="fas fa-wand-magic-sparkles me-2"></i>Hacer mi pedido personalizado
            </a>
        </div>
    </div>

    <ol class="breadcrumb mb-4 mt-3">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Catálogo</li>
    </ol>

    {{-- Filtros + búsqueda --}}
    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-center">
                <div class="col-12 col-lg-6">
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <span class="fw-semibold me-1">Categorías:</span>
                        <button type="button" class="btn btn-sm btn-outline-primary active rounded-pill" data-categoria="all">
                            Todas
                        </button>
                        @foreach($categorias as $categoria)
                            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill" data-categoria="{{ $categoria->idCategoria }}">
                                {{ $categoria->nombreCategoria }}
                            </button>
                        @endforeach
                    </div>
                </div>
                <div class="col-12 col-lg-6">
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="fas fa-magnifying-glass"></i></span>
                        <input id="buscarInput" type="text" class="form-control" placeholder="Buscar por nombre…">
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Grid de productos (4 por fila en desktop) --}}
    <div class="row g-3" id="productosGrid">
        @foreach($productos as $producto)
            @php
                // Resolver imagen: 1) foto del producto 2) primer diseño vinculado 3) null
                $imagenSrc = null;
                if ($producto->foto) {
                    $imagenSrc = asset('storage/' . $producto->foto);
                } else {
                    $pd = optional($producto->productoDiseno)->first();
                    $archivo = optional(optional($pd)->diseno)->archivo;
                    if ($archivo) {
                        $imagenSrc = asset('storage/' . $archivo);
                    }
                }
            @endphp

            <div class="col-12 col-sm-6 col-md-4 col-lg-3 producto-card"
                 data-categoria="{{ $producto->idCategoria }}"
                 data-nombre="{{ Str::lower($producto->nombre) }}">
                <div class="card h-100 shadow-sm product-card border-0">

                    {{-- Imagen --}}
                    <div class="position-relative overflow-hidden">
                        @if($imagenSrc)
                            <img src="{{ $imagenSrc }}"
                                 loading="lazy"
                                 class="card-img-top product-img"
                                 alt="{{ $producto->nombre }}">
                        @else
                            <div class="bg-light d-flex align-items-center justify-content-center"
                                 style="height:200px;">
                                <i class="fas fa-image fa-3x text-muted"></i>
                            </div>
                        @endif

                        {{-- Badge categoría --}}
                        @if($producto->categoria)
                            <span class="position-absolute top-0 end-0 m-2 badge bg-primary">
                                {{ $producto->categoria->nombreCategoria }}
                            </span>
                        @endif
                    </div>

                    {{-- Contenido --}}
                    <div class="card-body d-flex flex-column">
                        <h6 class="card-title mb-1 text-truncate" title="{{ $producto->nombre }}">
                            {{ $producto->nombre }}
                        </h6>

                        @if($producto->descripcion)
                            <p class="card-text text-muted small mb-2">
                                {{ \Illuminate\Support\Str::limit($producto->descripcion, 90) }}
                            </p>
                        @endif

                        {{-- Variante --}}
                        @if($producto->variante)
                            <div class="mb-2">
                                <span class="badge bg-info-subtle text-dark border">
                                    <i class="fas fa-tag me-1"></i>{{ $producto->variante->nombre }}
                                </span>
                            </div>
                        @endif

                        {{-- Precio --}}
                        <div class="mb-3">
                            <div class="d-flex align-items-baseline gap-2">
                                <span class="h5 text-success mb-0">Bs{{ number_format($producto->precioVenta, 0) }}</span>
                                @if($producto->precioProduccion)
                                    <small class="text-muted">Costo: Bs{{ number_format($producto->precioProduccion, 0) }}</small>
                                @endif
                            </div>
                        </div>

                        {{-- Acciones --}}
                        <div class="mt-auto d-grid gap-2">
                            <a href="{{ route('pedidos.configurar', $producto->idProducto) }}"
                               class="btn btn-primary">
                                <i class="fas fa-sliders me-2"></i>Configurar y pedir
                            </a>
                            <button type="button"
                                    class="btn btn-outline-secondary btn-sm"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalDetalles{{ $producto->idProducto }}">
                                <i class="fas fa-circle-info me-1"></i>Ver detalles
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Modal de detalles --}}
            <div class="modal fade" id="modalDetalles{{ $producto->idProducto }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">{{ $producto->nombre }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    @if($imagenSrc)
                                        <img src="{{ $imagenSrc }}" loading="lazy" class="img-fluid rounded" alt="{{ $producto->nombre }}">
                                    @else
                                        <div class="bg-light d-flex align-items-center justify-content-center rounded" style="height:300px;">
                                            <i class="fas fa-image fa-4x text-muted"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex align-items-baseline gap-2 mb-2">
                                        <span class="h4 text-success mb-0">Bs{{ number_format($producto->precioVenta, 0) }}</span>
                                        @if($producto->precioProduccion)
                                            <small class="text-muted">Costo: Bs{{ number_format($producto->precioProduccion, 0) }}</small>
                                        @endif
                                    </div>

                                    @if($producto->descripcion)
                                        <p class="mb-2"><strong>Descripción</strong></p>
                                        <p class="text-muted">{{ $producto->descripcion }}</p>
                                    @endif

                                    @if($producto->categoria)
                                        <p class="mb-1"><strong>Categoría:</strong> {{ $producto->categoria->nombreCategoria }}</p>
                                    @endif

                                    @if($producto->variante)
                                        <p class="mb-1"><strong>Variante:</strong> {{ $producto->variante->nombre }}</p>
                                        @if($producto->variante->varianteCaracteristicas->count() > 0)
                                            <p class="mb-1"><strong>Opciones disponibles</strong></p>
                                            <ul class="list-unstyled small">
                                                @foreach($producto->variante->varianteCaracteristicas->groupBy('caracteristica.opcion.nombre') as $opcionNombre => $caracteristicas)
                                                    <li class="mb-2">
                                                        <span class="fw-semibold">{{ $opcionNombre }}:</span>
                                                        <div class="mt-1">
                                                            @foreach($caracteristicas as $vc)
                                                                <span class="badge bg-light text-dark border me-1 mb-1">
                                                                    {{ $vc->caracteristica->nombre }}
                                                                </span>
                                                            @endforeach
                                                        </div>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    @endif

                                    <p class="mb-0"><strong>SKU:</strong> {{ $producto->SKU }}</p>
                                    @if($producto->pedidoMinimo)
                                        <p class="mb-0"><strong>Pedido mínimo:</strong> {{ $producto->pedidoMinimo }} unid.</p>
                                    @endif>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cerrar</button>
                            <a href="{{ route('pedidos.configurar', $producto->idProducto) }}" class="btn btn-primary">
                                <i class="fas fa-sliders me-2"></i>Configurar y pedir
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Vacío --}}
    @if($productos->count() === 0)
        <div class="text-center py-5">
            <i class="fas fa-box-open fa-4x text-muted mb-3"></i>
            <h4 class="text-muted">No hay productos disponibles</h4>
            <p class="text-muted">Vuelve más tarde para ver nuestros productos.</p>
        </div>
    @endif
</div>
@endsection

@push('styles')
<style>
    .product-card { transition: transform .15s ease, box-shadow .15s ease; }
    .product-card:hover { transform: translateY(-2px); box-shadow: 0 0.75rem 1.25rem rgba(0,0,0,.08); }
    .product-img { height: 200px; object-fit: cover; }
    .btn-outline-primary.active { background-color: #0d6efd; color: #fff; }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const botonesCategorias = document.querySelectorAll('[data-categoria]');
    const gridCards = document.querySelectorAll('#productosGrid .producto-card');
    const buscarInput = document.getElementById('buscarInput');

    function aplicaFiltros() {
        const categoriaSeleccionada = document.querySelector('[data-categoria].active')?.dataset.categoria || 'all';
        const q = (buscarInput.value || '').trim().toLowerCase();

        gridCards.forEach(card => {
            const coincideCategoria = (categoriaSeleccionada === 'all') || (card.dataset.categoria === categoriaSeleccionada);
            const coincideBusqueda = q === '' || (card.dataset.nombre || '').includes(q);
            card.style.display = (coincideCategoria && coincideBusqueda) ? '' : 'none';
        });
    }

    // Filtro por categoría
    botonesCategorias.forEach(btn => {
        btn.addEventListener('click', function() {
            botonesCategorias.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            aplicaFiltros();
        });
    });

    // Búsqueda por nombre
    buscarInput.addEventListener('input', aplicaFiltros);
});
</script>
@endpush
