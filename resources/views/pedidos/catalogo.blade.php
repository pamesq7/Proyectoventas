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
        <!-- 
<div class="d-flex gap-2">
    <a href="{{ route('pedidos.personalizar') }}" class="btn btn-success">
        <i class="fas fa-wand-magic-sparkles me-2"></i>Hacer mi pedido personalizado
    </a>
</div>
-->
    </div>

    <ol class="breadcrumb mb-4 mt-3">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Catálogo</li>
    </ol>

    {{-- ====
         1) Datos base (unificación)
       ==== --}}
    @php
    use Illuminate\Support\Str;

    // URL fija para "Nuevo Pedido" (modelos rápidos)
    $toNuevo = route('pedidos.nuevoPersonalizado');




    // Catálogo estático de 6 productos (mock) - Comentado para ocultar modelos rápidos
    $quickProducts = [];
    /*
    [
    'key' => 'polera',
    'title' => 'Polera',
    'subtitle' => 'Full sublimado / tela Drift',
    'price' => 85,
    'cost' => 70,
    'badge' => 'Top',
    'img' => asset('img/mock/polera.png'),
    'url' => route('pedidos.nuevoPersonalizado', ['tipo' => 'polera'])
    ],
    [
    'key' => 'conjunto_pyc',
    'title' => 'Conjunto (Polera + Corto)',
    'subtitle' => 'Match de equipo completo',
    'price' => 150,
    'cost' => 120,
    'badge' => 'Pack',
    'img' => asset('img/mock/conjunto-pyc.png'),
    'url' => route('pedidos.nuevoPersonalizado', ['tipo' => 'conjunto_pyc'])
    ],
    [
    'key' => 'chamarra',
    'title' => 'Chamarra',
    'subtitle' => 'Cuello alto, tela Drift',
    'price' => 120,
    'cost' => 95,
    'badge' => 'Outer',
    'img' => asset('img/mock/chamarra.png'),
    'url' => route('pedidos.nuevoPersonalizado', ['tipo' => 'chamarra'])
    ],
    [
    'key' => 'conjunto_cb',
    'title' => 'Conjunto (Chamarra + Buzo)',
    'subtitle' => 'Combo de abrigo',
    'price' => 210,
    'cost' => 170,
    'badge' => 'Pack',
    'img' => asset('img/mock/conjunto-cb.png'),
    'url' => route('pedidos.nuevoPersonalizado', ['tipo' => 'conjunto_cb'])
    ]
    ];
    */

    // Unificamos TODO en $items para un solo grid
    $items = [];

    // 1) Rápidos -> tipo quick
    foreach ($quickProducts as $p) {
    $items[] = (object)[
    'tipo' => 'quick',
    'key' => $p['key'],
    'id' => null,
    'nombre' => $p['title'],
    'descripcion' => $p['subtitle'],
    'precioVenta' => $p['price'],
    'precioProduccion' => $p['cost'],
    'img' => $p['img'],
    'badge' => $p['badge'],
    'categoria_key' => 'quick',
    'categoria_label' => 'Modelos rápidos',
    'config_url' => $p['url'], // Usamos la URL específica de cada producto
    ];
    }

    // 2) BD -> tipo db
    foreach ($productos as $producto) {
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

    $items[] = (object)[
    'tipo' => 'db',
    'key' => 'db-' . $producto->idProducto,
    'id' => $producto->idProducto,
    'nombre' => $producto->nombre,
    'descripcion' => $producto->descripcion,
    'precioVenta' => $producto->precioVenta,
    'precioProduccion' => $producto->precioProduccion,
    'img' => $imagenSrc,
    'badge' => optional($producto->variante)->nombre, // se muestra como badge (si aplica)
    'categoria_key' => (string)$producto->idCategoria,
    'categoria_label' => optional($producto->categoria)->nombreCategoria,
    'config_url' => route('pedidos.configurar', $producto->idProducto),
    ];
    }

    // Construimos botones de filtro: 'Todas' + 'Modelos rápidos' + categorías únicas de BD
    $filtros = collect($items)
    ->map(fn($it) => [$it->categoria_key, $it->categoria_label])
    ->unique(function($par){ return $par[0]; })
    ->values();
    @endphp

    {{-- ====
         2) Filtros + búsqueda (unificados)
       ==== --}}
    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-center">
                <div class="col-12 col-lg-6">
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <span class="fw-semibold me-1">Categorías:</span>
                        <button type="button" class="btn btn-sm btn-outline-primary active rounded-pill" data-filter-categoria="all">
                            Todas
                        </button>
                        {{-- Botón especial para "Modelos rápidos" - Ocultado
                        @if(collect($items)->contains(fn($i) => $i->categoria_key === 'quick'))
                        <button type="button" class="btn btn-sm btn-outline-primary rounded-pill" data-filter-categoria="quick">
                            Modelos rápidos
                        </button>
                        @endif
                        --}}
                        {{-- Resto de categorías BD --}}
                        @foreach($filtros as [$ckey, $clabel])
                        @if($ckey !== 'quick' && $ckey !== '')
                        <button type="button" class="btn btn-sm btn-outline-primary rounded-pill" data-filter-categoria="{{ $ckey }}">
                            {{ $clabel ?? 'Categoría' }}
                        </button>
                        @endif
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

    {{-- ====
         3) Grid único (todo junto)
       ==== --}}
    <div class="row g-3" id="gridUnico">
        @foreach($items as $it)
        <div class="col-12 col-sm-6 col-md-4 col-lg-3 item-card"
            data-categoria="{{ $it->categoria_key }}"
            data-nombre="{{ Str::lower($it->nombre) }}"
            data-tipo="{{ $it->tipo }}">
            <div class="card h-100 shadow-sm border-0 rounded-4 card-uniforme">
                {{-- Marco de imagen uniforme --}}
                <div class="media-uniforme">
                    @if($it->img)
                    <img src="{{ $it->img }}" alt="{{ $it->nombre }}" class="img-uniforme" loading="lazy">
                    @else
                    <div class="img-fallback"><i class="fas fa-image"></i></div>
                    @endif

                    {{-- Badge de categoría o variante --}}
                    @if($it->categoria_label)
                    <span class="badge badge-corner rounded-pill bg-white text-dark">
                        {{ $it->categoria_label }}
                    </span>
                    @endif
                </div>

                <div class="p-3 d-flex flex-column text-center"> <!-- Agregada clase text-center -->
    <h6 class="mb-1" title="{{ $it->nombre }}">{{ $it->nombre }}</h6>

    @if($it->descripcion)
    <p class="text-muted small mb-2">{{ Str::limit($it->descripcion, 90) }}</p>
    @endif

    <div class="mb-3">
        @if(!is_null($it->precioVenta))
        <span class="h6 text-success">Bs{{ number_format($it->precioVenta, 0) }}</span>
        @endif
    </div>

    <div class="mt-auto d-grid gap-2">
        <a href="{{ $it->config_url }}" class="btn btn-primary">
            <i class="fas fa-sliders me-2"></i>Configurar y pedir
        </a>
        <button type="button" class="btn btn-outline-secondary btn-sm"
            data-bs-toggle="modal" data-bs-target="#modal-{{ $it->key }}">
            <i class="fas fa-circle-info me-1"></i>Ver detalles
        </button>
    </div>
