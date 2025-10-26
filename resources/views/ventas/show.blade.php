@extends('layouts.app')

@section('title', 'Detalle de Venta')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">
        <i class="fas fa-receipt text-primary me-2"></i>
        Detalle de Venta #{{ $venta->idVenta }}
    </h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('ventas.index') }}">Gestión de Ventas</a></li>
        <li class="breadcrumb-item active">Venta #{{ $venta->idVenta }}</li>
    </ol>

    <div class="row">
        <div class="col-lg-8">
            <!-- Información de la venta -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-info-circle me-1"></i> Información de la Venta</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label"><strong>Cliente:</strong></label>
                                <div>{{ $venta->nombre_cliente }}</div>
                                <small class="text-muted">{{ $venta->tipo_cliente }}</small>
                            </div>
                            <div class="mb-3">
                                <label class="form-label"><strong>Vendedor:</strong></label>
                                <div>{{ $venta->nombre_empleado ?? 'No asignado' }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label"><strong>Fecha:</strong></label>
                                <div>{{ $venta->created_at->format('d/m/Y H:i') }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label"><strong>Total:</strong></label>
                                <div class="h5 text-primary">Bs. {{ number_format($venta->total, 2) }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label"><strong>Estado de Pago:</strong></label>
                                <div>
                                    @if($venta->estado_pago == 'PAGADO')
                                    <span class="badge bg-success">🟢 PAGADO</span>
                                    @elseif($venta->estado_pago == 'PARCIAL')
                                    <span class="badge bg-warning">🟡 PARCIAL</span>
                                    <div class="mt-1">
                                        <small>Pagado: Bs. {{ number_format($venta->monto_pagado, 2) }}</small><br>
                                        <small class="text-danger">Debe: Bs. {{ number_format($venta->saldo, 2) }}</small>
                                    </div>
                                    @else
                                    <span class="badge bg-danger">🔴 PENDIENTE</span>
                                    @if($venta->dias_atraso > 0)
                                    <div class="mt-1"><small class="text-danger">{{ $venta->dias_atraso }} días de atraso</small></div>
                                    @endif
                                    @endif
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label"><strong>Estado del Pedido:</strong></label>
                                <div>{{ $venta->estado_texto }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detalles de la venta -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-list me-1"></i> Detalles del Pedido</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th>Talla</th>
                                    <th>Cantidad</th>
                                    <th>Precio Unit.</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($venta->detalleVentas as $detalle)
                                <tr>
                                    <td>{{ $detalle->descripcion ?? 'Producto personalizado' }}</td>
                                    <td>{{ $detalle->talla->nombre ?? '-' }}</td>
                                    <td>{{ $detalle->cantidad }}</td>
                                    <td>Bs. {{ number_format($detalle->precioUnitario, 2) }}</td>
                                    <td>Bs. {{ number_format($detalle->cantidad * $detalle->precioUnitario, 2) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center">No hay detalles disponibles</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Transacciones/Pagos -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="fas fa-money-bill-wave me-1"></i> Historial de Pagos</h6>
                    @if($venta->puedeRecibirPagos())
                    <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#modalPago">
                        <i class="fas fa-plus me-1"></i> Registrar Pago
                    </button>
                    @endif
                </div>
                <div class="card-body">
                    @if($venta->transacciones->isNotEmpty())
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Monto</th>
                                    <th>Método</th>
                                    <th>Estado</th>
                                    <th>Observaciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($venta->transacciones->sortByDesc('created_at') as $transaccion)
                                <tr>
                                    <td>{{ $transaccion->created_at->format('d/m/Y H:i') }}</td>
                                    <td>Bs. {{ number_format($transaccion->monto, 2) }}</td>
                                    <td>{{ $transaccion->metodoPago }}</td>
                                    <td>
                                        @if($transaccion->estado == 1)
                                        <span class="badge bg-success">Confirmado</span>
                                        @else
                                        <span class="badge bg-secondary">Anulado</span>
                                        @endif
                                    </td>
                                    <td>{{ $transaccion->observaciones ?? '-' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Aún no hay pagos registrados para esta venta.
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Información adicional -->
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0"><i class="fas fa-chart-bar me-1"></i> Resumen Financiero</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Total Venta:</span>
                        <strong>Bs. {{ number_format($venta->total, 2) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Total Pagado:</span>
                        <strong>Bs. {{ number_format($venta->monto_pagado, 2) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Saldo Pendiente:</span>
                        <strong class="text-{{ $venta->saldo > 0 ? 'danger' : 'success' }}">
                            Bs. {{ number_format($venta->saldo, 2) }}
                        </strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Porcentaje Pagado:</span>
                        <strong>{{ number_format($venta->porcentaje_pagado, 1) }}%</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para registrar pago (igual que en ventas.index) -->
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
                    <input type="hidden" name="idVenta" id="ventaId" value="{{ $venta->idVenta }}">

                    <div class="form-group">
                        <label>Cliente:</label>
                        <p class="font-weight-bold">{{ $venta->nombre_cliente }}</p>
                    </div>

                    <div class="form-group">
                        <label>Saldo Pendiente:</label>
                        <p class="font-weight-bold text-danger">Bs. {{ number_format($venta->saldo, 2) }}</p>
                    </div>

                    <div class="form-group">
                        <label for="monto">Monto a Pagar: *</label>
                        <input type="number" name="monto" id="monto" class="form-control"
                            step="0.01" min="0.01" max="{{ $venta->saldo }}" required>
                    </div>

                    <div class="mb-2">
                        <label class="form-label">Tipo Pago *</label>
                        <select class="form-select" id="metodoPago" name="metodoPago">
                            <option value="efectivo" selected>Efectivo</option>
                            <option value="qr">QR</option>
                            <option value="cheque">Cheque</option>
                            <option value="transferencia">Transferencia bancaria</option>
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
                if (window.showNotification) {
                    window.showNotification('error', 'Por favor complete todos los campos requeridos.');
                }
                return;
            }

            // Deshabilitar botón mientras se envía
            const $submit = $form.find('button[type="submit" ]');
            $submit.prop('disabled', true).text('Guardando...');

            $.ajax({
                url: url,
                method: 'POST',
                data: data,
                success: function() {
                    // Cerrar modal y recargar página
                    if (window.bootstrap && bootstrap.Modal) {
                        const modalInstance = bootstrap.Modal.getInstance(document.getElementById('modalPago'));
                        if (modalInstance) modalInstance.hide();
                    } else if (typeof $('#modalPago').modal === 'function') {
                        $('#modalPago').modal('hide');
                    }
                    // Recargar página para mostrar cambios
                    location.reload();
                },
                error: function(xhr) {
                    // Mostrar errores específicos
                    let msg = 'No se pudo registrar el pago.';
                    if (xhr.status === 419) {
                        msg = 'Sesión expirada. Recarga la página.';
                    } else if (xhr.status === 422 && xhr.responseJSON) {
                        msg = 'Datos inválidos. Verifica el monto y método de pago.';
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }

                    if (window.showNotification) {
                        window.showNotification('error', msg);
                    }
                },
                complete: function() {
                    $submit.prop('disabled', false).html('<i class="fas fa-check"></i> Registrar Pago');
                }
            });
        });
    });
</script>
@endpush