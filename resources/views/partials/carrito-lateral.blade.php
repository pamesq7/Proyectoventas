<div class="offcanvas offcanvas-end" tabindex="-1" id="carritoLateral" aria-labelledby="carritoLateralLabel">
    <div class="offcanvas-header bg-light">
        <h5 class="offcanvas-title" id="carritoLateralLabel">
            <i class="fas fa-shopping-cart me-2"></i>Mi Carrito
            <span class="badge bg-primary ms-2" id="carritoCantidadBadge">
                {{ count(session('carrito', [])) }}
            </span>
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>

    {{-- CUERPO DEL CARRITO (AQUÍ INYECTAMOS EL HTML VÍA AJAX) --}}
    <div class="offcanvas-body p-0" id="carritoContenido">
        @php
            $carrito = session('carrito', []);
            $total = collect($carrito)->sum('subtotal');
        @endphp

        @include('pedidos.carrito-lateral-contenido', [
            'carrito' => $carrito,
            'total'   => $total,
        ])
    </div>

    <div class="offcanvas-footer p-3 border-top bg-light">
        <div class="d-grid gap-2">

            {{-- Botón para ir al carrito completo --}}
            <a href="{{ route('pedidos.carrito') }}" class="btn btn-primary" id="btnVerCarrito">
                <i class="fas fa-shopping-bag me-2"></i>Ver Carrito
            </a>

            {{-- Botón para vaciar el carrito --}}
            <button type="button" class="btn btn-outline-danger" id="btnVaciarCarrito">
                <i class="fas fa-trash-alt me-2"></i>Vaciar Carrito
            </button>

            {{-- Botón para cerrar y seguir comprando --}}
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="offcanvas">
                <i class="fas fa-arrow-left me-2"></i>Seguir Comprando
            </button>
        </div>
    </div>
</div>
