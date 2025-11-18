@extends('layouts.app')

@section('title', 'Mi Carrito')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">
        <i class="fas fa-shopping-cart me-2"></i>Mi Carrito
    </h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('pedidos.catalogo') }}">Catálogo</a></li>
        <li class="breadcrumb-item active">Carrito</li>
    </ol>

    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header">
                    <strong>Productos en el carrito</strong>
                </div>
                <div class="card-body p-0">
                    @php
                        // Agrupar productos como en la vista lateral
                        $productosAgrupados = [];
                        foreach($carrito as $item) {
                            $idProducto = $item['idProducto'];
                            if (!isset($productosAgrupados[$idProducto])) {
                                $productosAgrupados[$idProducto] = [
                                    'idProducto' => $item['idProducto'],
                                    'producto' => $item['producto'],
                                    'imagen' => $item['imagen'] ?? null,
                                    'precioUnitario' => $item['precioUnitario'] ?? 0,
                                    'cantidad_total' => 0,
                                    'subtotal' => 0,
                                    'items' => []
                                ];
                            }
                            $productosAgrupados[$idProducto]['cantidad_total'] += $item['cantidad'] ?? 0;
                            $productosAgrupados[$idProducto]['subtotal'] += $item['subtotal'] ?? 0;
                            $productosAgrupados[$idProducto]['items'][] = $item;
                        }
                    @endphp

                    @if(empty($productosAgrupados))
                        <div class="p-4 text-center text-muted">
                            <i class="fas fa-shopping-cart fa-2x mb-2"></i>
                            <p>Tu carrito está vacío</p>
                            <a href="{{ route('pedidos.catalogo') }}" class="btn btn-primary mt-2">
                                <i class="fas fa-store me-1"></i> Ver catálogo
                            </a>
                        </div>
                    @else
                            @foreach($productosAgrupados as $index => $producto)
                            <div class="card mb-3">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        {{-- IMAGEN --}}
                                        <div class="col-md-2">
                                            @php
                                            $imagenSrc = null;
                                            if (!empty($producto['imagen'])) {
                                                $imagenSrc = asset('storage/' . $producto['imagen']);
                                            }
                                            @endphp

                                            @if($imagenSrc)
                                            <img src="{{ $imagenSrc }}"
                                                alt="{{ $producto['producto'] }}"
                                                class="img-fluid rounded"
                                                style="width: 80px; height: 80px; object-fit: cover;">
                                            @else
                                            <div class="bg-light d-flex align-items-center justify-content-center rounded"
                                                style="width: 80px; height: 80px;">
                                                <i class="fas fa-image text-muted fa-2x"></i>
                                            </div>
                                            @endif
                                        </div>

                                        {{-- DETALLES --}}
                                        <div class="col-md-6">
                                            <h5 class="mb-2">{{ $producto['producto'] }}</h5>
                                            <p class="mb-1">
                                                <strong>Cantidad:</strong> {{ $producto['cantidad_total'] }} unidad(es)
                                            </p>
                                            <p class="mb-1">
                                                <strong>Precio unitario:</strong> Bs {{ number_format($producto['precioUnitario'], 2) }}
                                            </p>
                                            
                                            {{-- Detalles de tallas --}}
                                            @if(count($producto['items']) > 1)
                                            <button class="btn btn-sm btn-link p-0 text-decoration-none" 
                                                    data-bs-toggle="collapse" 
                                                    data-bs-target="#detalles-{{ $index }}">
                                                <small class="text-primary">
                                                    <i class="fas fa-chevron-down me-1"></i>Ver tallas ({{ count($producto['items']) }})
                                                </small>
                                            </button>
                                            @else
                                            <small class="text-muted">
                                                @if(!empty($producto['items'][0]['talla']))
                                                Talla: {{ $producto['items'][0]['talla'] }}
                                                @endif
                                            </small>
                                            @endif
                                            
                                            @if(count($producto['items']) > 1)
                                            <div class="collapse mt-2" id="detalles-{{ $index }}">
                                                <div class="small text-muted border-top pt-2">
                                                    @foreach($producto['items'] as $detalle)
                                                    @php
                                                        $precioUnit = (float)($detalle['precioUnitario'] ?? 0);
                                                        $subDet     = (float)($detalle['subtotal'] ?? 0);
                                                        $cantDet    = (int)($detalle['cantidad'] ?? 0);

                                                        // Si por bug viene cantidad 0 pero hay subtotal y precio,
                                                        // la reconstruimos: subtotal / precioUnitario
                                                        if ($cantDet === 0 && $precioUnit > 0 && $subDet > 0) {
                                                            $cantDet = (int) round($subDet / $precioUnit);
                                                        }
                                                    @endphp
                                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                                        <span>
                                                            @if(!empty($detalle['talla']))Talla {{ $detalle['talla'] }}@endif
                                                            @if(!empty($detalle['nombrePersonalizado']) || !empty($detalle['numeroPersonalizado']))
                                                            <small class="text-primary">
                                                                @if($detalle['nombrePersonalizado'])Nombre: {{ $detalle['nombrePersonalizado'] }}@endif
                                                                @if($detalle['numeroPersonalizado']) - Nº: {{ $detalle['numeroPersonalizado'] }}@endif
                                                            </small>
                                                            @endif
                                                        </span>
                                                        <span>
                                                            {{ $cantDet }} × Bs {{ number_format($precioUnit, 2) }}
                                                            = Bs {{ number_format($subDet, 2) }}
                                                        </span>
                                                    </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                            @endif
                                        </div>

                                        {{-- PRECIO Y ACCIONES --}}
                                        <div class="col-md-4 text-end">
                                            <h4 class="text-primary mb-2">
                                                Bs {{ number_format($producto['subtotal'], 2) }}
                                            </h4>
                                            <small class="text-muted d-block mb-3">Subtotal</small>
                                            <button
                                                class="btn btn-sm btn-outline-danger btnEliminarProductoCarrito"
                                                data-idproducto="{{ $producto['idProducto'] }}">
                                                <i class="fas fa-trash me-1"></i> Eliminar
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                    @endif
                </div>
            </div>
        </div>

        {{-- RESUMEN Y BOTONES --}}
        <div class="col-lg-4">
            <div class="card sticky-top" style="top:20px;">
                <div class="card-header bg-primary text-white">
                    <strong>Resumen del pedido</strong>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between fw-bold mb-2">
                        <span>Subtotal:</span>
                        <span>Bs {{ number_format($total, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between fw-bold text-primary fs-5 mb-3">
                        <span>Total:</span>
                        <span>Bs {{ number_format($total, 2) }}</span>
                    </div>

                    <a href="{{ route('pedidos.checkout') }}" class="btn btn-success btn-lg w-100 mb-2">
                        <i class="fas fa-credit-card me-1"></i> Iniciar compra
                    </a>
                    <a href="{{ route('pedidos.catalogo') }}" class="btn btn-outline-primary w-100">
                        <i class="fas fa-plus me-1"></i> Ver más productos
                    </a>


                </div>
            </div>
        </div>
    </div>
</div>
@endsection
