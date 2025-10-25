@extends('layouts.app')

@section('title', 'Detalle del Pedido #' . $venta->idVenta)

@section('content')
<div class="container-fluid px-4">
    <!-- ENCABEZADO MEJORADO -->
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="mt-4">
                        <i class="fas fa-shopping-bag me-2 text-primary"></i>
                        Pedido #{{ $venta->idVenta }}
                    </h1>
                    <div class="d-flex flex-wrap gap-3 text-muted">
                        <span><i class="fas fa-calendar me-1"></i> {{ $venta->created_at->format('d/m/Y H:i') }}</span>
                        <span><i class="fas fa-user me-1"></i> {{ $venta->nombre_cliente ?? 'Cliente' }}</span>
                        <span><i class="fas fa-tag me-1"></i> {{ $venta->tipo_cliente ?? 'Tipo no especificado' }}</span>
                    </div>
                </div>
                <div class="text-end">
                    <a href="{{ route('rolCliente.historial') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Volver al Historial
                    </a>
                </div>
            </div>

            <!-- ALERTA DE SEGURIDAD -->
            <div class="alert alert-success mb-4 d-flex align-items-center" role="alert">
                <i class="fas fa-shield-alt me-3 fs-4 text-success"></i>
                <div>
                    <strong>Modo seguro activado:</strong> Solo puedes ver detalles de tus propios pedidos.
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- COLUMNA PRINCIPAL (8) -->
        <div class="col-lg-8">
            <!-- TARJETA DE ESTADO Y PROGRESO -->
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-tasks me-2"></i>
                        Progreso del Pedido
                    </h5>
                </div>
                <div class="card-body">
                    <!-- TIMELINE VISUAL -->
                    <div class="progress-container mb-4">
                        @php
                        $estados = [
                            0 => ['nombre' => 'Pendiente', 'icon' => 'clock', 'color' => 'warning'],
                            1 => ['nombre' => 'En proceso', 'icon' => 'cogs', 'color' => 'info'],
                            2 => ['nombre' => 'Completado', 'icon' => 'check-circle', 'color' => 'success'],
                            3 => ['nombre' => 'Entregado', 'icon' => 'truck', 'color' => 'primary']
                        ];
                        @endphp

                        <div class="d-flex justify-content-between position-relative">
                            <div class="progress-bar-connector"></div>
                            @foreach($estados as $key => $estado)
                            <div class="text-center flex-fill">
                                <div class="progress-step {{ $venta->estadoPedido >= $key ? 'active' : '' }}">
                                    <div class="step-icon bg-{{ $estado['color'] }} {{ $venta->estadoPedido >= $key ? 'active' : '' }}">
                                        <i class="fas fa-{{ $estado['icon'] }}"></i>
                                    </div>
                                    <div class="step-label mt-2">
                                        <small class="d-block fw-bold">{{ $estado['nombre'] }}</small>
                                        @if($venta->estadoPedido == $key)
                                        <span class="badge bg-{{ $estado['color'] }}">Actual</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- INFORMACIÓN DE ENTREGA -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-item">
                                <label class="text-muted small">Fecha de Entrega:</label>
                                <div class="fw-bold">
                                    @if($venta->fechaEntrega)
                                    {{ $venta->fechaEntrega->format('d/m/Y') }}
                                    @else
                                    <span class="text-muted">Por definir</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <label class="text-muted small">Lugar de Entrega:</label>
                                <div class="fw-bold">{{ $venta->lugarEntrega ?? 'Por definir' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- GALERÍA DE IMÁGENES MEJORADA - VERSIÓN CORREGIDA -->
            @foreach($venta->detalleVentas as $detalle)
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-images me-2"></i>
                        Galería - {{ $detalle->producto->nombre ?? 'Producto' }}
                    </h5>
                </div>
                <div class="card-body">
                    @php
                    // ✅ CORRECCIÓN: Recopilar todas las imágenes disponibles para este detalle
                    $imagenes = [];
                    
                    // Prioridad 1: Imágenes de diseños personalizados
                    if($detalle->diseno && $detalle->diseno->archivo) {
                        $imagenes[] = [
                            'src' => asset('storage/' . $detalle->diseno->archivo),
                            'alt' => 'Diseño personalizado',
                            'tipo' => 'diseño',
                            'titulo' => 'Diseño Personalizado',
                            'descripcion' => $detalle->diseno->comentario ?? 'Diseño del pedido'
                        ];
                    }
                    
                    // Prioridad 2: Imagen del producto base
                    if($detalle->producto && $detalle->producto->foto) {
                        $imagenes[] = [
                            'src' => asset('storage/' . $detalle->producto->foto),
                            'alt' => $detalle->producto->nombre ?? 'Producto',
                            'tipo' => 'producto',
                            'titulo' => 'Producto Base',
                            'descripcion' => $detalle->producto->SKU ? 'SKU: ' . $detalle->producto->SKU : 'Producto de referencia'
                        ];
                    }
                    
                    $tieneImagenes = count($imagenes) > 0;
                    @endphp

                    @if($tieneImagenes)
                        <!-- GALERÍA CON MÚLTIPLES IMÁGENES -->
                        <div class="row">
                            <!-- IMAGEN PRINCIPAL GRANDE -->
                            <div class="col-lg-8 mb-3">
                                <div class="image-main-container text-center">
                                    <img id="imagen-principal-{{ $detalle->iddetalleVenta ?? $loop->index }}" 
                                         src="{{ $imagenes[0]['src'] }}" 
                                         alt="{{ $imagenes[0]['alt'] }}"
                                         class="img-fluid rounded shadow main-image"
                                         style="max-height: 400px; object-fit: contain; cursor: pointer;"
                                         onclick="ampliarImagen(this.src)">
                                    <div class="mt-2">
                                        <strong>{{ $imagenes[0]['titulo'] }}</strong>
                                        @if($imagenes[0]['descripcion'])
                                        <br><small class="text-muted">{{ $imagenes[0]['descripcion'] }}</small>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- MINIATURAS LATERALES -->
                            <div class="col-lg-4">
                                <div class="thumbnails-container">
                                    <h6 class="text-muted mb-3">Vistas disponibles:</h6>
                                    <div class="d-flex flex-lg-column gap-2 overflow-auto" style="max-height: 400px;">
                                        @foreach($imagenes as $index => $imagen)
                                        <div class="thumbnail-item">
                                            <img src="{{ $imagen['src'] }}" 
                                                 alt="{{ $imagen['alt'] }}"
                                                 class="img-thumbnail thumbnail-img {{ $index === 0 ? 'active' : '' }}"
                                                 style="width: 80px; height: 80px; object-fit: cover; cursor: pointer; border: 2px solid {{ $index === 0 ? '#007bff' : '#dee2e6' }};"
                                                 onclick="cambiarImagenPrincipal('{{ $detalle->iddetalleVenta ?? $loop->index }}', '{{ $imagen['src'] }}', '{{ $imagen['alt'] }}', '{{ $imagen['tipo'] }}', '{{ $imagen['descripcion'] }}', this)">
                                            <div class="thumbnail-caption mt-1">
                                                <small class="text-muted d-block">{{ $imagen['titulo'] }}</small>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <!-- PLACEHOLDER CUANDO NO HAY IMÁGENES -->
                        <div class="text-center py-4">
                            <div class="no-image-placeholder bg-light rounded p-5">
                                <i class="fas fa-image fa-3x text-muted mb-3"></i>
                                <p class="text-muted mb-0">No hay imágenes disponibles para este producto</p>
                                <small class="text-muted">El diseño y producto no tienen imágenes asociadas</small>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- ESPECIFICACIONES TÉCNICAS DEL PRODUCTO -->
            <div class="card mb-4">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-cogs me-2"></i>
                        Especificaciones Técnicas - {{ $detalle->producto->nombre ?? 'Producto' }}
                    </h5>
                    <span class="badge bg-info">Detalles del Producto</span>
                </div>
                <div class="card-body">
                    <div class="product-specs">
                        <div class="row">
                            <!-- INFORMACIÓN BÁSICA -->
                            <div class="col-md-6">
                                <h6 class="text-primary mb-3">
                                    <i class="fas fa-cube me-2"></i>
                                    Información del Producto
                                </h6>

                                <div class="specs-grid">
                                    @if($detalle->producto && $detalle->producto->variante)
                                    <div class="spec-item">
                                        <span class="spec-label">Variante:</span>
                                        <span class="spec-value badge bg-secondary">
                                            {{ $detalle->producto->variante->nombre }}
                                        </span>
                                    </div>
                                    @endif

                                    @if($detalle->talla)
                                    <div class="spec-item">
                                        <span class="spec-label">Talla:</span>
                                        <span class="spec-value badge bg-dark">
                                            {{ $detalle->talla->nombre }}
                                        </span>
                                    </div>
                                    @endif

                                    <div class="spec-item">
                                        <span class="spec-label">Cantidad:</span>
                                        <span class="spec-value fw-bold">{{ $detalle->cantidad }}</span>
                                    </div>

                                    <div class="spec-item">
                                        <span class="spec-label">Precio Unitario:</span>
                                        <span class="spec-value text-success fw-bold">
                                            ${{ number_format($detalle->precioUnitario, 2) }}
                                        </span>
                                    </div>

                                    @if($detalle->producto && $detalle->producto->descripcion)
                                    <div class="spec-item">
                                        <span class="spec-label">Descripción:</span>
                                        <span class="spec-value small">{{ $detalle->producto->descripcion }}</span>
                                    </div>
                                    @endif
                                </div>
                            </div>

                            <!-- PERSONALIZACIÓN -->
                            <div class="col-md-6">
                                <h6 class="text-primary mb-3">
                                    <i class="fas fa-list-alt me-2"></i>
                                    Personalización
                                </h6>

                                @if($detalle->nombrePersonalizado)
                                <div class="spec-item mb-2">
                                    <span class="spec-label">Nombre Personalizado:</span>
                                    <span class="spec-value badge bg-info">
                                        <i class="fas fa-font me-1"></i>
                                        {{ $detalle->nombrePersonalizado }}
                                    </span>
                                </div>
                                @endif

                                @if($detalle->numeroPersonalizado)
                                <div class="spec-item mb-2">
                                    <span class="spec-label">Número Personalizado:</span>
                                    <span class="spec-value badge bg-warning text-dark">
                                        <i class="fas fa-hashtag me-1"></i>
                                        {{ $detalle->numeroPersonalizado }}
                                    </span>
                                </div>
                                @endif

                                @if($detalle->textoAdicional)
                                <div class="spec-item mb-2">
                                    <span class="spec-label">Texto Adicional:</span>
                                    <span class="spec-value small">{{ $detalle->textoAdicional }}</span>
                                </div>
                                @endif

                                @if($detalle->observacion)
                                <div class="spec-item">
                                    <span class="spec-label">Observaciones:</span>
                                    <span class="spec-value small text-muted">{{ $detalle->observacion }}</span>
                                </div>
                                @endif

                                <!-- ESTADO DEL DETALLE -->
                                <div class="spec-item mt-3">
                                    <span class="spec-label">Estado del Item:</span>
                                    <span class="spec-value">
                                        @if($detalle->estado == 1)
                                        <span class="badge bg-success">Activo</span>
                                        @else
                                        <span class="badge bg-danger">Inactivo</span>
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- OPCIONES CONFIGURADAS -->
                        @if($detalle->producto && $detalle->producto->opciones && $detalle->producto->opciones->count() > 0)
                        <div class="mt-4 pt-3 border-top">
                            <h6 class="text-primary mb-3">
                                <i class="fas fa-sliders-h me-2"></i>
                                Opciones Configuradas
                            </h6>
                            <div class="row g-3">
                                @foreach($detalle->producto->opciones as $opcion)
                                <div class="col-md-6 col-lg-4">
                                    <div class="option-card p-3 border rounded bg-light">
                                        <div class="option-header mb-2">
                                            <strong class="text-primary">{{ $opcion->nombre }}</strong>
                                        </div>
                                        <div class="option-body">
                                            <small class="text-muted d-block">Descripción:</small>
                                            <span class="fw-bold">{{ $opcion->descripcion ?? 'Sin descripción' }}</span>
                                        </div>
                                        @if($opcion->caracteristicas && $opcion->caracteristicas->count() > 0)
                                        <div class="option-footer mt-2">
                                            <small class="text-muted d-block">Características disponibles:</small>
                                            <div class="d-flex flex-wrap gap-1 mt-1">
                                                @foreach($opcion->caracteristicas as $caracteristica)
                                                <span class="badge bg-secondary badge-sm">{{ $caracteristica->nombre }}</span>
                                                @endforeach
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- COLUMNA LATERAL (4) -->
        <div class="col-lg-4">
            <!-- RESUMEN FINANCIERO MEJORADO -->
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-money-bill-wave me-2"></i>
                        Resumen Financiero
                    </h5>
                </div>
                <div class="card-body">
                    <div class="finance-summary">
                        <div class="finance-item d-flex justify-content-between align-items-center mb-3 p-3 bg-light rounded">
                            <span><i class="fas fa-receipt me-2"></i>Total del Pedido:</span>
                            <strong class="fs-5 text-dark">${{ number_format($venta->total, 2) }}</strong>
                        </div>

                        @if($venta->saldo > 0)
                        <div class="finance-item d-flex justify-content-between align-items-center mb-2 p-2 border rounded">
                            <span class="text-danger">
                                <i class="fas fa-clock me-1"></i>Saldo Pendiente:
                            </span>
                            <strong class="text-danger">${{ number_format($venta->saldo, 2) }}</strong>
                        </div>
                        @endif

                        <div class="finance-item d-flex justify-content-between align-items-center mb-2 p-2 border rounded">
                            <span class="text-success">
                                <i class="fas fa-check-circle me-1"></i>Total Pagado:
                            </span>
                            <strong class="text-success">${{ number_format($venta->monto_pagado ?? ($venta->total - $venta->saldo), 2) }}</strong>
                        </div>

                        <!-- BARRA DE PROGRESO DE PAGO -->
                        @php
                        $porcentajePagado = $venta->total > 0 ? (($venta->total - $venta->saldo) / $venta->total) * 100 : 0;
                        @endphp
                        <div class="mt-3">
                            <div class="d-flex justify-content-between mb-1">
                                <small>Progreso de Pago</small>
                                <small>{{ number_format($porcentajePagado, 1) }}%</small>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-success" 
                                     role="progressbar" 
                                     style="width: {{ $porcentajePagado }}%"
                                     aria-valuenow="{{ $porcentajePagado }}" 
                                     aria-valuemin="0" 
                                     aria-valuemax="100">
                                </div>
                            </div>
                        </div>

                        @if($venta->saldo > 0)
                        <div class="mt-3 text-center">
                            <button class="btn btn-success btn-sm w-100">
                                <i class="fas fa-credit-card me-1"></i>Pagar Saldo
                            </button>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- INFORMACIÓN DEL EQUIPO -->
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-users me-2"></i>
                        Equipo Asignado
                    </h5>
                </div>
                <div class="card-body">
                    @if($venta->empleado)
                    <div class="team-member mb-3">
                        <div class="d-flex align-items-center">
                            <div class="member-avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3"
                                style="width: 40px; height: 40px;">
                                <i class="fas fa-user"></i>
                            </div>
                            <div>
                                <div class="fw-bold">{{ $venta->empleado->user->name ?? 'Vendedor' }}</div>
                                <small class="text-muted">Vendedor asignado</small>
                            </div>
                        </div>
                    </div>
                    @endif

                    @php
                    $disenador = null;
                    foreach($venta->detalleVentas as $detalle) {
                        if($detalle->diseno && $detalle->diseno->empleado) {
                            $disenador = $detalle->diseno->empleado;
                            break;
                        }
                    }
                    @endphp

                    @if($disenador)
                    <div class="team-member">
                        <div class="d-flex align-items-center">
                            <div class="member-avatar bg-info text-white rounded-circle d-flex align-items-center justify-content-center me-3"
                                style="width: 40px; height: 40px;">
                                <i class="fas fa-palette"></i>
                            </div>
                            <div>
                                <div class="fw-bold">{{ $disenador->user->name ?? 'Diseñador' }}</div>
                                <small class="text-muted">Diseñador asignado</small>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if(!$venta->empleado && !$disenador)
                    <div class="text-center text-muted py-2">
                        <i class="fas fa-info-circle me-1"></i>
                        Equipo por asignar
                    </div>
                    @endif
                </div>
            </div>

            <!-- HISTORIAL DE PAGOS MEJORADO -->
            @if($venta->transacciones && $venta->transacciones->count() > 0)
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-receipt me-2"></i>
                        Historial de Pagos
                        <span class="badge bg-primary ms-2">{{ $venta->transacciones->count() }}</span>
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @foreach($venta->transacciones as $transaccion)
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <div class="fw-bold">${{ number_format($transaccion->monto, 2) }}</div>
                                    <small class="text-muted">{{ $transaccion->created_at->format('d/m/Y H:i') }}</small>
                                </div>
                                <div class="text-end">
                                    @if($transaccion->metodoPago == 'efectivo')
                                    <span class="badge bg-success">Efectivo</span>
                                    @elseif($transaccion->metodoPago == 'transferencia')
                                    <span class="badge bg-info">Transferencia</span>
                                    @elseif($transaccion->metodoPago == 'qr')
                                    <span class="badge bg-primary">QR</span>
                                    @else
                                    <span class="badge bg-secondary">{{ ucfirst($transaccion->metodoPago) }}</span>
                                    @endif
                                </div>
                            </div>
                            @if($transaccion->observaciones)
                            <div class="mt-1">
                                <small class="text-muted">{{ $transaccion->observaciones }}</small>
                            </div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- MODAL PARA AMPLIAR IMAGEN -->
<div class="modal fade" id="imageModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Vista Ampliada</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img id="modalImage" src="" class="img-fluid" style="max-height: 70vh; object-fit: contain;">
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* ESTILOS PARA EL TIMELINE */
    .progress-container {
        position: relative;
    }
    .progress-bar-connector {
        position: absolute;
        top: 20px;
        left: 10%;
        right: 10%;
        height: 3px;
        background-color: #e9ecef;
        z-index: 1;
    }
    .progress-step {
        position: relative;
        z-index: 2;
    }
    .step-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
        border: 3px solid #e9ecef;
        background-color: #f8f9fa;
        transition: all 0.3s ease;
    }
    .step-icon.active {
        border-color: currentColor;
        transform: scale(1.1);
    }
    .step-label {
        font-size: 0.8rem;
    }

    /* ESTILOS PARA LA GALERÍA */
    .main-image {
        transition: transform 0.3s ease;
    }
    .main-image:hover {
        transform: scale(1.02);
    }
    .thumbnail-img {
        transition: all 0.3s ease;
    }
    .thumbnail-img:hover {
        border-color: #007bff !important;
        transform: scale(1.05);
    }
    .thumbnail-img.active {
        border-color: #007bff !important;
        box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.25);
    }
    .thumbnails-container {
        max-height: 400px;
        overflow-y: auto;
    }
    .thumbnail-item {
        text-align: center;
    }
    .no-image-placeholder {
        border: 2px dashed #dee2e6;
    }

    /* ESTILOS PARA ESPECIFICACIONES */
    .specs-grid {
        display: grid;
        gap: 10px;
    }
    .spec-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 5px 0;
        border-bottom: 1px solid #f8f9fa;
    }
    .spec-label {
        font-weight: 500;
        color: #6c757d;
        font-size: 0.9rem;
    }
    .spec-value {
        text-align: right;
    }

    /* ESTILOS PARA OPCIONES */
    .option-card {
        background-color: #f8f9fa;
        transition: all 0.3s ease;
        height: 100%;
    }
    .option-card:hover {
        background-color: #e9ecef;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    .badge-sm {
        font-size: 0.7rem;
        padding: 0.25em 0.4em;
    }

    /* ESTILOS FINANCIEROS */
    .finance-summary {
        font-size: 0.95rem;
    }
    .finance-item {
        transition: background-color 0.2s ease;
    }
    .finance-item:hover {
        background-color: #f8f9fa;
    }
