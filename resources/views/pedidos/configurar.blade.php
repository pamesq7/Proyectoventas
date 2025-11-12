@extends('layouts.app')

@section('title', 'Nuevo Pedido - ' . $producto->nombre)

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Nuevo Pedido</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('pedidos.catalogo') }}">Catálogo</a></li>
        <li class="breadcrumb-item active">Nuevo Pedido</li>
    </ol>

    {{-- Alertas --}}
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

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <i class="fas fa-plus me-1"></i>
                Crear Nuevo Pedido
            </div>
            <a href="{{ route('pedidos.catalogo') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Volver al Catálogo
            </a>
        </div>
        <!-- Información del producto -->
        <div class="card mb-4">
            <div class="card-header">

                <h5 class="mb-0">
                    <i class="fas fa-cube me-2"></i>Producto Seleccionado
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 text-center">
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
                        <h5>{{ $producto->nombre }}</h5>
                        <p class="text-muted">{{ $producto->descripcion }}</p>
                        <div class="row">
                            <div class="col-6">
                                <strong>Precio Base:</strong><br>
                                <span class="h5 text-success">Bs {{ number_format($producto->precioVenta, 2) }}</span>
                            </div>
                            <div class="col-6">
                                <strong>Estado:</strong><br>
                                <span class="badge bg-success">Disponible</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- Detalles del producto -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-cube me-2"></i>Detalles del Producto
                </h5>
            </div>



            @if(isset($pack))
            <div class="alert alert-primary d-flex align-items-start">
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

            {{-- === NO PACK: tu bloque original === --}}
            @if(!$esPack)
            <span class="badge bg-primary">
                Variante: {{ strtoupper($varianteNombre ?? '—') }}
            </span>

            <div class="card-body">
                @if(isset($opcionesVariante) && $opcionesVariante->count())
                <form id="formOpcionesProducto">
                    @csrf
                    <input type="hidden" name="idProducto" value="{{ $producto->idProducto }}">
                    <input type="hidden" name="idVariante" value="{{ $varianteId }}">

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
                            name="opciones[{{ $opcion['idOpcion'] }}]"
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
                        <div class="alert alert-warning">No hay características disponibles para esta opción.</div>
                        @endif
                    </div>
                    @endforeach
                </form>
                @else
                <div class="alert alert-info">No hay opciones configuradas para esta variante.</div>
                @endif
            </div>
            @endif

            {{-- PACK: un bloque por cada VARIANTE incluida --}}
            @if($esPack && !empty($variantesPack) && count($variantesPack))
            <div class="card-body">
                <form id="formOpcionesPack">
                    @csrf
                    <input type="hidden" name="idProducto" value="{{ $producto->idProducto }}">

                    @foreach($variantesPack as $var)
                    <div class="border rounded p-3 mb-3">
                        <span class="badge bg-primary mb-2">
                            Variante del pack: {{ strtoupper($var['nombreVariante'] ?? '—') }}
                        </span>
                        <input type="hidden" name="idVariante[]" value="{{ $var['idVariante'] }}">

                        @forelse($var['opciones'] as $opcion)
                        <div class="mb-3">
                            <label for="opcion_{{ $var['idVariante'] }}_{{ $opcion['idOpcion'] }}" class="form-label">
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
                            <div class="alert alert-warning">No hay características disponibles para esta opción.</div>
                            @endif
                        </div>
                        @empty
                        <div class="alert alert-info">Esta variante no tiene opciones configuradas.</div>
                        @endforelse
                    </div>
                    @endforeach
                </form>
            </div>
            @endif

            {{-- NO PACK: una sola variante --}}
            @if(!$esPack)
            {{-- … tu bloque original renderizando $opcionesVariante … --}}
            @endif


            {{-- === NO PACK: muestra una sola variante === --}}
            @if(!$esPack)
            <div class="card-body">
                @if(isset($opcionesVariante) && $opcionesVariante->count())
                <form id="formOpcionesProducto">
                    @csrf
                    <input type="hidden" name="idProducto" value="{{ $producto->idProducto }}">
                    <input type="hidden" name="idVariante" value="{{ $varianteId }}">

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
                            name="opciones[{{ $opcion['idOpcion'] }}]"
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
                        <div class="alert alert-warning">No hay características disponibles para esta opción.</div>
                        @endif
                    </div>
                    @endforeach
                </form>
                @else
                <div class="alert alert-info">No hay opciones configuradas para esta variante.</div>
                @endif
            </div>
            @endif
        </div>


        <!-- Configuración de prendas -->
        <!-- <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-tshirt me-2"></i>Configuración de Prendas por talla
                </h5>
                <button type="button" id="btnAddRow" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus me-1"></i> Agregar Fila(s)
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="tablaPrendas">
                        <thead class="table-dark">
                            <tr>
                                <th width="20%">Talla *</th>
                                <th width="15%">Cantidad *</th>
                                <th width="25%">Nombre Personalizado</th>
                                <th width="20%">Número</th>
                                <th width="30%">Observaciones</th>
                                <th width="5%">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyItems">
                            <tr class="item-row">
                                <td>
                                    <select name="items[1][idTallas]" class="form-select form-select-sm sel-talla" required>
                                        <option value="">Seleccionar talla</option>
                                        @foreach($tallas as $talla)
                                        <option value="{{ $talla->idTallas }}">{{ $talla->nombre }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input type="number" name="items[1][cantidad]"
                                        class="form-control form-control-sm inp-cantidad"
                                        min="1" value="1" required>
                                </td>
                                <td>
                                    <input type="text" name="items[1][nombre]"
                                        class="form-control form-control-sm inp-nombre"
                                        placeholder="Nombre para la prenda">
                                </td>
                                <td>
                                    <input type="number" name="items[1][numero]"
                                        class="form-control form-control-sm inp-numero"
                                        placeholder="Número" min="0" max="999">
                                </td>
                                <td>
                                    <textarea name="items[1][observaciones]"
                                        class="form-control form-control-sm inp-obs"
                                        placeholder="Observaciones opcionales"
                                        rows="1"></textarea>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-outline-danger btnRemoveRow"
                                        title="Quitar fila" disabled>
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div> -->
        <!-- Configuración de prendas -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-tshirt me-2"></i>Configuración de Prendas por talla
                </h5>
                <button type="button" id="btnAddRow" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus me-1"></i> Agregar Fila(s)
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="tablaPrendas">
                        <thead class="table-dark">
                            <tr>
                                <th width="20%">Talla *</th>
                                <th width="15%">Cantidad *</th>
                                <th width="25%">Nombre Personalizado</th>
                                <th width="20%">Número</th>
                                <th width="30%">Observaciones</th>
                                <th width="5%">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyItems">
                            <tr class="item-row">
                                <td>
                                    <select name="items[1][idTalla]" class="form-select form-select-sm sel-talla" required>
                                        <option value="">Seleccionar talla</option>
                                        @foreach($tallas as $talla)
                                        <option value="{{ $talla->idTalla }}">{{ $talla->nombre }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input type="number" name="items[1][cantidad]"
                                        class="form-control form-control-sm inp-cantidad"
                                        min="1" value="1" required>
                                </td>
                                <td>
                                    <input type="text" name="items[1][nombre]"
                                        class="form-control form-control-sm inp-nombre"
                                        placeholder="Nombre para la prenda">
                                </td>
                                <td>
                                    <input type="number" name="items[1][numero]"
                                        class="form-control form-control-sm inp-numero"
                                        placeholder="Número" min="0" max="999">
                                </td>
                                <td>
                                    <textarea name="items[1][observaciones]"
                                        class="form-control form-control-sm inp-obs"
                                        placeholder="Observaciones opcionales"
                                        rows="1"></textarea>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-outline-danger btnRemoveRow"
                                        title="Quitar fila" disabled>
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>



        <div class="card-body">
            <form id="formNuevoPedido" action="{{ route('pedidos.guardar-desde-catalogo') }}" method="POST">
                @csrf
                <input type="hidden" name="tipoCliente" id="tipoCliente" value="">
                <input type="hidden" name="idCliente" id="idCliente" value="">
                <input type="hidden" name="idEstablecimiento" id="idEstablecimiento" value="">
                <input type="hidden" name="idProducto" id="idProducto" value="{{ $producto->idProducto }}" data-nombre="{{ strtolower($producto->nombre) }}" data-precio="{{ $producto->precioVenta ?? 0 }}">

                <div class="row">
                    <!-- Columna izquierda - Información del pedido -->
                    <div class="col-lg-8">
                        <!-- Información básica del pedido -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-info-circle me-2"></i>Información del Pedido
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Fecha de Entrega *</label>
                                        <input type="date" name="fechaEntrega" class="form-control"
                                            value="{{ old('fechaEntrega') }}"
                                            min="{{ date('Y-m-d', strtotime('+1 day')) }}" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Lugar de Entrega *</label>
                                        <input type="text" name="lugarEntrega" class="form-control"
                                            value="{{ old('lugarEntrega', 'Recojo en tienda') }}" required>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Columna derecha - Cliente y pago -->
                    <div class="col-lg-4">
                        <!-- Selección de cliente -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-user me-2"></i>Información del Cliente
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label">Cliente *</label>
                                    <div class="input-group mb-2">
                                        <a href="{{ url('users/create') }}" class="btn btn-success"
                                            title="Agregar nuevo usuario" target="_blank" rel="noopener">
                                            <i class="fas fa-plus"></i>
                                        </a>
                                        <input type="text" id="clienteFilter" class="form-control"
                                            placeholder="Buscar por CI, nombre o teléfono...">
                                    </div>
                                    <label class="form-label">Cliente *</label>
                                    @php($oldClienteSel = old('clienteSeleccionado'))
                                    <div class="input-group">
                                        <a href="{{ url('users/create') }}" class="btn btn-success" title="Agregar nuevo usuario" target="_blank" rel="noopener">
                                            <i class="fas fa-plus"></i>
                                        </a>
                                        <div class="flex-grow-1">
                                            <input type="text" id="clienteFilter" class="form-control mb-1" placeholder="Buscar por CI, nombre o teléfono...">
                                            <select class="form-select" name="clienteSeleccionado" id="clienteSelect" required>
                                                <option value="">Seleccione un cliente</option>
                                                <optgroup label="Clientes naturales"></optgroup>
                                                @foreach($clientesNaturales as $c)
                                                @php($val = 'natural:' . $c['id']) <!-- Cambiado de $c->idCliente a $c['id'] -->
                                                @php($doc = $c['ci'] ?? '') <!-- Cambiado de $c->user->ci a $c['ci'] -->
                                                @php($tel = $c['telefono'] ?? '') <!-- Cambiado de $c->user->telefono a $c['telefono'] -->
                                                @php($nom = $c['text'] ?? 'Cliente') <!-- Usamos el campo 'text' ya formateado -->
                                                <option value="{{ $val }}" data-ci="{{ $doc }}" data-telefono="{{ $tel }}">
                                                    {{ $nom }}
                                                </option>
                                                @endforeach
                                                <optgroup label="Establecimientos"></optgroup>
                                                @foreach($clientesEstablecimientos as $e)
                                                @php($val = 'establecimiento:' . $e['id'])
                                                @php($doc = $e['nit'] ?? '')
                                                @php($tel = $e['telefono'] ?? '')
                                                @php($nom = $e['text'] ?? 'Establecimiento')
                                                <option value="{{ $val }}" data-ci="{{ $doc }}" data-telefono="{{ $tel }}">
                                                    {{ $nom }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Información de pago -->
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-credit-card me-2"></i>Información de Pago
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label">Tipo de Pago *</label>
                                    <select class="form-select" id="tipoPago" name="tipoTransaccion" required>
                                        <option value="efectivo" selected>Efectivo</option>
                                        <option value="qr">QR</option>
                                        <option value="cheque">Cheque</option>
                                        <option value="transferencia">Transferencia bancaria</option>
                                    </select>
                                </div>

                                <div class="mb-3" id="efectivoGroup">
                                    <label class="form-label">Efectivo Recibido</label>
                                    <input type="number" class="form-control" id="efectivoRecibido"
                                        placeholder="Cantidad recibida" step="0.01" min="0">
                                </div>

                                <div class="form-check mb-3" id="efectivoExactoGroup">
                                    <input class="form-check-input" type="checkbox" id="efectivoExacto">
                                    <label class="form-check-label" for="efectivoExacto">Efectivo Exacto</label>
                                </div>

                                <hr>

                                <!-- Resumen de compra actualizado -->
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <div class="d-flex justify-content-between">
                                                    <small>Cantidad total</small>
                                                    <small id="uiCantTotal" class="ms-3">0</small>
                                                </div>
                                                <div id="uiBreakdownTallas" class="small text-muted">Ninguna talla seleccionada</div>
                                                <div class="d-flex justify-content-between">
                                                    <small>Precio unitario</small>
                                                    <small id="uiPrecioUnit">Bs 0.00</small>
                                                </div>
                                            </div>
                                            <div class="text-end">
                                                <h5 class="mb-0">TOTAL</h5>
                                                <h3 id="uiTotal" class="mb-0">Bs 0.00</h3>
                                            </div>
                                        </div>

                                        <div class="mt-3" id="uiTablaTallas">
                                            <!-- Aquí se mostrará la tabla de tallas -->
                                        </div>

                                        <div class="mt-3">
                                            <div class="d-flex justify-content-between">
                                                <small>Adelanto</small>
                                                <small id="uiAdelanto">Bs 0.00</small>
                                            </div>
                                            <div class="d-flex justify-content-between">
                                                <small><strong>Saldo</strong></small>
                                                <small id="uiSaldo"><strong>Bs 0.00</strong></small>
                                            </div>
                                            <hr>
                                            <div class="d-flex justify-content-between text-success">
                                                <small>Monto Efectivo</small>
                                                <small id="uiEfectivo">Bs 0.00</small>
                                            </div>
                                            <div class="d-flex justify-content-between text-danger">
                                                <small>Vuelto</small>
                                                <small id="uiVuelto">Bs 0.00</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Adelanto</label>
                                    <input type="number" class="form-control" id="montoAdelanto"
                                        name="montoAdelanto" placeholder="Monto de adelanto"
                                        step="0.01" min="0">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Botones de acción -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('pedidos.catalogo') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i> Volver al Catálogo
                            </a>
                            <div>
                                <button type="submit" class="btn btn-success btn-lg">
                                    <i class="fas fa-save me-2"></i> Guardar Pedido
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    (() => {
        // =========================
        // Estado y utilidades
        // =========================
        let contadorFilas = 1;
        let tallaPriceMap = new Map();

        const $ = (id) => document.getElementById(id);
        const precioBase = parseFloat($('idProducto')?.dataset?.precio || '0') || 0;

        // Formatea números como "Bs 0.00"
        const formatear = (n) => 'Bs ' + (Number(n || 0).toFixed(2)).replace(/\d(?=(\d{3})+\.)/g, '$&,');

        async function fetchJSON(url) {
            const res = await fetch(url, {
                headers: {
                    'Accept': 'application/json'
                }
            });
            if (!res.ok) throw new Error(`HTTP ${res.status} ${res.statusText} :: ${await res.text()}`);
            return res.json();
        }

        // =========================
        // Opciones/características por producto
        // =========================
        async function onProductoChange(idProducto) {
            const cont = $('caracteristicasContainer');
            const wrap = $('opcionesContainer');
            const msg = $('msgOpciones');

            if (cont) cont.innerHTML = '';
            if (wrap) {
                wrap.innerHTML = '';
                wrap.style.display = 'block';
            }
            if (msg) msg.textContent = 'Cargando opciones...';

            if (!idProducto) {
                msg && (msg.textContent = '⚠️ Producto no válido');
                return;
            }

            try {
                const data = await fetchJSON(`{{ url('api/producto') }}/${idProducto}/opciones`);
                const opciones = data.opciones || [];

                if (!opciones.length) {
                    msg && (msg.textContent = 'No hay opciones para este producto.');
                    return;
                }

                // Render
                opciones.forEach(op => {
                    const col = document.createElement('div');
                    col.className = 'col-md-3';
                    const label = document.createElement('label');
                    label.className = 'form-label';
                    label.innerHTML = `<strong>${op.nombreOpcion}</strong>`;

                    const select = document.createElement('select');
                    select.className = 'form-select';
                    select.name = `caracteristicas[${op.idOpcion || 'otros'}]`;
                    select.required = true;
                    select.innerHTML = '<option value="">Seleccionar</option>' +
                        (op.caracteristicas || []).map(c => `<option value="${c.idCaracteristica}">${c.nombre}</option>`).join('');

                    col.append(label, select);
                    cont?.appendChild(col);
                });

                msg && (msg.textContent = '');
                wrap && (wrap.style.display = '');
            } catch (e) {
                console.error('[onProductoChange]', e);
                msg && (msg.textContent = 'Error al cargar opciones del producto.');
            }
        }

        // =========================
        // Precios por talla
        // =========================
        async function loadTallaPrecios(idProducto) {
            tallaPriceMap = new Map();
            if (!idProducto) return;
            try {
                const data = await fetchJSON(`{{ url('api/producto') }}/${idProducto}/tallas-precios`);
                (data.precios || []).forEach(p => {
                    tallaPriceMap.set(String(p.idTallas), Number(p.precioUnitario || 0));
                });
            } catch (e) {
                console.error('[loadTallaPrecios]', e);
            }
        }

        // =========================
        // Mostrar/ocultar campo "Número"
        // =========================
        function ocultarCamposNumero() {
            document.querySelectorAll('#tbodyItems tr.item-row').forEach(tr => {
                const inp = tr.querySelector('.inp-numero');
                if (inp) {
                    const td = inp.closest('td');
                    if (td) td.style.display = 'none';
                    inp.value = '';
                }
            });
            // Header
            const ths = document.querySelectorAll('#tablaPrendas thead th');
            if (ths && ths.length >= 4) ths[3].style.display = 'none';
        }

        function toggleNumeroByProducto() {
            try {
                const sel = $('idProducto');
                if (!sel || !sel.options || sel.options.length === 0) return ocultarCamposNumero();

                if (sel.selectedIndex === -1) sel.selectedIndex = 0;
                const opt = sel.options[sel.selectedIndex];
                const nombre = (opt?.dataset?.nombre || '').toLowerCase();

                const esChamarra = nombre.includes('chamarra');
                const esPolera = nombre.includes('polera');
                const mostrar = esPolera && !esChamarra;

                // Filas
                document.querySelectorAll('#tbodyItems tr.item-row').forEach(tr => {
                    const inp = tr.querySelector('.inp-numero');
                    if (!inp) return;
                    const td = inp.closest('td');
                    if (td) td.style.display = mostrar ? '' : 'none';
                    if (!mostrar) inp.value = '';
                });
                // Header
                const ths = document.querySelectorAll('#tablaPrendas thead th');
                if (ths && ths.length >= 4) ths[3].style.display = mostrar ? '' : 'none';
            } catch (e) {
                console.error('[toggleNumeroByProducto]', e);
                ocultarCamposNumero();
            }
        }

        // =========================
        // Gestión de filas
        // =========================
        function actualizarBotonesEliminar() {
            const filas = document.querySelectorAll('.item-row');
            const botones = document.querySelectorAll('.btnRemoveRow');
            const onlyOne = (filas.length <= 1);
            botones.forEach(btn => btn.disabled = onlyOne);
        }

        function agregarFila() {
            contadorFilas++;
            const tr = document.createElement('tr');
            tr.className = 'item-row';
            tr.innerHTML = `
      <td>
        <select name="items[${contadorFilas}][idTallas]" class="form-select form-select-sm sel-talla" required>
          <option value="">Seleccionar talla</option>
          @foreach($tallas as $talla)
            <option value="{{ $talla->idTallas }}">{{ $talla->nombre }}</option>
          @endforeach
        </select>
      </td>
      <td><input type="number" name="items[${contadorFilas}][cantidad]" class="form-control form-control-sm inp-cantidad" min="1" value="1" required></td>
      <td><input type="text"   name="items[${contadorFilas}][nombre]"   class="form-control form-control-sm inp-nombre"  placeholder="Nombre para la prenda"></td>
      <td><input type="number" name="items[${contadorFilas}][numero]"   class="form-control form-control-sm inp-numero"  placeholder="Número" min="0" max="999"></td>
      <td><textarea name="items[${contadorFilas}][observaciones]" class="form-control form-control-sm inp-obs" rows="1" placeholder="Observaciones opcionales"></textarea></td>
      <td class="text-center">
        <button type="button" class="btn btn-sm btn-outline-danger btnRemoveRow" title="Quitar fila">
          <i class="fas fa-trash"></i>
        </button>
      </td>
    `;
            $('tbodyItems').appendChild(tr);
            toggleNumeroByProducto();
            actualizarBotonesEliminar();
            recalcTotales();
        }

        function eliminarFila(btn) {
            const tr = btn.closest('.item-row');
            if (!tr) return;
            const filas = document.querySelectorAll('.item-row');
            if (filas.length > 1) {
                tr.remove();
                actualizarBotonesEliminar();
                recalcTotales();
            }
        }

        // =========================
        // Totales / resumen
        // =========================
        function recalcTotales() {
            const filas = document.querySelectorAll('.item-row');
            let totalPrendas = 0;
            let totalPrecio = 0;
            const desglose = {};

            filas.forEach(fila => {
                const cant = parseInt(fila.querySelector('.inp-cantidad')?.value || '0') || 0;
                const selT = fila.querySelector('.sel-talla');
                const idT = selT?.value || '';
                const txtT = selT?.options[selT.selectedIndex]?.text || 'Sin talla';
                const unit = idT ? (tallaPriceMap.get(idT) || 0) : 0;
                const sub = cant * unit;

                totalPrendas += cant;
                totalPrecio += sub;

                if (!desglose[txtT]) desglose[txtT] = {
                    cantidad: 0,
                    subtotal: 0
                };
                desglose[txtT].cantidad += cant;
                desglose[txtT].subtotal += sub;
            });

            // Cantidad total
            $('uiCantTotal') && ($('uiCantTotal').textContent = totalPrendas);

            // Desglose tallas
            const bd = $('uiBreakdownTallas');
            if (bd) {
                bd.innerHTML = Object.entries(desglose).length ?
                    Object.entries(desglose).map(([t, d]) => `${t}: ${d.cantidad} (${formatear(d.subtotal)})`).join('<br>') :
                    'Ninguna talla seleccionada';
            }

            // Precio unitario (mostrar base si hay mezcla)
            $('uiPrecioUnit') && ($('uiPrecioUnit').textContent = formatear(precioBase));

            // Total
            $('uiTotal') && ($('uiTotal').textContent = formatear(totalPrecio));

            // Tabla tallas
            const tabla = $('uiTablaTallas');
            if (tabla) {
                const rows = Object.entries(desglose).map(([t, d]) => {
                    const unit = d.cantidad ? (d.subtotal / d.cantidad) : 0;
                    return `
          <tr>
            <td>${t}</td>
            <td class="text-end">${d.cantidad}</td>
            <td class="text-end">${formatear(unit)}</td>
            <td class="text-end">${formatear(d.subtotal)}</td>
          </tr>`;
                }).join('');
                tabla.innerHTML = `
        <table class="table table-sm small">
          <thead>
            <tr><th>Talla</th><th class="text-end">Cantidad</th><th class="text-end">P. Unit</th><th class="text-end">Subtotal</th></tr>
          </thead>
          <tbody>
            ${rows}
            <tr class="table-active"><th colspan="3" class="text-end">Total:</th><th class="text-end">${formatear(totalPrecio)}</th></tr>
          </tbody>
        </table>`;
            }

            // Adelanto / saldo
            const adelanto = parseFloat($('montoAdelanto')?.value || '0') || 0;
            $('uiAdelanto') && ($('uiAdelanto').textContent = formatear(adelanto));
            $('uiSaldo') && ($('uiSaldo').innerHTML = `<strong>${formatear(Math.max(totalPrecio - adelanto, 0))}</strong>`);

            // Efectivo / vuelto
            const exacto = $('efectivoExacto')?.checked;
            const inpEf = $('efectivoRecibido');
            let efectivo = 0;
            if (exacto) {
                efectivo = totalPrecio;
                if (inpEf) {
                    inpEf.value = totalPrecio.toFixed(2);
                    inpEf.readOnly = true;
                }
            } else {
                if (inpEf) {
                    inpEf.readOnly = false;
                    efectivo = parseFloat(inpEf.value || '0') || 0;
                }
            }
            $('uiEfectivo') && ($('uiEfectivo').textContent = formatear(efectivo));
            $('uiVuelto') && ($('uiVuelto').textContent = formatear(Math.max(efectivo - totalPrecio, 0)));
        }

        // =========================
        // Init
        // =========================
        document.addEventListener('DOMContentLoaded', async () => {
            // Producto preseleccionado (viene como hidden input)
            const idProducto = $('idProducto')?.value;
            if (idProducto) {
                await onProductoChange(idProducto);
                await loadTallaPrecios(idProducto);
                toggleNumeroByProducto();
            }

            // Eventos filas
            $('btnAddRow')?.addEventListener('click', agregarFila);
            $('tbodyItems')?.addEventListener('click', (e) => {
                const btn = e.target.closest('.btnRemoveRow');
                if (btn) eliminarFila(btn);
            });

            // Recalcular al cambiar talla/cantidad
            document.addEventListener('change', (e) => {
                if (e.target.classList.contains('sel-talla') || e.target.classList.contains('inp-cantidad')) {
                    recalcTotales();
                }
            });

            // Pago
            $('tipoPago')?.addEventListener('change', () => {
                const tipo = $('tipoPago')?.value;
                const gEf = $('efectivoGroup');
                const gEx = $('efectivoExactoGroup');
                if (tipo === 'efectivo') {
                    gEf && (gEf.style.display = 'block');
                    gEx && (gEx.style.display = 'block');
                } else {
                    gEf && (gEf.style.display = 'none');
                    gEx && (gEx.style.display = 'none');
                    $('efectivoExacto') && ($('efectivoExacto').checked = false);
                    $('efectivoRecibido') && ($('efectivoRecibido').value = '');
                }
                recalcTotales();
            });
            $('montoAdelanto')?.addEventListener('input', recalcTotales);
            $('efectivoRecibido')?.addEventListener('input', recalcTotales);
            $('efectivoExacto')?.addEventListener('change', recalcTotales);

            // Cliente
            $('clienteSelect')?.addEventListener('change', function() {
                const v = String(this.value || '');
                const tipo = $('tipoCliente'),
                    idCli = $('idCliente'),
                    idEst = $('idEstablecimiento');
                if (!tipo || !idCli || !idEst) return;
                if (v.startsWith('natural:')) {
                    tipo.value = 'natural';
                    idCli.value = v.split(':')[1] || '';
                    idEst.value = '';
                } else if (v.startsWith('establecimiento:')) {
                    tipo.value = 'establecimiento';
                    idEst.value = v.split(':')[1] || '';
                    idCli.value = '';
                } else {
                    tipo.value = '';
                    idCli.value = '';
                    idEst.value = '';
                }
            });

            $('clienteFilter')?.addEventListener('input', function() {
                const q = this.value.toLowerCase();
                document.querySelectorAll('#clienteSelect option').forEach(opt => {
                    if (!opt.value) return; // placeholder
                    opt.style.display = opt.textContent.toLowerCase().includes(q) ? '' : 'none';
                });
            });

            // Submit
            $('formNuevoPedido')?.addEventListener('submit', (e) => {
                const cli = $('clienteSelect');
                if (!cli?.value) {
                    e.preventDefault();
                    alert('Selecciona un cliente.');
                    cli?.focus();
                }
                const filas = document.querySelectorAll('.item-row');
                if (!filas.length) {
                    e.preventDefault();
                    alert('Agrega al menos una prenda.');
                    return;
                }
                let ok = true;
                filas.forEach(f => {
                    const s = f.querySelector('.sel-talla');
                    if (!s?.value) ok = false;
                });
                if (!ok) {
                    e.preventDefault();
                    alert('Selecciona una talla en todas las filas.');
                }
            });

            // Primer cálculo / estado UI
            actualizarBotonesEliminar();
            recalcTotales();
        });
    })();
</script>
@endpush