@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">
        <i class="fas fa-shopping-cart me-2"></i>Carrito de Compras
    </h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('pedidos.catalogo') }}">Catálogo</a></li>
        <li class="breadcrumb-item active">Carrito</li>
    </ol>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(empty($carrito))
        {{-- Carrito vacío --}}
        <div class="text-center py-5">
            <i class="fas fa-shopping-cart fa-4x text-muted mb-3"></i>
            <h4 class="text-muted">Tu carrito está vacío</h4>
            <p class="text-muted">Explora nuestro catálogo y agrega productos a tu carrito.</p>
            <a href="{{ route('pedidos.catalogo') }}" class="btn btn-primary">
                <i class="fas fa-store me-2"></i>Ver Catálogo
            </a>
        </div>
    @else
        <div class="row">
            {{-- Items del carrito --}}
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-list me-2"></i>Productos en tu carrito ({{ count($carrito) }})
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        @foreach($carrito as $item)
                            <div class="border-bottom p-3">
                                <div class="row align-items-center">
                                    {{-- Imagen del producto --}}
                                    <div class="col-md-2">
                                        @php
                                            $imagenSrc = null;
                                            if ($item['fotoProducto']) {
                                                $imagenSrc = asset('storage/' . $item['fotoProducto']);
                                            } elseif ($item['archivoDiseno']) {
                                                $imagenSrc = asset('storage/' . $item['archivoDiseno']);
                                            }
                                        @endphp
                                        
                                        @if($imagenSrc)
                                            <img src="{{ $imagenSrc }}" 
                                                 class="img-fluid rounded" 
                                                 style="height: 80px; object-fit: cover;"
                                                 alt="{{ $item['nombreProducto'] }}">
                                        @else
                                            <div class="bg-light d-flex align-items-center justify-content-center rounded" 
                                                 style="height: 80px;">
                                                <i class="fas fa-image text-muted"></i>
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Información del producto --}}
                                    <div class="col-md-6">
                                        <h6 class="mb-1">{{ $item['nombreProducto'] }}</h6>
                                        <div class="text-muted small">
                                            <span class="badge bg-info me-1">{{ $item['nombreTalla'] }}</span>
                                            
                                            {{-- Características seleccionadas --}}
                                            @if(!empty($item['caracteristicas']))
                                                @foreach($item['caracteristicas'] as $opcionId => $caracteristicaId)
                                                    @php
                                                        $caracteristica = \App\Models\Caracteristica::find($caracteristicaId);
                                                    @endphp
                                                    @if($caracteristica)
                                                        <span class="badge bg-light text-dark me-1">
                                                            {{ $caracteristica->opcion->nombre }}: {{ $caracteristica->nombre }}
                                                        </span>
                                                    @endif
                                                @endforeach
                                            @endif
                                        </div>

                                        {{-- Personalización --}}
                                        @if($item['nombrePersonalizado'] || $item['numeroPersonalizado'] || $item['textoAdicional'])
                                            <div class="mt-2">
                                                <small class="text-primary">
                                                    <i class="fas fa-edit me-1"></i>Personalización:
                                                </small>
                                                <div class="small text-muted">
                                                    @if($item['nombrePersonalizado'])
                                                        <div>Nombre: {{ $item['nombrePersonalizado'] }}</div>
                                                    @endif
                                                    @if($item['numeroPersonalizado'])
                                                        <div>Número: {{ $item['numeroPersonalizado'] }}</div>
                                                    @endif
                                                    @if($item['textoAdicional'])
                                                        <div>Texto: {{ Str::limit($item['textoAdicional'], 50) }}</div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif

                                        {{-- Diseño personalizado --}}
                                        @if($item['disenoPersonalizado'])
                                            <div class="mt-2">
                                                <small class="text-success">
                                                    <i class="fas fa-upload me-1"></i>Diseño personalizado incluido
                                                </small>
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Cantidad y precio --}}
                                    <div class="col-md-3">
                                        <div class="text-center">
                                            <div class="fw-bold">Cantidad: {{ $item['cantidad'] }}</div>
                                            <div class="text-muted small">
                                                ${{ number_format($item['precioUnitario'], 0) }} c/u
                                            </div>
                                            <div class="text-success fw-bold">
                                                ${{ number_format($item['subtotal'], 0) }}
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Acciones --}}
                                    <div class="col-md-1">
                                        <form action="{{ route('pedidos.eliminar-carrito', $item['id']) }}" 
                                              method="POST" 
                                              onsubmit="return confirm('¿Eliminar este producto del carrito?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Resumen del pedido --}}
            <div class="col-lg-4">
                <div class="card sticky-top" style="top: 20px;">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-receipt me-2"></i>Resumen del Pedido
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Productos ({{ count($carrito) }}):</span>
                            <span>${{ number_format($total, 0) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Envío:</span>
                            <span class="text-muted">A coordinar</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between fw-bold h5">
                            <span>Total:</span>
                            <span class="text-success">${{ number_format($total, 0) }}</span>
                        </div>
                        
                        <div class="d-grid gap-2 mt-4">
                            <a href="{{ route('pedidos.checkout') }}" class="btn btn-success btn-lg">
                                <i class="fas fa-credit-card me-2"></i>Proceder al Checkout
                            </a>
                            <a href="{{ route('pedidos.catalogo') }}" class="btn btn-outline-primary">
                                <i class="fas fa-plus me-2"></i>Agregar más productos
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Información adicional --}}
                <div class="card mt-3">
                    <div class="card-body">
                        <h6 class="card-title">
                            <i class="fas fa-info-circle me-2"></i>Información importante
                        </h6>
                        <ul class="list-unstyled small text-muted mb-0">
                            <li class="mb-1">
                                <i class="fas fa-clock me-1"></i>
                                Tiempo de producción: 5-10 días hábiles
                            </li>
                            <li class="mb-1">
                                <i class="fas fa-truck me-1"></i>
                                Entrega a coordinar según ubicación
                            </li>
                            <li class="mb-1">
                                <i class="fas fa-edit me-1"></i>
                                Personalización incluida sin costo adicional
                            </li>
                            <li>
                                <i class="fas fa-phone me-1"></i>
                                Soporte: contacto@empresa.com
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