</style>
@endpush

@push('scripts')
<script>
    function ampliarImagen(src) {
        document.getElementById('modalImage').src = src;
        new bootstrap.Modal(document.getElementById('imageModal')).show();
    }

    // ✅ CORRECCIÓN: Función mejorada para cambiar imagen principal
    function cambiarImagenPrincipal(detalleId, nuevaSrc, nuevaAlt, tipo, descripcion, elemento) {
        // Cambiar la imagen principal
        const imgPrincipal = document.getElementById(`imagen-principal-${detalleId}`);
        if (imgPrincipal) {
            imgPrincipal.src = nuevaSrc;
            imgPrincipal.alt = nuevaAlt;
        }

        // Actualizar el texto descriptivo
        const textoContainer = imgPrincipal.nextElementSibling;
        if (textoContainer) {
            let titulo = tipo === 'diseño' ? 'Diseño Personalizado' : 'Producto Base';
            textoContainer.innerHTML = `<strong>${titulo}</strong>`;
            
            if (descripcion) {
                textoContainer.innerHTML += `<br><small class="text-muted">${descripcion}</small>`;
            }
        }

        // Actualizar borde activo en miniaturas
        const todasMiniaturas = document.querySelectorAll(`.thumbnail-img`);
        todasMiniaturas.forEach(thumb => {
            thumb.style.borderColor = '#dee2e6';
            thumb.classList.remove('active');
        });
        
        // Activar la miniatura clickeada
        elemento.style.borderColor = '#007bff';
        elemento.classList.add('active');
    }

    // Inicializar tooltips
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
@endpush