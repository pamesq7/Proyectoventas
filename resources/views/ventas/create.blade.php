@extends('layouts.app')

@section('title', 'Registrar Pago')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">
                        <i class="fas fa-dollar-sign"></i> Registrar Pago
                    </h3>
                    <a href="{{ route('ventas.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Volver a Ventas
                    </a>
                </div>

                <div class="card-body">
                    @if($ventasPendientes->count() > 0)
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            <strong>Selecciona una venta pendiente</strong> para registrar un pago.
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Cliente</th>
                                        <th>Total</th>
                                        <th>Pagado</th>
                                        <th>Saldo Pendiente</th>
                                        <th>Estado</th>
                                        <th>Acción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($ventasPendientes as $venta)
                                    <tr>
                                        <td>{{ $venta->created_at->format('d/m/Y') }}</td>
                                        <td>
                                            <strong>{{ $venta->nombre_cliente }}</strong>
                                            <br><small class="text-muted">{{ $venta->tipo_cliente }}</small>
                                        </td>
                                        <td>S/. {{ number_format($venta->total, 2) }}</td>
                                        <td>S/. {{ number_format($venta->monto_pagado, 2) }}</td>
                                        <td>
                                            <span class="font-weight-bold text-danger">
                                                S/. {{ number_format($venta->saldo, 2) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($venta->estado_pago == 'PARCIAL')
                                                <span class="badge badge-warning">🟡 PARCIAL</span>
                                                <br><small>{{ $venta->porcentaje_pagado }}%</small>
                                            @else
                                                <span class="badge badge-danger">🔴 PENDIENTE</span>
                                            @endif
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-success btn-sm btn-registrar-pago" 
                                                    data-venta-id="{{ $venta->idVenta }}"
                                                    data-cliente="{{ $venta->nombre_cliente }}"
                                                    data-saldo="{{ $venta->saldo }}"
                                                    data-total="{{ $venta->total }}">
                                                <i class="fas fa-dollar-sign"></i> Registrar Pago
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-success text-center">
                            <i class="fas fa-check-circle fa-3x mb-3"></i>
                            <h4>¡Excelente!</h4>
                            <p>No hay ventas pendientes de pago. Todas las ventas están saldadas.</p>
                            <a href="{{ route('ventas.index') }}" class="btn btn-primary">
                                <i class="fas fa-list"></i> Ver Todas las Ventas
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para registrar pago -->
<div class="modal fade" id="modalRegistrarPago" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-dollar-sign"></i> Registrar Pago
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form method="POST" action="{{ route('ventas.store') }}">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="idVenta" id="ventaIdModal">
                    <input type="hidden" name="tipoTransaccion" value="pago">
                    
                    <!-- Información de la venta -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">Información de la Venta</h6>
                                    <p><strong>Cliente:</strong> <span id="clienteModal"></span></p>
                                    <p><strong>Total:</strong> S/. <span id="totalModal"></span></p>
                                    <p><strong>Saldo Pendiente:</strong> 
                                        <span class="text-danger font-weight-bold">S/. <span id="saldoModal"></span></span>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <h6 class="card-title">Opciones de Pago</h6>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="tipoPago" id="pagoCompleto" value="completo" checked>
                                        <label class="form-check-label" for="pagoCompleto">
                                            Pago Completo
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="tipoPago" id="pagoParcial" value="parcial">
                                        <label class="form-check-label" for="pagoParcial">
                                            Pago Parcial
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Formulario de pago -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="monto">Monto a Pagar: *</label>
                                <input type="number" name="monto" id="monto" class="form-control" 
                                       step="0.01" min="0.01" required>
                                <small class="form-text text-muted">Ingrese el monto que el cliente está pagando</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="metodoPago">Método de Pago: *</label>
                                <select name="metodoPago" id="metodoPago" class="form-control" required>
                                    <option value="">Seleccionar método...</option>
                                    <option value="efectivo">💵 Efectivo</option>
                                    <option value="tarjeta">💳 Tarjeta de Crédito/Débito</option>
                                    <option value="transferencia">🏦 Transferencia Bancaria</option>
                                    <option value="yape">📱 Yape</option>
                                    <option value="plin">📱 Plin</option>
                                    <option value="deposito">🏧 Depósito Bancario</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="observaciones">Observaciones / Número de Serie:</label>
                        <textarea name="observaciones" id="observaciones" class="form-control" rows="3" 
                                  placeholder="Ej: Número de operación, serie de transferencia, observaciones adicionales..."></textarea>
                        <small class="form-text text-muted">
                            Para transferencias, incluye el número de operación. Para otros métodos, cualquier observación relevante.
                        </small>
                    </div>
                    
                    <!-- Resumen del pago -->
                    <div class="alert alert-light border" id="resumenPago" style="display: none;">
                        <h6><i class="fas fa-calculator"></i> Resumen del Pago</h6>
                        <div class="row">
                            <div class="col-md-4">
                                <strong>Monto a pagar:</strong><br>
                                <span class="text-success" id="montoPagar">S/. 0.00</span>
                            </div>
                            <div class="col-md-4">
                                <strong>Saldo restante:</strong><br>
                                <span class="text-danger" id="saldoRestante">S/. 0.00</span>
                            </div>
                            <div class="col-md-4">
                                <strong>Estado final:</strong><br>
                                <span id="estadoFinal" class="badge">-</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check"></i> Confirmar Pago
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
$(document).ready(function() {
    let saldoActual = 0;
    
    // Abrir modal de pago
    $('.btn-registrar-pago').click(function() {
        const ventaId = $(this).data('venta-id');
        const cliente = $(this).data('cliente');
        const saldo = parseFloat($(this).data('saldo'));
        const total = parseFloat($(this).data('total'));
        
        saldoActual = saldo;
        
        $('#ventaIdModal').val(ventaId);
        $('#clienteModal').text(cliente);
        $('#totalModal').text(total.toFixed(2));
        $('#saldoModal').text(saldo.toFixed(2));
        $('#monto').attr('max', saldo);
        $('#monto').val(saldo); // Por defecto, pago completo
        
        actualizarResumen();
        $('#modalRegistrarPago').modal('show');
    });
    
    // Manejar tipo de pago
    $('input[name="tipoPago"]').change(function() {
        if ($(this).val() === 'completo') {
            $('#monto').val(saldoActual);
        } else {
            $('#monto').val('');
        }
        actualizarResumen();
    });
    
    // Actualizar resumen cuando cambia el monto
    $('#monto').on('input', function() {
        actualizarResumen();
    });
    
    function actualizarResumen() {
        const monto = parseFloat($('#monto').val()) || 0;
        const saldoRestante = saldoActual - monto;
        
        $('#montoPagar').text('S/. ' + monto.toFixed(2));
        $('#saldoRestante').text('S/. ' + saldoRestante.toFixed(2));
        
        if (saldoRestante <= 0) {
            $('#estadoFinal').removeClass('badge-warning badge-danger').addClass('badge-success').text('🟢 PAGADO');
        } else if (monto > 0) {
            $('#estadoFinal').removeClass('badge-success badge-danger').addClass('badge-warning').text('🟡 PARCIAL');
        } else {
            $('#estadoFinal').removeClass('badge-success badge-warning').addClass('badge-danger').text('🔴 PENDIENTE');
        }
        
        $('#resumenPago').show();
    }
    
    // Limpiar modal al cerrar
    $('#modalRegistrarPago').on('hidden.bs.modal', function() {
        $(this).find('form')[0].reset();
        $('#resumenPago').hide();
        saldoActual = 0;
    });
    
    // Validación del formulario
    $('form').submit(function(e) {
        const monto = parseFloat($('#monto').val());
        if (monto <= 0) {
            e.preventDefault();
            alert('El monto debe ser mayor a 0');
            return false;
        }
        if (monto > saldoActual) {
            e.preventDefault();
            alert('El monto no puede ser mayor al saldo pendiente');
            return false;
        }
    });
});
</script>
@endsection
