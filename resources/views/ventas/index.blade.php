@extends('layouts.app')

@section('title', 'Gestión de Ventas')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">
                        <i class="fas fa-shopping-cart"></i> Gestión de Ventas
                    </h3>
                    <div class="btn-group">

                        <a href="{{ route('ventas.morosos') }}" class="btn btn-warning">
                            <i class="fas fa-exclamation-triangle"></i> Clientes Morosos
                        </a>
                    </div>
                </div>

                <!-- Filtros -->
                <div class="card-body">
                    <form method="GET" action="{{ route('ventas.index') }}" class="mb-4">
                        <div class="row">
                            <div class="col-md-2">
                                <label for="fecha_desde">Desde:</label>
                                <input type="date" name="fecha_desde" id="fecha_desde" class="form-control"
                                    value="{{ request('fecha_desde') }}">
                            </div>
                            <div class="col-md-2">
                                <label for="fecha_hasta">Hasta:</label>
                                <input type="date" name="fecha_hasta" id="fecha_hasta" class="form-control"
                                    value="{{ request('fecha_hasta') }}">
                            </div>
                            <div class="col-md-2">
                                <label for="estado_pago">Estado de Pago:</label>
                                <select name="estado_pago" id="estado_pago" class="form-control">
                                    <option value="">Todos</option>
                                    <option value="saldado" {{ request('estado_pago') == 'saldado' ? 'selected' : '' }}>Saldado</option>
                                    <option value="pendiente" {{ request('estado_pago') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                                </select>
                            </div>

                            <div class="col-md-2">
                                <label for="tipo_cliente">Tipo Cliente:</label>
                                <select name="tipo_cliente" id="tipo_cliente" class="form-control">
                                    <option value="">Todos</option>
                                    <option value="natural" {{ request('tipo_cliente') == 'natural' ? 'selected' : '' }}>Natural</option>
                                    <option value="establecimiento" {{ request('tipo_cliente') == 'establecimiento' ? 'selected' : '' }}>Establecimiento</option>
                                </select>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-info mr-2">
                                    <i class="fas fa-search"></i> Filtrar
                                </button>
                                <a href="{{ route('ventas.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Limpiar
                                </a>
                            </div>
                        </div>
                    </form>

                    <!-- Estadísticas -->
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <div class="info-box bg-info">
                                <span class="info-box-icon"><i class="fas fa-shopping-cart"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Ventas</span>
                                    <span class="info-box-number">{{ $estadisticas['total_ventas'] }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box bg-success">
                                <span class="info-box-icon"><i class="fas fa-check-circle"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Saldadas</span>
                                    <span class="info-box-number">{{ $estadisticas['ventas_saldadas'] }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box bg-warning">
                                <span class="info-box-icon"><i class="fas fa-clock"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Pendientes</span>
                                    <span class="info-box-number">{{ $estadisticas['ventas_pendientes'] }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box bg-danger">
                                <span class="info-box-icon"><i class="fas fa-dollar-sign"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Monto Pendiente</span>
                                    <span class="info-box-number">Bs. {{ number_format($estadisticas['monto_pendiente'], 2) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tabla de ventas -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="thead-dark">
                                <tr>
                                    <th>N°</th>
                                    <th>Fecha</th>
                                    <th>Cliente</th>
                                    <th>Total</th>
                                    <th>Estado Pago</th>
                                    <th>Vendedor</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $contador = ($ventas->currentPage() - 1) * $ventas->perPage() + 1; @endphp
                                @forelse($ventas as $venta)
                                <tr>
                                    <td>{{ $contador++ }}</td>
                                    <td>{{ $venta->created_at->format('d/m/Y') }}</td>
                                    <td>
                                        <strong>{{ $venta->nombre_cliente }}</strong>
                                        <br><small class="text-muted">{{ $venta->tipo_cliente }}</small>
                                    </td>
                                    <td>
                                        <strong>Bs. {{ number_format($venta->total, 2) }}</strong>
                                        @if($venta->estado_pago == 'PARCIAL')
                                        <br><small class="text-muted">Pagado: Bs. {{ number_format($venta->monto_pagado, 2) }}</small>
                                        <br><small class="text-danger">Debe: Bs. {{ number_format($venta->saldo, 2) }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($venta->estado_pago == 'PAGADO')
                                        <span class="badge badge-success">🟢 PAGADO</span>
                                        @elseif($venta->estado_pago == 'PARCIAL')
                                        <span class="badge badge-warning">🟡 PARCIAL</span>
                                        <br><small>{{ $venta->porcentaje_pagado }}%</small>
                                        @else
                                        <span class="badge badge-danger">🔴 PENDIENTE</span>
                                        @if($venta->dias_atraso > 0)
                                        <br><small class="text-danger">{{ $venta->dias_atraso }} días</small>
                                        @endif
                                        @endif
                                    </td>
                                    <td>
                                        <strong>{{ $venta->nombre_empleado }}</strong>
                                    </td>

                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('ventas.show', $venta->idVenta) }}"
                                                class="btn btn-info" title="Ver detalles">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if($venta->puedeRecibirPagos())
                                            <button type="button"
                                                class="btn btn-success"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalPago"
                                                data-venta-id="{{ $venta->idVenta }}"
                                                data-cliente="{{ $venta->nombre_cliente }}"
                                                data-saldo="{{ $venta->saldo }}"
                                                title="Registrar pago">
                                                <i class="fas fa-dollar-sign"></i>
                                            </button>
                                            @endif
                                            @if($venta->puedeSerAnulada())
                                            <button type="button" class="btn btn-warning btn-anular"
                                                data-venta-id="{{ $venta->idVenta }}"
                                                title="Anular venta">
                                                <i class="fas fa-ban"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center">No se encontraron ventas</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginación -->
                    <div class="d-flex justify-content-center">
                        {{ $ventas->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Estilos para la tabla -->
<style>
    .table {
        color: #000000;
        /* Texto negro */
        font-size: 0.9rem;
    }

    .table thead th {
        background-color: #343a40;
        color: white;
        padding: 8px 12px;
        vertical-align: middle;
    }

    .table tbody td {
        padding: 6px 12px;
        vertical-align: middle;
    }

    .table-striped tbody tr:nth-of-type(odd) {
        background-color: rgba(0, 0, 0, 0.02);
    }

    .table-hover tbody tr:hover {
        background-color: rgba(0, 0, 0, 0.05);
    }

    /* Ajuste para los botones de acción */
    .btn-group-sm>.btn,
    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.8rem;
    }

    /* Ajuste para los badges de estado */
    .badge-success,
    .badge-warning,
    .badge-danger {
        color: #000000 !important;
        /* Texto negro */
        font-weight: 500;
    }

    .badge-success {
        background-color: #a3e4a3;
    }

    /* Verde claro */
    .badge-warning {
        background-color: #f9e79f;
    }

    /* Amarillo claro */
    .badge-danger {
        background-color: #f5b7b1;
    }

    /* Rojo claro */

    /* Ajuste para los textos pequeños */
    small {
        font-size: 0.8rem;
    }
</style>

<!-- Modal para registrar pago -->
<div class="modal fade" id="modalPago" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Registrar Pago</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formPago" method="POST" action="{{ route('ventas.store') }}">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="idVenta" id="ventaId">
                    <input type="hidden" name="tipoTransaccion" value="pago">

                    <div class="form-group">
                        <label>Cliente:</label>
                        <p id="clienteNombre" class="font-weight-bold"></p>
                    </div>

                    <div class="form-group">
                        <label>Saldo Pendiente:</label>
                        <p id="saldoPendiente" class="font-weight-bold text-danger"></p>
                    </div>

                    <div class="form-group">
                        <label for="monto">Monto a Pagar: *</label>
                        <input type="number" name="monto" id="monto" class="form-control"
                            step="0.01" min="0.01" required>
                    </div>

                    <div class="form-group">
                        <label for="metodoPago">Método de Pago: *</label>
                        <select name="metodoPago" id="metodoPago" class="form-control" required>
                            <option value="">Seleccionar...</option>
                            <option value="efectivo">Efectivo</option>
                            <option value="tarjeta">Tarjeta</option>
                            <option value="transferencia">Transferencia</option>
                            <option value="yape">Yape</option>
                            <option value="plin">Plin</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="observaciones">Observaciones/Serie:</label>
                        <textarea name="observaciones" id="observaciones" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Registrar Pago</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Poblar datos del modal cuando se abre
    const modalEl = document.getElementById('modalPago');
    if (modalEl) {
        modalEl.addEventListener('show.bs.modal', function(event) {
            // Botón que disparó el modal
            const button = event.relatedTarget;

            // Extraer datos del botón
            const ventaId = button.getAttribute('data-venta-id');
            const cliente = button.getAttribute('data-cliente');
            const saldo = parseFloat(button.getAttribute('data-saldo')) || 0;

            // Actualizar campos del modal
            document.getElementById('ventaId').value = ventaId;
            document.getElementById('clienteNombre').textContent = cliente;
            document.getElementById('saldoPendiente').textContent = 'Bs. ' + saldo.toFixed(2);
            document.getElementById('monto').setAttribute('max', saldo);
            document.getElementById('monto').value = saldo; // Prellenar con saldo completo
        });

        // Limpiar formulario al cerrar
        modalEl.addEventListener('hidden.bs.modal', function() {
            const form = document.getElementById('formPago');
            if (form) form.reset();
        });
    }

    // Enviar pago por AJAX para evitar redirección a ventas.show
    $(function() {
        $('#formPago').on('submit', function(e) {
            e.preventDefault();
            const $form = $(this);
            const url = $form.attr('action');
            const data = $form.serialize();

            // Validar datos básicos antes de enviar
            const monto = parseFloat($('#monto').val()) || 0;
            const metodo = $('#metodoPago').val();
            const ventaId = $('#ventaId').val();

            if (!ventaId || !metodo || monto <= 0) {
                alert('Por favor complete todos los campos requeridos.');
                return;
            }

            // Deshabilitar botón mientras se envía
            const $submit = $form.find('button[type="submit"]');
            $submit.prop('disabled', true).text('Guardando...');

            $.ajax({
                url: url,
                method: 'POST',
                data: data,
                success: function() {
                    // Cerrar modal y recargar lista
                    if (window.bootstrap && bootstrap.Modal) {
                        const modalInstance = bootstrap.Modal.getInstance(document.getElementById('modalPago'));
                        if (modalInstance) modalInstance.hide();
                    } else if (typeof $('#modalPago').modal === 'function') {
                        $('#modalPago').modal('hide');
                    }
                    // Pago registrado correctamente
                    setTimeout(function() {
                        location.reload();
                    }, 500);
                },
                error: function(xhr) {
                    // Mostrar errores específicos, pero solo si son errores reales
                    let msg = 'No se pudo registrar el pago.';
                    if (xhr.status === 419) {
                        msg = 'Sesión expirada. Recarga la página.';
                    } else if (xhr.status === 422 && xhr.responseJSON) {
                        msg = 'Datos inválidos. Verifica el monto y método de pago.';
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }

                    alert(msg);
                },
                complete: function() {
                    $submit.prop('disabled', false).html('<i class="fas fa-check"></i> Registrar Pago');
                }
            });
        });
    });
</script>
@endpush