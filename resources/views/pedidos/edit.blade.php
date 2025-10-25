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

@endpush