</div>
            </div>
        </div>

        {{-- Modal de detalles (mejorado) --}}
        <div class="modal fade" id="modal-{{ $it->key }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content border-0 shadow-lg rounded-4">
                    <div class="modal-header bg-light border-0">
                        <h5 class="modal-title fw-bold">{{ $it->nombre }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>

                    <div class="modal-body">
                        <div class="row g-4 align-items-start">
                            {{-- Imagen --}}
                            <div class="col-md-6">
                                <div class="media-uniforme mb-0">
                                    @if($it->img)
                                    <img src="{{ $it->img }}" alt="{{ $it->nombre }}" class="img-uniforme rounded">
                                    @else
                                    <div class="img-fallback"><i class="fas fa-image fa-3x"></i></div>
                                    @endif
                                </div>
                            </div>

                            {{-- Datos principales --}}
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <div class="d-flex align-items-baseline gap-2 mb-1">
                                        @if(!is_null($it->precioVenta))
                                        <span class="h4 text-success mb-0">Bs{{ number_format($it->precioVenta, 0) }}</span>
                                        @endif
                                        @if(!is_null($it->precioProduccion))
                                        <small class="text-muted">Costo: Bs{{ number_format($it->precioProduccion, 0) }}</small>
                                        @endif
                                    </div>
                                    @if($it->categoria_label)
                                    <span class="badge bg-primary-subtle text-dark border">
                                        {{ $it->categoria_label }}
                                    </span>
                                    @endif
                                    @if($it->tipo === 'quick')
                                    <span class="badge bg-warning-subtle text-dark border ms-1">
                                        Modelo rápido
                                    </span>
                                    @endif
                                </div>

                                @if($it->descripcion)
                                <p class="mb-2"><strong>Descripción</strong></p>
                                <p class="text-muted small mb-3">{{ $it->descripcion }}</p>
                                @endif

                                {{-- Info técnica según tipo --}}
                                @if($it->tipo === 'quick')
                                <ul class="small text-muted mb-3">
                                    <li>Mockup plano (frente y dorso en la misma lámina)</li>
                                    <li>Listo para personalizar tallas, nombre y número</li>
                                    <li>Entrega y seguimiento desde el módulo de pedidos</li>
                                    <li>Ideal para equipos, academias o clubes</li>
                                </ul>
                                @else
                                <ul class="small text-muted mb-3">
                                    <li>SKU: {{ $producto->SKU ?? 'N/A' }}</li>
                                    @if($producto->variante)
                                    <li>Variante: {{ $producto->variante->nombre }}</li>
                                    @endif
                                    @if($producto->categoria)
                                    <li>Categoría: {{ $producto->categoria->nombreCategoria }}</li>
                                    @endif
                                    <li>Pedido mínimo: {{ $producto->pedidoMinimo ?? 1 }} unidades</li>
                                    <li>Creado el: {{ $producto->created_at?->format('d/m/Y') ?? '—' }}</li>
                                </ul>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer bg-light border-0">
                        <a href="{{ $it->config_url }}" class="btn btn-primary">
                            <i class="fas fa-sliders me-2"></i>Configurar y pedir
                        </a>
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-xmark me-2"></i>Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        @endforeach
    </div>

    {{-- Si no hay nada --}}
    @if(count($items) === 0)
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
    /* ===== Tarjeta uniforme para TODO el grid ===== */
    .card-uniforme {
        border-radius: 16px;
        transition: transform .15s ease, box-shadow .15s ease;
        background: #fff;
    }

    .card-uniforme:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.75rem 1.25rem rgba(0, 0, 0, .08);
    }

    /* ===== Marco de imagen idéntico para todas ===== */
    .media-uniforme {
        position: relative;
        aspect-ratio: 4 / 3;
        /* misma proporción para todas */
        background: linear-gradient(180deg, #fafafa 0%, #f4f4f5 100%);
        border-top-left-radius: 16px;
        border-top-right-radius: 16px;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 18px;
        border-bottom: 1px solid #eee;
    }

    /* Imagen sin deformarse y con “lámina” blanca + sombra suave */
    .img-uniforme {
        width: 100%;
        height: 100%;
        object-fit: contain;
        /* no recorta mockups */
        background: #fff;
        border-radius: 8px;
        filter: drop-shadow(0 6px 14px rgba(0, 0, 0, .08));
    }

    /* Fallback de imagen */
    .img-fallback {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #bdbdbd;
        font-size: 42px;
    }

    /* Badge arriba a la derecha (categoría o “Modelos rápidos”) */
    .badge-corner {
        position: absolute;
        top: 10px;
        right: 10px;
        border: 1px solid rgba(0, 0, 0, .08);
        box-shadow: 0 2px 6px rgba(0, 0, 0, .06);
    }

    /* Responsive: caja un poco más baja en móviles */
    @media (max-width: 575.98px) {
        .media-uniforme {
            aspect-ratio: 16/11;
            padding: 14px;
        }
    }

    /* Botón outline suave */
    .btn-outline-secondary {
        border-color: #e5e7eb;
    }

    .btn-outline-secondary:hover {
        background: #f3f4f6;
        border-color: #e5e7eb;
    }

    /* Filtro activo */
    .btn-outline-primary.active {
        background-color: #0d6efd;
        color: #fff;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const botonesCategorias = document.querySelectorAll('[data-filter-categoria]');
        const cards = document.querySelectorAll('#gridUnico .item-card');
        const buscarInput = document.getElementById('buscarInput');

        function aplicaFiltros() {
            const activeBtn = document.querySelector('[data-filter-categoria].active');
            const catSel = activeBtn ? activeBtn.dataset.filterCategoria : 'all';
            const q = (buscarInput?.value || '').trim().toLowerCase();

            cards.forEach(card => {
                const cat = (card.dataset.categoria || '').toLowerCase();
                const nombre = (card.dataset.nombre || '').toLowerCase();
                const passCat = (catSel === 'all') || (cat === String(catSel).toLowerCase());
                const passSearch = !q || nombre.includes(q);
                card.style.display = (passCat && passSearch) ? '' : 'none';
            });
        }

        botonesCategorias.forEach(btn => {
            btn.addEventListener('click', function() {
                botonesCategorias.forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                aplicaFiltros();
            });
        });

        if (buscarInput) {
            buscarInput.addEventListener('input', aplicaFiltros);
        }
    });
