@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Editar Pedido #{{ $pedido->idVenta }}</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('pedidos.index') }}">Pedidos</a></li>
        <li class="breadcrumb-item active">Editar</li>
    </ol>

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i>
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if($errors->any())
    <div class="alert alert-danger">
        <h6 class="mb-1"><i class="fas fa-exclamation-circle me-1"></i>Errores de validación:</h6>
        <ul class="mb-0">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- =========================================================
         TARJETA: RESUMEN
       ========================================================= --}}
    <div class="card mb-3">
        <div class="card-body">
            <div class="row g-3">
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
                        $nombreCliente = $pedido->clienteEstablecimiento->razonSocial ?? 'Establecimiento';
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
        </div>
    </div>

    {{-- =========================================================
         FORM #1: DATOS BÁSICOS (pedidos.update)
       ========================================================= --}}
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div><i class="fas fa-edit me-1"></i>Datos del pedido</div>
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
            <form action="{{ route('pedidos.update', $pedido->idVenta) }}" method="POST" enctype="multipart/form-data" class="row g-3">
                @csrf
                @method('PUT')

                {{-- ✅ Cliente en el formato que consume update(): natural:ID / establecimiento:ID --}}
                <div class="col-md-12">
                    <label for="tipoClienteSelect" class="form-label">Cliente *</label>
                    <select name="tipoCliente" id="tipoClienteSelect" class="form-select" required>
                        <option value="">Seleccionar cliente</option>

                        @php
                        $currentNatural = $pedido->idCliente ? 'natural:'.$pedido->idCliente : null;
                        $currentEst = $pedido->idEstablecimiento ? 'establecimiento:'.$pedido->idEstablecimiento : null;
                        $oldTC = old('tipoCliente', $currentNatural ?? $currentEst ?? '');
                        @endphp

                        <optgroup label="Clientes naturales">
                            @foreach($clientesNaturales as $cliente)
                            @php $val = 'natural:'.$cliente->idCliente; @endphp
                            <option value="{{ $val }}" {{ $oldTC === $val ? 'selected' : '' }}>
                                {{ $cliente->user->name }} {{ $cliente->user->primerApellido }}
                            </option>
                            @endforeach
                        </optgroup>

                        <optgroup label="Establecimientos">
                            @foreach($clientesEstablecimientos as $e)
                            @php $val = 'establecimiento:'.$e->idEstablecimiento; @endphp
                            <option value="{{ $val }}" {{ $oldTC === $val ? 'selected' : '' }}>
                                {{ $e->razonSocial }}
                            </option>
                            @endforeach
                        </optgroup>
                    </select>
                </div>

                <div class="col-md-4">
                    <label for="fechaEntrega" class="form-label">Fecha de entrega *</label>
                    <input type="date"
                        id="fechaEntrega"
                        name="fechaEntrega"
                        class="form-control"
                        value="{{ old('fechaEntrega', $pedido->fechaEntrega ? $pedido->fechaEntrega->format('Y-m-d') : '') }}"
                        required>
                </div>

                <div class="col-md-5">
                    <label for="lugarEntrega" class="form-label">Lugar de entrega *</label>
                    <input type="text"
                        id="lugarEntrega"
                        name="lugarEntrega"
                        class="form-control"
                        maxlength="200"
                        value="{{ old('lugarEntrega', $pedido->lugarEntrega) }}"
                        required>
                </div>

                <div class="col-md-3">
                    <label for="estadoPedido" class="form-label">Estado *</label>
                    <select id="estadoPedido" name="estadoPedido" class="form-select" required>
                        @foreach($estados as $k => $label)
                        <option value="{{ $k }}" {{ old('estadoPedido', (string)$pedido->estadoPedido) === (string)$k ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Asignar Diseñador *</label>

                    <select name="idEmpleado" class="form-select" required>
                        <option value="">Seleccionar diseñador</option>

                        @foreach($diseñadores as $d)
                        <option value="{{ $d->idEmpleado }}"
                            {{ old('idEmpleado', $pedido->idEmpleado) == $d->idEmpleado ? 'selected' : '' }}>
                            {{ $d->user->name }} {{ $d->user->primerApellido }}
                            {{ $d->user->segundApellido ?? '' }}
                        </option>
                        @endforeach
                    </select>

                    <small class="text-muted">Diseñador responsable de este pedido</small>
                </div>
                {{-- Imagen del pedido (muestra actual si existe) --}}
                <div class="col-12">
                    <label for="imagenPedido" class="form-label">Imagen del Pedido</label>
                    <div class="imagen-container">
                        <input type="file" id="imagenPedido" name="imagenPedido" class="form-control" accept="image/*">
                        @if($pedido->disenos && $pedido->disenos->first() && $pedido->disenos->first()->archivo)
                        <div class="mt-2 position-relative" style="display:inline-block;">
                            <img src="{{ asset('storage/' . $pedido->disenos->first()->archivo) }}"
                                alt="Imagen del pedido"
                                class="img-thumbnail"
                                style="max-width:200px;height:auto;">
                            <button type="button"
                                class="btn btn-sm btn-outline-danger position-absolute"
                                style="top:5px;right:5px;padding:2px 6px;"
                                data-id="{{ $pedido->idVenta }}"
                                onclick="confirmDeleteImage(this)">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        @endif
                    </div>

                    @if($pedido->disenos && $pedido->disenos->first() && $pedido->disenos->first()->archivo)
                    <div class="form-check mt-2">
                        <input class="form-check-input" type="checkbox" id="delete_imagen" name="delete_imagen" value="1" style="display:none;">
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
                </div>
            </form>
        </div>
    </div>

    {{-- =========================================================
         FORM #2: DETALLES (pedidos.update-detalles)
       ========================================================= --}}
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-list me-2"></i>Detalles del pedido</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('pedidos.update-detalles', $pedido->idVenta) }}" method="POST" id="form-detalles">
                @csrf
                @method('PUT')

                <div id="detalles-bloques">
                    <div class="bloque-detalle" data-index="0">
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Producto (precarga precio por talla en filas nuevas) *</label>
                                {{-- ✅ name="idProducto" requerido por updateDetalles() --}}
                                <select name="idProducto" class="form-select form-select-sm productoSelector" required>
                                    <option value="">Seleccionar producto</option>
                                    @php
                                    $productoSeleccionado = optional($pedido->detalleVentas->first())->producto->idProducto ?? null;
                                    @endphp

                                    @foreach(($productos ?? []) as $p)
                                    <option value="{{ $p->idProducto }}"
                                        {{ old('idProducto', $productoSeleccionado) == $p->idProducto ? 'selected' : '' }}>
                                        [#{{ $p->idProducto }}] {{ $p->nombre }}
                                    </option>

                                    @endforeach
                                </select>
                                <div class="form-text">Solo ayuda a rellenar precios de filas nuevas.</div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-sm align-middle tabla-detalles">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 12rem">Talla *</th>
                                        <th style="width: 7rem">Cant. *</th>
                                        <th style="width: 9rem">Precio *</th>
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
                                            <select name="idTalla[]" class="form-select form-select-sm tallaSel" required>
                                                @foreach(($tallas ?? []) as $t)
                                                <option value="{{ $t->idTalla }}" {{ $det->idTalla == $t->idTalla ? 'selected' : '' }}>{{ $t->nombre }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <input type="number" name="cantidad[]" class="form-control form-control-sm cantidad" min="1" value="{{ $det->cantidad }}" required>
                                        </td>
                                        <td>
                                            {{-- ✅ precioUnitario requerido por updateDetalles --}}
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
                                        <td class="text-end subtotal">
                                            {{ number_format($det->cantidad * $det->precioUnitario, 2) }}
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-outline-danger btn-sm btn-del" title="Eliminar fila">
                                                <i class="fas fa-trash"></i>
                                            </button>
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



                <div class="row g-3 mt-3">
                    <div class="col-md-4">
                        <label class="form-label">Tipo Pago</label>
                        <select name="tipoTransaccion" class="form-select form-select-sm">
                            <option value="">Sin registrar pago</option>
                            @foreach(($metodosPago ?? []) as $mp)
                            <option value="{{ $mp['codigo'] }}" {{ old('tipoTransaccion') === $mp['codigo'] ? 'selected':'' }}>
                                {{ $mp['nombre'] }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Adelanto</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Bs</span>
                            <input type="number" step="0.01" min="0" name="montoAdelanto" class="form-control" value="{{ old('montoAdelanto') ?? '' }}" placeholder="0.00">
                        </div>
                        <div class="form-text">Se registrará como pago y ajustará el saldo.</div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-2 bg-light rounded border">
                            <div class="d-flex justify-content-between"><span>Total</span><strong id="sum-total">0.00</strong></div>
                            <div class="d-flex justify-content-between"><span>Saldo actual</span><strong>{{ number_format($pedido->saldo, 2) }}</strong></div>
                        </div>
                    </div>
                </div>

                <input type="hidden" name="delete_ids" id="delete_ids_holder">

                <div class="d-flex justify-content-end mt-3">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save me-1"></i>Guardar detalles
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    (function() {
        const bloquesContainer = document.getElementById('detalles-bloques');
        const deleteIdsHolder = document.getElementById('delete_ids_holder');
        const btnAgregarBloque = document.getElementById('btn-agregar-bloque');
        let deleteIds = [];
        let bloqueCount = 1; // ya existe el bloque 0

        // mapa de precios por talla por bloque
        const preciosPorTallaByBlock = {}; // { index: { idTalla: precio } }

        function initBloqueEvents(bloque) {
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

            // Recalcular al cargar por si ya hay filas
            tbody.querySelectorAll('tr').forEach(tr => calcRowSubtotal(tr));
            calcTotalsGlobal();

            // inputs cantidad/precio
            tbody.addEventListener('input', function(e) {
                const tr = e.target.closest('tr');
                if (tr) calcRowSubtotal(tr);
            });

            // cambio de talla (solo filas nuevas sin row_id)
            tbody.addEventListener('change', function(e) {
                if (e.target.classList.contains('tallaSel')) {
                    const tr = e.target.closest('tr');
                    const rowId = tr.querySelector('input[name="row_id[]"]')?.value;
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

            // agregar fila
            const btnAdd = bloque.querySelector('.btn-agregar-fila');
            btnAdd.addEventListener('click', function() {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                <input type="hidden" name="row_id[]" value="">
                <td>
                    <select name="idTalla[]" class="form-select form-select-sm tallaSel" required>
                        @foreach(($tallas ?? []) as $t)
                            <option value="{{ $t->idTalla }}">{{ $t->nombre }}</option>
                        @endforeach
                    </select>
                </td>
                <td><input type="number" name="cantidad[]" class="form-control form-control-sm cantidad" min="1" value="1" required></td>
                <td><input type="number" step="0.01" name="precioUnitario[]" class="form-control form-control-sm precio" min="0" value="0.00" required></td>
                <td><input type="text" name="nombrePersonalizado[]" class="form-control form-control-sm"></td>
                <td><input type="text" name="numeroPersonalizado[]" class="form-control form-control-sm"></td>
                <td><input type="text" name="descripcion[]" class="form-control form-control-sm"></td>
                <td><input type="text" name="observacion[]" class="form-control form-control-sm"></td>
                <td class="text-end subtotal">0.00</td>
                <td class="text-center"><button type="button" class="btn btn-outline-danger btn-sm btn-del"><i class="fas fa-trash"></i></button></td>
            `;
                tbody.appendChild(tr);
            });

            // eliminar fila
            tbody.addEventListener('click', function(e) {
                const btn = e.target.closest('.btn-del');
                if (!btn) return;
                const tr = btn.closest('tr');
                const id = tr.querySelector('input[name="row_id[]"]').value;
                if (id) {
                    deleteIds.push(id);
                    deleteIdsHolder.value = deleteIds.join(',');
                    // evita enviar inputs de la fila eliminada
                    tr.querySelectorAll('input,select').forEach(el => {
                        el.name = el.name + '_ignored';
                    });
                }
                tr.remove();
                calcTotalsGlobal();
            });
        }

        function calcRowSubtotal(tr) {
            const q = parseFloat(tr.querySelector('.cantidad')?.value || '0');
            const p = parseFloat(tr.querySelector('.precio')?.value || '0');
            tr.querySelector('.subtotal').textContent = (q * p).toFixed(2);
        }

        function calcTotalsGlobal() {
            let sum = 0;
            document.querySelectorAll('.tabla-detalles tbody tr').forEach(tr => {
                const c = parseFloat(tr.querySelector('.cantidad')?.value || '0');
                const p = parseFloat(tr.querySelector('.precio')?.value || '0');
                sum += (c * p);
            });
            const out = document.getElementById('sum-total');
            if (out) out.textContent = sum.toFixed(2);
        }

        // init bloque 0
        initBloqueEvents(bloquesContainer.querySelector('.bloque-detalle[data-index="0"]'));

        // agregar bloque nuevo
        btnAgregarBloque.addEventListener('click', function() {
            const idx = bloqueCount++;
            const bloque = document.createElement('div');
            bloque.className = 'bloque-detalle';
            bloque.dataset.index = String(idx);
            bloque.innerHTML = `
            <hr class="my-3">
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label">Producto (precarga precio por talla en filas nuevas) *</label>
                    <select name="idProducto" class="form-select form-select-sm productoSelector" required>
                        <option value="">Seleccionar producto</option>
                        @foreach(($productos ?? []) as $p)
                            <option value="{{ $p->idProducto }}">[#{{ $p->idProducto }}] {{ $p->nombre }}</option>
                        @endforeach
                    </select>
                    <div class="form-text">Este bloque crea filas nuevas independientes del resto.</div>
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
                    <tbody></tbody>
                    <tfoot>
                        <tr>
                            <td colspan="9">
                                <button type="button" class="btn btn-sm btn-outline-primary btn-agregar-fila"><i class="fas fa-plus me-1"></i>Agregar fila</button>
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
        eliminarImagenAjax(idVenta);
    }

    function eliminarImagenAjax(idVenta) {
        fetch(`/pedidos/${idVenta}/eliminar-imagen`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(r => {
                if (!r.ok) throw new Error('HTTP ' + r.status);
                return r.json();
            })
            .then(data => {
                if (data.success) {
                    document.querySelector('.imagen-container').innerHTML = `
                <input type="file" id="imagenPedido" name="imagenPedido" class="form-control" accept="image/*">
                <div class="form-text mt-2">Sube una nueva imagen si lo deseas.</div>
            `;
                    if (window.showNotification) window.showNotification('success', 'Imagen eliminada correctamente');
                } else {
                    if (window.showNotification) window.showNotification('error', 'Error al eliminar la imagen: ' + data.message);
                    else alert('Error al eliminar la imagen: ' + data.message);
                }
            })
            .catch(err => {
                console.error(err);

                alert('Error al eliminar la imagen: ' + err.message);
            });
    }
</script>
@endpush