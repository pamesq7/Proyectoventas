@extends('layouts.app')

@section('title', 'Editar Pago')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">
        <i class="fas fa-edit text-primary me-2"></i>
        Editar Pago
    </h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('ventas.index') }}">Ventas</a></li>
        <li class="breadcrumb-item"><a href="{{ route('ventas.show', $transaccion->venta->idVenta) }}">Venta #{{ $transaccion->venta->idVenta }}</a></li>
        <li class="breadcrumb-item active">Editar Pago</li>
    </ol>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-money-bill-wave me-1"></i> Información del Pago</h6>
                </div>
                <div class="card-body">
                    <!-- Información de contexto -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded">
                                <h6 class="text-muted mb-2">Venta Asociada</h6>
                                <div><strong>ID Venta:</strong> #{{ $transaccion->venta->idVenta }}</div>
                                <div><strong>Cliente:</strong> {{ $transaccion->venta->nombre_cliente }}</div>
                                <div><strong>Total Venta:</strong> Bs. {{ number_format($transaccion->venta->total, 2) }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded">
                                <h6 class="text-muted mb-2">Estado Actual</h6>
                                <div><strong>Saldo Actual:</strong> Bs. {{ number_format($transaccion->venta->saldo, 2) }}</div>
                                <div><strong>Pagos Totales:</strong> Bs. {{ number_format($transaccion->venta->monto_pagado, 2) }}</div>
                                <div><strong>Porcentaje Pagado:</strong> {{ number_format($transaccion->venta->porcentaje_pagado, 1) }}%</div>
                            </div>
                        </div>
                    </div>

                    <form id="formEditarPago" method="POST" action="{{ route('pagos.update', $transaccion->idTransaccion) }}">
                        @csrf
                        @method('PUT')

                        <!-- Información de solo lectura -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">ID Transacción</label>
                                <input type="text" class="form-control" value="#{{ $transaccion->idTransaccion }}" readonly>
                                <div class="form-text">No se puede modificar</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Venta Asociada</label>
                                <input type="text" class="form-control" value="#{{ $transaccion->venta->idVenta }}" readonly>
                                <div class="form-text">No se puede modificar</div>
                            </div>
                        </div>

                        <!-- Campos editables -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Monto Actual</label>
                                <div class="input-group">
                                    <span class="input-group-text">Bs.</span>
                                    <input type="text" class="form-control" value="{{ number_format($transaccion->monto, 2) }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Nuevo Monto *</label>
                                <div class="input-group">
                                    <span class="input-group-text">Bs.</span>
                                    <input type="number" name="monto" id="monto" class="form-control"
                                           step="0.01" min="0.01" max="{{ $transaccion->venta->saldo + $transaccion->monto }}"
                                           value="{{ $transaccion->monto }}" required>
                                </div>
                                <div class="form-text">Máximo permitido: Bs. {{ number_format($transaccion->venta->saldo + $transaccion->monto, 2) }}</div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Método de Pago Actual</label>
                                <input type="text" class="form-control" value="{{ $transaccion->metodoPago }}" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Nuevo Método de Pago *</label>
                                <select name="metodoPago" id="metodoPago" class="form-select" required>
                                    <option value="">Seleccionar método...</option>
                                    @foreach($metodosPago as $metodo)
                                        <option value="{{ $metodo['codigo'] }}" {{ $transaccion->metodoPago == $metodo['codigo'] ? 'selected' : '' }}>
                                            {{ $metodo['nombre'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Fecha Actual</label>
                                <input type="text" class="form-control" value="{{ $transaccion->created_at->format('d/m/Y H:i') }}" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Nueva Fecha *</label>
                                <input type="datetime-local" name="fecha_pago" id="fecha_pago" class="form-control"
                                       value="{{ $transaccion->created_at->format('Y-m-d\TH:i') }}" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Observaciones Actuales</label>
                            <textarea class="form-control" rows="2" readonly>{{ $transaccion->observaciones }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nuevas Observaciones</label>
                            <textarea name="observaciones" id="observaciones" class="form-control" rows="2"
                                      placeholder="Referencia, voucher, notas...">{{ $transaccion->observaciones }}</textarea>
                        </div>

                        <!-- Información de validación -->
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Información importante:</strong>
                            <ul class="mb-0 mt-2">
                                <li>El saldo de la venta se recalculará automáticamente</li>
                                <li>Los cambios quedan registrados en el historial</li>
                                <li>No se puede editar el ID de transacción ni la venta asociada</li>
                            </ul>
                        </div>

                        <div class="d-flex gap-2">
                            <a href="{{ route('ventas.show', $transaccion->venta->idVenta) }}" class="btn btn-secondary">
                                <i class="fas fa-times me-1"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Guardar Cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Información de ayuda -->
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0"><i class="fas fa-question-circle me-1"></i> Ayuda</h6>
                </div>
                <div class="card-body">
                    <h6>¿Qué se puede editar?</h6>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-check text-success me-2"></i>Monto del pago</li>
                        <li><i class="fas fa-check text-success me-2"></i>Método de pago</li>
                        <li><i class="fas fa-check text-success me-2"></i>Observaciones</li>
                        <li><i class="fas fa-check text-success me-2"></i>Fecha del pago</li>
                    </ul>

                    <h6>¿Qué NO se puede editar?</h6>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-times text-danger me-2"></i>ID de transacción</li>
                        <li><i class="fas fa-times text-danger me-2"></i>Venta asociada</li>
                        <li><i class="fas fa-times text-danger me-2"></i>Usuario que registró</li>
                    </ul>

                    <hr>
                    <div class="alert alert-warning">
                        <strong>Nota:</strong> Los cambios afectan el saldo de la venta asociada.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Formulario de edición con AJAX
    $('#formEditarPago').on('submit', function(e) {
        e.preventDefault();

        const $form = $(this);
        const $submit = $form.find('button[type="submit"]');
        const $montoInput = $('#monto');

        // Validación básica del lado del cliente
        const monto = parseFloat($montoInput.val()) || 0;
        const metodo = $('#metodoPago').val();

        if (monto <= 0) {
            alert('El monto debe ser mayor a 0.');
            $montoInput.focus();
            return;
        }

        if (!metodo) {
            alert('Debe seleccionar un método de pago.');
            $('#metodoPago').focus();
            return;
        }

        // Deshabilitar botón mientras se procesa
        $submit.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Guardando...');

        $.ajax({
            url: $form.attr('action'),
            method: 'POST',
            data: $form.serialize(),
            success: function(response) {
                if (response.success) {
                    // Mostrar mensaje de éxito
                    const alertHtml = `
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            <strong>¡Éxito!</strong> ${response.message}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    `;
                    $form.before(alertHtml);

                    // Actualizar información mostrada
                    $('.card-body').first().find('input[readonly]').first().next().val('Bs. ' + response.data.monto_actual);
                    $('.bg-light').first().find('strong').eq(2).text('Bs. ' + response.data.saldo_actual);

                    // Redirigir después de 2 segundos
                    setTimeout(() => {
                        window.location.href = '{{ route("ventas.show", $transaccion->venta->idVenta) }}';
                    }, 2000);
                }
            },
            error: function(xhr) {
                let message = 'Error al actualizar el pago.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }

                const alertHtml = `
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Error:</strong> ${message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                `;
                $form.before(alertHtml);

                // Rehabilitar botón
                $submit.prop('disabled', false).html('<i class="fas fa-save me-1"></i> Guardar Cambios');
            }
        });
    });

    // Validación en tiempo real del monto
    $('#monto').on('input', function() {
        const monto = parseFloat($(this).val()) || 0;
        const maximo = parseFloat($(this).attr('max')) || 0;

        if (monto > maximo) {
            $(this).addClass('is-invalid');
            $('.form-text').addClass('text-danger').text(`El monto no puede exceder Bs. ${maximo.toFixed(2)}`);
        } else {
            $(this).removeClass('is-invalid');
            $('.form-text').removeClass('text-danger').text(`Máximo permitido: Bs. ${maximo.toFixed(2)}`);
        }
    });
});
</script>
@endpush