</script>
@endpush

@push('styles')
<style>
    /* ===== Uniformar tarjetas (rápidos + BD) ===== */

    /* 1) Altura y estructura vertical idéntica en ambas familias de tarjetas */
    .product-card .card,
    .quick-card2 {
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    /* 2) Cuerpo como columna flexible (el bloque de botones se ancla abajo) */
    .product-card .card-body,
    .quick-card2 .p-3 {
        display: flex;
        flex-direction: column;
        flex: 1 1 auto;
    }

    /* 3) Bloque de botones siempre al fondo (ya tenés mt-auto en el HTML) */
    .product-card .card-body .mt-auto.d-grid,
    .quick-card2 .p-3 .mt-auto.d-grid {
        margin-top: auto;
    }

    /* 4) Altura mínima del contenido ANTES de los botones (nombre + desc + precio)
      Ajustá este valor si necesitás más/menos “aire” de texto. */
    .product-card .card-body {
        min-height: 180px;
    }

    .quick-card2 .p-3 {
        min-height: 180px;
    }

    /* 5) Imagen con altura uniforme en TODAS las tarjetas */
    :root {
        --card-img-h: 220px;
    }

    /* Cambiá SOLO este valor para todas */

    .product-img {
        /* BD */
        height: var(--card-img-h);
        object-fit: cover;
        border-top-left-radius: 16px;
        border-top-right-radius: 16px;
    }

    .quick-media2 {
        /* Rápidos */
        position: relative;
        height: var(--card-img-h);
        background: linear-gradient(180deg, #fafafa 0%, #f4f4f5 100%);
        border-top-left-radius: 16px;
        border-top-right-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 14px;
        overflow: hidden;
        border-bottom: 1px solid #eee;
    }

    .quick-img2 {
        width: 100%;
        height: 100%;
        object-fit: contain;
        /* se ve completa, sin recorte */
        background: #fff;
        border-radius: 8px;
        filter: drop-shadow(0 6px 14px rgba(0, 0, 0, .08));
    }

    /* 6) Botones con la misma altura y tipografía consistente */
    .product-card .btn,
    .quick-card2 .btn {
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: .95rem;
    }

    /* 7) Pequeñas mejoras visuales */
    .product-card .card,
    .quick-card2 {
        border-radius: 16px;
        transition: transform .15s ease, box-shadow .15s ease;
    }

    .product-card .card:hover,
    .quick-card2:hover {
        transform: translateY(-2px);
        box-shadow: 0 .9rem 1.25rem rgba(0, 0, 0, .08);
    }

    /* 8) Responsive: reducimos un poco la altura en móviles */
    @media (max-width: 576px) {
        :root {
            --card-img-h: 200px;
        }

        .product-card .card-body,
        .quick-card2 .p-3 {
            min-height: 160px;
        }
    }
</style>
@endpush
@push('styles')
<style>
    /* ===== Corrección: uniformar altura de las tarjetas ===== */

    /* Hace que todas las tarjetas tengan la misma estructura vertical */
    .card-uniforme {
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    /* El cuerpo (texto y botones) ocupa todo el espacio disponible */
    .card-uniforme .p-3 {
        display: flex;
        flex-direction: column;
        flex-grow: 1;
        /* hace que todo crezca dentro del alto */
    }

    /* Sección del contenido superior (nombre, descripción, precios) */
    .card-uniforme .p-3>h6,
    .card-uniforme .p-3>p,
    .card-uniforme .p-3>div {
        flex-shrink: 0;
        /* evita que se compriman */
    }

    /* Bloque de botones SIEMPRE abajo */
    .card-uniforme .d-grid.gap-2 {
        margin-top: auto;
        /* empuja hacia abajo */
    }

    /* Altura mínima consistente para la parte de texto antes de los botones */
    .card-uniforme .p-3 {
        min-height: 180px;
        /* ajustá según la cantidad de texto promedio */
    }

    /* Uniformar tamaño de botones */
    .card-uniforme .btn {
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.9rem;
    }

    /* Responsive: si las tarjetas son más pequeñas en móviles */
    @media (max-width: 576px) {
        .card-uniforme .p-3 {
            min-height: 160px;
        }
    }
</style>
@endpush