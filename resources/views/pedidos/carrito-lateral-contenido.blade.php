@if(empty($carrito))
<div class="p-4 text-center text-muted">
    <i class="fas fa-shopping-cart fa-2x mb-2"></i>
    <p>Tu carrito está vacío</p>
</div>
@else
@php
// Agrupar productos por idProducto
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

<ul class="list-group list-group-flush">
    @foreach($productosAgrupados as $index => $producto)
    <li class="list-group-item">
        <div class="d-flex">
            {{-- IMAGEN --}}
            <div class="me-3">
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
                    style="width: 60px; height: 60px; object-fit: cover;">
                @else
                <div class="bg-light d-flex align-items-center justify-content-center rounded"
                    style="width: 60px; height: 60px;">
                    <i class="fas fa-image text-muted"></i>
                </div>
                @endif
            </div>

            {{-- INFO + ACCIONES --}}
            <div class="flex-grow-1 d-flex justify-content-between">
                <div>
                    <strong>{{ $producto['producto'] }}</strong><br>
                    <small class="text-muted">
                        {{ $producto['cantidad_total'] }} unidad(es) |
                        Precio unitario: Bs {{ number_format($producto['precioUnitario'], 2) }}
                    </small><br>
                    
                    {{-- Detalles de tallas (colapsable) --}}
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
                        <div class="small text-muted">
                            @foreach($producto['items'] as $detalle)
                            @php
                                $precioUnit = (float)($detalle['precioUnitario'] ?? 0);
                                $subDet     = (float)($detalle['subtotal'] ?? 0);
                                $cantDet    = (int)($detalle['cantidad'] ?? 0);

                                // Si por algún bug la cantidad viene 0 pero hay subtotal y precio unitario,
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
                                        @if($detalle['numeroPersonalizado'])- Nº: {{ $detalle['numeroPersonalizado'] }}@endif
                                    </small>
                                    @endif
                                </span>
                                <span>{{ $cantDet }} × Bs {{ number_format($precioUnit, 2) }} = Bs {{ number_format($subDet, 2) }}</span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>

                <div class="text-end">
                    <span class="fw-bold d-block text-primary">
                        Bs {{ number_format($producto['subtotal'], 2) }}
                    </span>
                    <small class="text-muted d-block">Subtotal</small>
                    <button
                        class="btn btn-sm btn-outline-danger mt-2 btnEliminarProductoCarrito"
                        data-idproducto="{{ $producto['idProducto'] }}">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
    </li>
    @endforeach
</ul>

<div class="p-3 border-top bg-light">
    <div class="d-flex justify-content-between fw-bold mb-2">
        <span>Subtotal:</span>
        <span>Bs {{ number_format($total, 2) }}</span>
    </div>
    <div class="d-flex justify-content-between fw-bold text-primary fs-5">
        <span>Total:</span>
        <span>Bs {{ number_format($total, 2) }}</span>
    </div>
</div>
@endif