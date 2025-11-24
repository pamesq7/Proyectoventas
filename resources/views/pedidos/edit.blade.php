@extends('layouts.app')

@section('title', 'Nuevo Pedido - ' . $producto->nombre)

@section('title', 'Editar Pedido #' . $pedido->idVenta . ($producto ? ' - ' . $producto->nombre : ''))

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Editar Pedido #{{ $pedido->idVenta }}</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('pedidos.index') }}">Pedidos</a></li>
        <li class="breadcrumb-item active">Editar</li>
    </ol>

    {{-- Alertas bootstrap --}}
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i>
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif
    @if(session('info'))
    <div class="alert alert-info alert-dismissible fade show" role="alert">
        <i class="fas fa-info-circle me-2"></i>
        {{ session('info') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif


    <form id="formNuevoPedido" action="{{ route('pedidos.guardar-desde-catalogo') }}" method="POST">
        @csrf
        <input type="hidden" name="idProducto" value="{{ $producto->idProducto }}">
        <input type="hidden" name="idVariante" value="{{ $varianteId ?? '' }}">

        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <i class="fas fa-plus me-1"></i>
                    Crear Nuevo Pedido
                </div>
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-success btn-sm" id="btnAgregarCarrito">
                        <i class="fas fa-cart-plus me-1"></i> Agregar al carrito

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-edit me-2"></i>Datos del pedido</h5>
            <div>
                <a href="{{ route('pedidos.show', $pedido->idVenta) }}" class="btn btn-info btn-sm">
                    <i class="fas fa-eye me-1"></i> Ver
                </a>
                <a href="{{ route('pedidos.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i> Volver
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="p-3 bg-light rounded h-100">
                        <h6 class="text-muted mb-2">Cliente</h6>
                        @php
                        if ($pedido->clienteNatural && $pedido->clienteNatural->user) {
                        $nombreCliente = trim($pedido->clienteNatural->user->name . ' ' .
                        $pedido->clienteNatural->user->primerApellido . ' ' .
                        ($pedido->clienteNatural->user->segundApellido ?? ''));
                        $tipoCliente = 'Cliente Natural';
                        } elseif ($pedido->clienteEstablecimiento) {
                        $nombreCliente = $pedido->clienteEstablecimiento->razonSocial ??
                        $pedido->clienteEstablecimiento->razonSocial ??
                        'Establecimiento';
                        $tipoCliente = 'Establecimiento';
                        } else {
                        $nombreCliente = 'No especificado';
                        $tipoCliente = '';
                        }
                        @endphp
                        <div class="fw-semibold">{{ $nombreCliente }}</div>
                        @if($tipoCliente)
                        <div class="text-muted small">{{ $tipoCliente }}</div>
                        @endif
                        <div class="text-muted small">Creado: {{ $pedido->created_at->format('d/m/Y H:i') }}</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="p-3 bg-light rounded h-100">
                        <h6 class="text-muted mb-2">Resumen</h6>
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <strong>Total:</strong> ${{ number_format($pedido->total, 2) }}
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <strong>Saldo:</strong> ${{ number_format($pedido->saldo, 2) }}
                            @php $pagado = (float)($pedido->saldo ?? 0) <= 0; @endphp
                                <span class="badge bg-{{ $pagado ? 'success' : 'danger' }}">{{ $pagado ? 'Pago completado' : 'Debe' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <form action="{{ route('pedidos.update', $pedido->idVenta) }}" method="POST" enctype="multipart/form-data" class="row g-3">
                @csrf
                @method('PUT')

                <div class="col-md-4">
                    <label for="fechaEntrega" class="form-label">Fecha de entrega *</label>
                    <input type="date"
                        id="fechaEntrega"
                        name="fechaEntrega"
                        class="form-control"
                        value="{{ old('fechaEntrega', $pedido->fechaEntrega ? \Carbon\Carbon::parse($pedido->fechaEntrega)->format('Y-m-d') : '') }}"
                        required>
                </div>

                <div class="col-md-5">
                    <label for="lugarEntrega" class="form-label">Lugar de entrega *</label>
                    <input type="text" id="lugarEntrega" name="lugarEntrega" class="form-control" maxlength="200" value="{{ old('lugarEntrega', $pedido->lugarEntrega) }}" required>
                </div>

                <div class="col-md-3">
                    <label for="estadoPedido" class="form-label">Estado *</label>
                    <select id="estadoPedido" name="estadoPedido" class="form-select" required>
                        @foreach($estados as $k => $label)
                        <option value="{{ $k }}" {{ old('estadoPedido', (string)$pedido->estadoPedido) === (string)$k ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12">
                    <label for="imagenPedido" class="form-label">Imagen del Pedido</label>
                    <div class="imagen-container">
                        <input type="file" id="imagenPedido" name="imagenPedido" class="form-control" accept="image/*">
                        {{-- Mostrar imagen desde diseños --}}
                        @if($pedido->disenos && $pedido->disenos->first() && $pedido->disenos->first()->archivo)
                        <div class="mt-2 position-relative" style="display: inline-block;">
                            <img src="{{ asset('storage/' . $pedido->disenos->first()->archivo) }}" alt="Imagen del pedido" class="img-thumbnail" style="max-width: 200px; height: auto;">
                            <button type="button" class="btn btn-sm btn-outline-danger position-absolute" style="top: 5px; right: 5px; padding: 2px 6px;" data-id="{{ $pedido->idVenta }}" onclick="confirmDeleteImage(this)">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        @endif
                    </div>
                    {{-- Checkbox para eliminación (solo si hay imagen) --}}
                    @if($pedido->disenos && $pedido->disenos->first() && $pedido->disenos->first()->archivo)
                    <div class="form-check mt-2">
                        <input class="form-check-input" type="checkbox" id="delete_imagen" name="delete_imagen" value="1" style="display: none;">
                        <label class="form-check-label" for="delete_imagen">
                            Eliminar imagen actual (se activa al confirmar)
                        </label>
                    </div>
                    @endif
                </div>

                <div class="col-12 d-flex justify-content-end mt-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Guardar cambios

                    </button>
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fas fa-save me-1"></i> Guardar Pedido
                    </button>
                    <a href="{{ route('pedidos.catalogo') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left me-1"></i> Volver al Catálogo
                    </a>
                </div>
            </div>


            {{-- Información del producto --}}
            <div class="card mb-4 mt-3">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-cube me-2"></i>Producto Seleccionado
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-4 text-center mb-3 mb-md-0">
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
                                class="img-fluid rounded shadow"
                                style="max-height: 200px;"
                                alt="{{ $producto->nombre }}">
                            @else
                            <div class="bg-light d-flex align-items-center justify-content-center rounded"
                                style="height: 150px;">
                                <i class="fas fa-image fa-3x text-muted"></i>
                            </div>
                            @endif
                        </div>
                        <div class="col-md-8">
                            <h5 class="mb-1">{{ $producto->nombre }}</h5>
                            <p class="text-muted mb-2">{{ $producto->descripcion }}</p>
                            <div class="row">
                                <div class="col-6">
                                    <strong>Precio Base:</strong><br>
                                    <span class="h5 text-success">
                                        Bs {{ number_format($producto->precioVenta, 2) }}
                                    </span>
                                </div>
                                <div class="col-6">
                                    <strong>Estado:</strong><br>
                                    <span class="badge bg-success">Disponible</span>
                                </div>
                            </div>

    <!-- Edición completa de detalles -->
    <div class="card mt-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-list me-2"></i>Detalles del pedido</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('pedidos.update-detalles', $pedido->idVenta) }}" method="POST" id="form-detalles">
                @csrf
                @method('PUT')

                <div id="detalles-bloques">
                    <!-- Bloque 0: detalles existentes -->
                    <div class="bloque-detalle" data-index="0">
                        <!-- Selector de producto por bloque -->
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Producto (precarga precio por talla en filas nuevas)</label>
                                <select class="form-select form-select-sm productoSelector">
                                    <option value="">Seleccionar producto</option>
                                    @foreach(($productos ?? []) as $p)
                                    <option value="{{ $p->idProducto }}">[#{{ $p->idProducto }}] {{ $p->nombre }}</option>
                                    @endforeach
                                </select>
                                <div class="form-text">No cambia el historial del pedido; solo ayuda a rellenar precios de nuevas filas.</div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-sm align-middle tabla-detalles">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 12rem">Talla</th>
                                        <th style="width: 7rem">Cant.</th>
                                        <th style="width: 9rem">Precio</th>
                                        <th style="width: 14rem">Nombre</th>
                                        <th style="width: 10rem">Número</th>
                                        <th>Descripción</th>
                                        <th>Observación</th>
                                        <th style="width: 6rem" class="text-end">Subtotal</th>
                                        <th style="width: 3rem"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach(($pedido->detalleVentas ?? []) as $det)
                                    <tr>
                                        <input type="hidden" name="row_id[]" value="{{ $det->iddetalleVenta }}">
                                        <td>
                                            <select name="idTallas[]" class="form-select form-select-sm tallaSel" required>
                                                @foreach(($tallas ?? []) as $t)
                                                <option value="{{ $t->idTallas }}" {{ $det->idTallas == $t->idTallas ? 'selected' : '' }}>{{ $t->nombre }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <input type="number" name="cantidad[]" class="form-control form-control-sm cantidad" min="1" value="{{ $det->cantidad }}" required>
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" name="precioUnitario[]" class="form-control form-control-sm precio" min="0" value="{{ number_format($det->precioUnitario, 2, '.', '') }}" required>
                                        </td>
                                        <td>
                                            <input type="text" name="nombrePersonalizado[]" class="form-control form-control-sm" value="{{ $det->nombrePersonalizado }}">
                                        </td>
                                        <td>
                                            <input type="text" name="numeroPersonalizado[]" class="form-control form-control-sm" value="{{ $det->numeroPersonalizado }}">
                                        </td>
                                        <td>
                                            <input type="text" name="descripcion[]" class="form-control form-control-sm" value="{{ $det->descripcion }}">
                                        </td>
                                        <td>
                                            <input type="text" name="observacion[]" class="form-control form-control-sm" value="{{ $det->observacion }}">
                                        </td>
                                        <td class="text-end subtotal">{{ number_format($det->cantidad * $det->precioUnitario, 2) }}</td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-outline-danger btn-sm btn-del" data-id="{{ $det->iddetalleVenta }}"><i class="fas fa-trash"></i></button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="9">
                                            <button type="button" class="btn btn-sm btn-outline-primary btn-agregar-fila">
                                                <i class="fas fa-plus me-1"></i>Agregar fila
                                            </button>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>

                        </div>
                    </div>
                </div>
            </div>

            {{-- Detalles del producto / variante / pack --}}
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-cube me-2"></i>Detalles del Producto
                    </h5>
                </div>

                @if(isset($pack))
                <div class="alert alert-primary d-flex align-items-start m-3">
                    <i class="fas fa-boxes me-2 mt-1"></i>
                    <div>
                        <strong>Este producto es un PACK:</strong> {{ $pack->nombre ?? 'Pack' }}<br>
                        @if(!empty($pack->descripcion))
                        <small class="d-block mt-1">{{ $pack->descripcion }}</small>
                        @endif
                        @if(isset($variantesPack) && $variantesPack->count())
                        <small class="d-block mt-1">
                            Variantes incluidas:
                            {{ collect($variantesPack)->pluck('nombreVariante')->filter()->join(' + ') }}
                        </small>
                        @endif
                    </div>
                </div>
                @endif

                <div class="d-flex justify-content-between align-items-center mt-2">
                    <button type="button" class="btn btn-outline-secondary btn-sm" id="btn-agregar-bloque">
                        <i class="fas fa-layer-group me-1"></i> Agregar otro pedido
                    </button>
                </div>


                {{-- PACK --}}
                @if($esPack && !empty($variantesPack) && count($variantesPack))
                <div class="card-body">
                    @foreach($variantesPack as $var)
                    <div class="border rounded p-3 mb-3">
                        <span class="badge bg-primary mb-2">
                            Variante del pack: {{ strtoupper($var['nombreVariante'] ?? '—') }}
                        </span>
                        <input type="hidden" name="idVariante[]" value="{{ $var['idVariante'] }}">

                        @forelse($var['opciones'] as $opcion)
                        <div class="mb-3">
                            <label for="opcion_{{ $var['idVariante'] }}_{{ $opcion['idOpcion'] }}"
                                class="form-label">
                                <strong>{{ $opcion['nombre'] }}</strong>
                                @if(!empty($opcion['descripcion']))
                                <small class="text-muted">({{ $opcion['descripcion'] }})</small>
                                @endif
                            </label>

                            @if(!empty($opcion['caracteristicas']))
                            <select
                                class="form-select opcion-select"
                                id="opcion_{{ $var['idVariante'] }}_{{ $opcion['idOpcion'] }}"
                                name="opciones[{{ $var['idVariante'] }}][{{ $opcion['idOpcion'] }}]"
                                data-variante-id="{{ $var['idVariante'] }}"
                                data-opcion-id="{{ $opcion['idOpcion'] }}"
                                required>
                                <option value="">Seleccione una opción</option>
                                @foreach($opcion['caracteristicas'] as $car)
                                <option value="{{ $car['idCaracteristica'] }}"
                                    data-precio-extra="{{ $car['precioAdicional'] ?? 0 }}">
                                    {{ $car['nombre'] }}
                                    @if(($car['precioAdicional'] ?? 0) > 0)
                                    (+{{ number_format($car['precioAdicional'], 2) }} Bs)
                                    @endif
                                </option>
                                @endforeach
                            </select>
                            @else
                            <div class="alert alert-warning">
                                No hay características disponibles para esta opción.
                            </div>
                            @endif
                        </div>
                        @empty
                        <div class="alert alert-info">
                            Esta variante no tiene opciones configuradas.
                        </div>
                        @endforelse
                    </div>
                    @endforeach
                </div>
                @endif

                {{-- NO PACK --}}
                @if(!$esPack)
                <div class="card-body">
                    @if(isset($opcionesVariante) && $opcionesVariante->count())
                    @foreach($opcionesVariante as $opcion)
                    <div class="mb-3">
                        <label for="opcion_{{ $opcion['idOpcion'] }}" class="form-label">
                            <strong>{{ $opcion['nombre'] }}</strong>
                            @if(!empty($opcion['descripcion']))
                            <small class="text-muted">({{ $opcion['descripcion'] }})</small>
                            @endif
                        </label>

                        @if(!empty($opcion['caracteristicas']))
                        <select
                            class="form-select opcion-select"
                            id="opcion_{{ $opcion['idOpcion'] }}"
                            name="caracteristicas[{{ $opcion['idOpcion'] }}]"
                            data-opcion-id="{{ $opcion['idOpcion'] }}"
                            required>
                            <option value="">Seleccione una opción</option>
                            @foreach($opcion['caracteristicas'] as $car)
                            <option
                                value="{{ $car['idCaracteristica'] }}"
                                data-precio-extra="{{ $car['precioAdicional'] ?? 0 }}">
                                {{ $car['nombre'] }}
                                @if(($car['precioAdicional'] ?? 0) > 0)
                                (+{{ number_format($car['precioAdicional'], 2) }} Bs)
                                @endif
                            </option>
                            @endforeach
                        </select>
                        @else
                        <div class="alert alert-warning">
                            No hay características disponibles para esta opción.

                <div class="row g-3 mt-3">
                    <div class="col-md-4">
                        <label class="form-label">Tipo Pago</label>
                        <select name="tipoTransaccion" class="form-select form-select-sm">
                            <option value="">Sin registrar pago</option>
                            @foreach(($metodosPago ?? []) as $mp)
                            <option value="{{ $mp['codigo'] }}">{{ $mp['nombre'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Adelanto</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Bs</span>
                            <input type="number" step="0.01" min="0" name="montoAdelanto" class="form-control" placeholder="0.00">

                        </div>
                        @endif
                    </div>
                    @endforeach
                    @else
                    <div class="alert alert-info m-3">
                        No hay opciones configuradas para esta variante.
                    </div>
                    @endif
                </div>
                @endif
            </div>


            {{-- Modo de producto --}}
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Seleccione el modo de producto</h5>
                </div>
                <div class="card-body">
                    <input type="hidden" name="modo_producto" id="modoProducto" value="{{ $modoSeleccionado }}">

                    <div class="nav nav-pills mb-3" id="modoProductoTabs" role="tablist">
                        @foreach($modosProducto as $index => $modo)
                        <button class="nav-link {{ $index === 0 ? 'active' : '' }}"
                            id="modo-{{ $modo['key'] }}-tab"
                            data-bs-toggle="pill"
                            data-bs-target="#modo-{{ $modo['key'] }}"
                            type="button"
                            role="tab"
                            data-modokey="{{ $modo['key'] }}"
                            aria-controls="modo-{{ $modo['key'] }}"
                            aria-selected="{{ $index === 0 ? 'true' : 'false' }}">
                            <i class="fas fa-{{ $modo['icon'] ?? 'tshirt' }} me-1"></i>
                            {{ $modo['label'] }}
                        </button>
                        @endforeach
                    </div>

                    <div class="tab-content" id="modoProductoTabContent">
                        @foreach($modosProducto as $index => $modo)
                        <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}"
                            id="modo-{{ $modo['key'] }}"
                            role="tabpanel"
                            aria-labelledby="modo-{{ $modo['key'] }}-tab">
                            <div class="alert alert-info mb-0">
                                <i class="fas fa-info-circle me-2"></i>
                                Has seleccionado: <strong>{{ $modo['label'] }}</strong>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>


            {{-- Configurador rápido de tallas --}}
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Tallas, cantidad y datos base</h5>
                </div>
                <div class="card-body">
                    {{-- Tallas --}}
                    <div class="mb-3">
                        <label class="form-label">Tallas</label>
                        <div id="tallasButtons" class="d-flex flex-wrap gap-2">
                            @foreach($tallas as $talla)
                            <button type="button"
                                class="btn btn-outline-secondary btn-sm talla-btn"
                                data-id="{{ $talla->idTallas }}"
                                data-nombre="{{ $talla->nombre }}">
                                {{ $talla->nombre }}
                            </button>
                            @endforeach
                        </div>
                        <small class="text-muted">Haz clic para seleccionar una talla.</small>
                    </div>

                    {{-- Cantidad --}}
                    <div class="mb-3">
                        <label class="form-label d-block">Cantidad</label>
                        <div class="input-group" style="max-width: 220px;">
                            <button type="button" class="btn btn-outline-secondary" id="btnCantMenos">-</button>
                            <input type="number" class="form-control text-center" id="cantidadBase" value="1" min="1">
                            <button type="button" class="btn btn-outline-secondary" id="btnCantMas">+</button>
                        </div>
                    </div>

                    {{-- Número / Nombre base --}}
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Número (opcional)</label>
                            <input type="text"
                                id="numeroBase"
                                class="form-control"
                                placeholder="SIN NÚMERO">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nombre (opcional)</label>
                            <input type="text"
                                id="nombreBase"
                                class="form-control"
                                placeholder="SIN NOMBRE">
                        </div>
                    </div>

                    <div class="text-center">
                        <button type="button" class="btn btn-primary btn-lg" id="btnGenerarFilas">
                            <i class="fas fa-plus me-2"></i> GENERAR FILAS
                        </button>
                    </div>
                </div>
            </div>

            {{-- Tabla de filas generadas --}}
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Filas generadas</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle" id="tablaItems">
                            <thead class="table-dark">
                                <tr>
                                    <th style="width: 8%">Talla</th>
                                    <th style="width: 20%">Producto</th>
                                    <th style="width: 10%">Cantidad</th>
                                    <th style="width: 20%">Nombre</th>
                                    <th style="width: 12%">Número</th>
                                    <th style="width: 20%">Observaciones</th>
                                    <th style="width: 10%">Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="tbodyItems">
                                {{-- Filas generadas vía JS --}}
                            </tbody>
                        </table>
                        <small class="text-muted">
                            Estas filas se enviarán al servidor como <code>items[...][...]</code>,
                            y tu <strong>PedidoController</strong> las procesará en
                            <code>guardarDesdeCatalogo()</code>.
                        </small>
                    </div>
                </div>
            </div>
        </div> {{-- fin card principal --}}
    </form>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // ===
        // 1) Variables globales
        // ===
        const inputModoProducto = document.getElementById('modoProducto');
        const modoTabs = document.querySelectorAll('#modoProductoTabs [data-modokey]');
        const tallaButtons = document.querySelectorAll('.talla-btn');
        const inputCantidad = document.getElementById('cantidadBase');
        const btnCantMenos = document.getElementById('btnCantMenos');
        const btnCantMas = document.getElementById('btnCantMas');
        const inputNumero = document.getElementById('numeroBase');
        const inputNombre = document.getElementById('nombreBase');
        const btnGenerar = document.getElementById('btnGenerarFilas');
        const tbodyItems = document.getElementById('tbodyItems');
        let contadorFilas = tbodyItems ? tbodyItems.querySelectorAll('tr').length : 0;

        // ===
        // 2) Funciones de utilidad
        // ===
        function cambiarModo(modoKey, tabElement) {
            if (inputModoProducto) {
                inputModoProducto.value = modoKey;
                document.dispatchEvent(new CustomEvent('modoProductoCambiado', {
                    detail: {
                        modo: modoKey,
                        element: tabElement
                    }
                }));
            }

            modoTabs.forEach(tab => {
                const key = tab.getAttribute('data-modokey');
                const isActive = (key === modoKey);
                tab.classList.toggle('active', isActive);
                tab.setAttribute('aria-selected', isActive ? 'true' : 'false');

                const targetId = tab.getAttribute('data-bs-target')?.substring(1);
                if (!targetId) return;

                const pane = document.getElementById(targetId);
                if (!pane) return;
                pane.classList.toggle('show', isActive);
                pane.classList.toggle('active', isActive);
            });
        }

        function getProductoLabelActual() {
            const nombreBase = @json($producto->nombre);

            if (!inputModoProducto) return nombreBase;

            const modoKey = inputModoProducto.value;
            if (!modoKey) return nombreBase;

            const tab = document.querySelector('#modo-' + CSS.escape(modoKey) + '-tab');
            if (!tab) return nombreBase;

            return tab.textContent.trim();
        }

        // ===
        // 3) Manejadores de eventos
        // ===
        function inicializarEventos() {
            modoTabs.forEach(tab => {
                tab.addEventListener('click', function(e) {
                    e.preventDefault();
                    const key = this.getAttribute('data-modokey');
                    cambiarModo(key, this);
                });
            });

            // Tallas (solo una activa)
            tallaButtons.forEach(btn => {
                btn.addEventListener('click', function() {
                    tallaButtons.forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                });
            });

            btnCantMenos?.addEventListener('click', () => {
                let v = parseInt(inputCantidad.value || '1', 10);
                if (v > 1) inputCantidad.value = v - 1;
            });

            btnCantMas?.addEventListener('click', () => {
                let v = parseInt(inputCantidad.value || '1', 10);
                inputCantidad.value = v + 1;
            });

            btnGenerar?.addEventListener('click', generarFilas);

            tbodyItems?.addEventListener('click', function(e) {
                const btn = e.target.closest('.btnRemoveRow');
                if (!btn) return;
                const tr = btn.closest('tr');
                if (tr) tr.remove();
            });
        }

        // ===
        // 4) Lógica principal
        // ===
        function generarFilas() {
            if (!tallaButtons || !tbodyItems) return;

            const btnTalla = document.querySelector('.talla-btn.active');

            if (!btnTalla) {
                alert('Selecciona una talla.');
                return;
            }

            const cantidad = parseInt(inputCantidad?.value || '1', 10);
            if (isNaN(cantidad) || cantidad <= 0) {
                alert('La cantidad debe ser mayor a 0.');
                return;
            }

            const numeroBase = inputNumero?.value.trim() || '';
            const nombreBase = inputNombre?.value.trim() || '';
            const productoLabel = getProductoLabelActual();

            const idTallas = btnTalla.getAttribute('data-id');
            const nombreTal = btnTalla.getAttribute('data-nombre');

            contadorFilas++;
            const tr = document.createElement('tr');
            tr.dataset.index = contadorFilas;

            tr.innerHTML = `
                <td>
                    <strong>${nombreTal}</strong>
                    <input type="hidden" name="items[${contadorFilas}][idTallas]" value="${idTalla}">
                </td>
                <td>${productoLabel}</td>
                <td>
                    <input type="number" class="form-control form-control-sm"
                           name="items[${contadorFilas}][cantidad]" min="1" value="${cantidad}">
                </td>
                <td>
                    <input type="text" class="form-control form-control-sm"
                           name="items[${contadorFilas}][nombre]" placeholder="SIN NOMBRE" value="${nombreBase}">
                </td>
                <td>
                    <input type="text" class="form-control form-control-sm"
                           name="items[${contadorFilas}][numero]" placeholder="SIN NÚMERO" value="${numeroBase}">
                </td>
                <td>
                    <input type="text" class="form-control form-control-sm"
                           name="items[${contadorFilas}][observaciones]" placeholder="Observaciones (opcional)">
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-outline-danger btnRemoveRow" title="Eliminar fila">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            `;

            tbodyItems.appendChild(tr);

            // Limpieza básica
            btnTalla.classList.remove('active');
            if (inputCantidad) inputCantidad.value = '1';
        }

        // ===
        // 5) Inicialización
        // ===
        function inicializar() {
            if (inputModoProducto && modoTabs.length) {
                const modoInicial = inputModoProducto.value || modoTabs[0].getAttribute('data-modokey');
                cambiarModo(modoInicial, modoTabs[0]);
            }
            inicializarEventos();
        }

        inicializar();
    });
</script>

<script>
    // Lógica para "Agregar al carrito"
    document.addEventListener('DOMContentLoaded', function() {
        const btnAgregarCarrito = document.getElementById('btnAgregarCarrito');
        const formNuevoPedido = document.getElementById('formNuevoPedido');
        const carritoOffcanvasEl = document.getElementById('carritoLateral');
        const carritoContenido = document.getElementById('carritoContenido');
        const contadorCarrito = document.getElementById('carritoCantidadBadge');

        const carritoOffcanvas = carritoOffcanvasEl ?
            bootstrap.Offcanvas.getOrCreateInstance(carritoOffcanvasEl) :
            null;

        function abrirCarrito() {
            if (carritoOffcanvas) carritoOffcanvas.show();
        }

        btnAgregarCarrito?.addEventListener('click', function() {
            if (!formNuevoPedido) return;

            const formData = new FormData(formNuevoPedido);

            fetch("{{ route('pedidos.agregar-carrito') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: formData
                })
                .then(r => r.json())
                .then(data => {
                    if (!data.ok) {
                        alert(data.mensaje || 'Error al agregar al carrito');
                        return;
                    }

                    if (carritoContenido && data.html_carrito) {
                        carritoContenido.innerHTML = data.html_carrito;
                    }

                    if (contadorCarrito && typeof data.items_en_carrito !== 'undefined') {
                        contadorCarrito.textContent = data.items_en_carrito;
                        contadorCarrito.style.display = data.items_en_carrito > 0 ? 'inline-block' : 'none';
                    }

                    abrirCarrito();

                    const tbody = document.getElementById('tbodyItems');
                    if (tbody) tbody.innerHTML = '';
                })
                .catch(err => {
                    console.error(err);
                    alert('Error de conexión al agregar al carrito');
                });
        });
    });
</script>

});
</script>

{{-- SweetAlert2 (opcional) --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@if(session('success'))
<script>
    Swal.fire({
        icon: 'success',
        title: 'Éxito',
        text: @json(session('success'))
    });
</script>
@endif
@if(session('error'))
<script>
    Swal.fire({
        icon: 'error',
        title: 'Error',
        text: @json(session('error'))
    });
</script>
@endif
@endpush

                <input type="hidden" name="delete_ids" id="delete_ids_holder">
                <div class="d-flex justify-content-end mt-3">
                    <button type="submit" class="btn btn-success"><i class="fas fa-save me-1"></i>Guardar detalles</button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        (function() {
            const bloquesContainer = document.getElementById('detalles-bloques');
            const deleteIdsHolder = document.getElementById('delete_ids_holder');
            const btnAgregarBloque = document.getElementById('btn-agregar-bloque');
            let deleteIds = [];
            let bloqueCount = 1; // ya existe el bloque 0

            // Precios por talla por bloque
            const preciosPorTallaByBlock = {}; // { index: { idTalla: precio } }

            function initBloqueEvents(bloque) {
                // Producto selector por bloque
                const sel = bloque.querySelector('.productoSelector');
                if (sel) {
                    sel.addEventListener('change', async function() {
                        const idProd = this.value;
                        const idx = bloque.dataset.index;
                        preciosPorTallaByBlock[idx] = {};
                        if (!idProd) return;
                        try {
                            const url = "{{ route('api.producto.tallas-precios', ['idProducto' => ':id']) }}".replace(':id', idProd);
                            const res = await fetch(url);
                            const data = await res.json();
                            if (data && Array.isArray(data.precios)) {
                                data.precios.forEach(p => {
                                    preciosPorTallaByBlock[idx][String(p.idTalla)] = parseFloat(p.precioUnitario);
                                });
                            }
                        } catch (e) {
                            console.error(e);
                        }
                    });
                }

                const tbody = bloque.querySelector('.tabla-detalles tbody');
                // Inputs cantidad/precio
                tbody.addEventListener('input', function(e) {
                    const tr = e.target.closest('tr');
                    if (tr) calcRowSubtotal(tr);
                });
                // Cambio de talla: solo filas nuevas
                tbody.addEventListener('change', function(e) {
                    if (e.target.classList.contains('tallaSel')) {
                        const tr = e.target.closest('tr');
                        const rowId = tr.querySelector('input[name="row_id[]"]').value;
                        if (!rowId) {
                            const idx = bloque.dataset.index;
                            const idT = String(e.target.value);
                            const mapa = preciosPorTallaByBlock[idx] || {};
                            if (mapa[idT] != null) {
                                const precioInput = tr.querySelector('.precio');
                                precioInput.value = mapa[idT].toFixed(2);
                                calcRowSubtotal(tr);
                            }
                        }
                    }
                });

                // Agregar fila
                const btnAdd = bloque.querySelector('.btn-agregar-fila');
                btnAdd.addEventListener('click', function() {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <input type=\"hidden\" name=\"row_id[]\" value=\"\">
                        <td>
                            <select name=\"idTallas[]\" class=\"form-select form-select-sm tallaSel\" required>
                                @foreach(($tallas ?? []) as $t)
                                    <option value=\"{{ $t->idTallas }}\">{{ $t->nombre }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td><input type=\"number\" name=\"cantidad[]\" class=\"form-control form-control-sm cantidad\" min=\"1\" value=\"1\" required></td>
                        <td><input type=\"number\" step=\"0.01\" name=\"precioUnitario[]\" class=\"form-control form-control-sm precio\" min=\"0\" value=\"0.00\" required></td>
                        <td><input type=\"text\" name=\"nombrePersonalizado[]\" class=\"form-control form-control-sm\"></td>
                        <td><input type=\"text\" name=\"numeroPersonalizado[]\" class=\"form-control form-control-sm\"></td>
                        <td><input type=\"text\" name=\"descripcion[]\" class=\"form-control form-control-sm\"></td>
                        <td><input type=\"text\" name=\"observacion[]\" class=\"form-control form-control-sm\"></td>
                        <td class=\"text-end subtotal\">0.00</td>
                        <td class=\"text-center\"><button type=\"button\" class=\"btn btn-outline-danger btn-sm btn-del\"><i class=\"fas fa-trash\"></i></button></td>
                    `;
                    tbody.appendChild(tr);
                });

                // Eliminar fila
                tbody.addEventListener('click', function(e) {
                    if (e.target.closest('.btn-del')) {
                        const tr = e.target.closest('tr');
                        const id = tr.querySelector('input[name=\"row_id[]\"]').value;
                        if (id) {
                            deleteIds.push(id);
                            deleteIdsHolder.value = deleteIds.join(',');
                            // Evitar que se envíe esta fila al backend
                            tr.querySelectorAll('input,select').forEach(el => {
                                el.name = el.name + '_ignored';
                            });
                        }
                        tr.remove();
                        calcTotalsGlobal();
                    }
                });
            }

            function calcRowSubtotal(tr) {
                const q = parseFloat(tr.querySelector('.cantidad')?.value || '0');
                const p = parseFloat(tr.querySelector('.precio')?.value || '0');
                tr.querySelector('.subtotal').textContent = (q * p).toFixed(2);
                calcTotalsGlobal();
            }

            function calcTotalsGlobal() {
                let sum = 0;
                bloquesContainer.querySelectorAll('.tabla-detalles tbody tr').forEach(tr => {
                    const c = parseFloat(tr.querySelector('.cantidad')?.value || '0');
                    const p = parseFloat(tr.querySelector('.precio')?.value || '0');
                    sum += (c * p);
                });
                document.getElementById('sum-total').textContent = sum.toFixed(2);
            }

            // Inicializar bloque 0
            initBloqueEvents(bloquesContainer.querySelector('.bloque-detalle[data-index="0"]'));
            // Inicializar subtotales existentes
            bloquesContainer.querySelectorAll('.tabla-detalles tbody tr').forEach(tr => calcRowSubtotal(tr));
            calcTotalsGlobal();

            // Agregar nuevo bloque
            btnAgregarBloque.addEventListener('click', function() {
                const idx = bloqueCount++;
                const bloque = document.createElement('div');
                bloque.className = 'bloque-detalle';
                bloque.dataset.index = String(idx);
                bloque.innerHTML = `
                    <hr class=\"my-3\">
                    <div class=\"row g-3 mb-3\">
                        <div class=\"col-md-6\">
                            <label class=\"form-label\">Producto (precarga precio por talla en filas nuevas)</label>
                            <select class=\"form-select form-select-sm productoSelector\">
                                <option value=\"\">Seleccionar producto</option>
                                @foreach(($productos ?? []) as $p)
                                    <option value=\"{{ $p->idProducto }}\">[#{{ $p->idProducto }}] {{ $p->nombre }}</option>
                                @endforeach
                            </select>
                            <div class=\"form-text\">Este bloque crea filas nuevas independientes del resto.</div>
                        </div>
                    </div>
                    <div class=\"table-responsive\"> 
                        <table class=\"table table-sm align-middle tabla-detalles\">
                            <thead class=\"table-light\">
                                <tr>
                                    <th style=\"width: 12rem\">Talla</th>
                                    <th style=\"width: 7rem\">Cant.</th>
                                    <th style=\"width: 9rem\">Precio</th>
                                    <th style=\"width: 14rem\">Nombre</th>
                                    <th style=\"width: 10rem\">Número</th>
                                    <th>Descripción</th>
                                    <th>Observación</th>
                                    <th style=\"width: 6rem\" class=\"text-end\">Subtotal</th>
                                    <th style=\"width: 3rem\"></th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                            <tfoot>
                                <tr>
                                    <td colspan=\"9\">
                                        <button type=\"button\" class=\"btn btn-sm btn-outline-primary btn-agregar-fila\"><i class=\"fas fa-plus me-1\"></i>Agregar fila</button>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                `;
                bloquesContainer.appendChild(bloque);
                initBloqueEvents(bloque);
            });
        })();
    </script>

    <script>
        function confirmDeleteImage(button) {
            const idVenta = button.getAttribute('data-id');
            // Proceder directamente con la eliminación sin notificación inicial
            eliminarImagenAjax(idVenta);
        }

        function eliminarImagenAjax(idVenta) {
            console.log('Iniciando eliminación AJAX para idVenta:', idVenta);
            fetch(`/pedidos/${idVenta}/eliminar-imagen`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => {
                    console.log('Respuesta del servidor:', response);
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Datos JSON recibidos:', data);
                    if (data.success) {
                        // Actualizar la vista: quitar la imagen y mostrar el campo de subida
                        document.querySelector('.imagen-container').innerHTML = `
                <input type="file" id="imagenPedido" name="imagenPedido" class="form-control" accept="image/*">
                <div class="form-text mt-2">Sube una nueva imagen si lo deseas.</div>
            `;
                        // Mostrar notificación de éxito
                        window.showNotification('success', 'Imagen eliminada correctamente');
                    } else {
                        // Mostrar notificación de error
                        window.showNotification('error', 'Error al eliminar la imagen: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error en AJAX:', error);
                    alert('Error al eliminar la imagen: ' + error.message);
                });
        }
    </script>
    @endpush
</div>
@endsection

