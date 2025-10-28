@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <h5 class="card-title">Total Morosos</h5>
                    <p class="h4">{{ $estadisticas['total_morosos'] }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-dark">
                <div class="card-body">
                    <h5 class="card-title">Monto Pendiente</h5>
                    <p class="h4">S/ {{ number_format($estadisticas['monto_total_pendiente'], 2) }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h5 class="card-title">Clientes Morosos</h5>
                    <p class="h4">{{ $estadisticas['clientes_morosos'] }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-secondary text-white">
                <div class="card-body">
                    <h5 class="card-title">Días de Atraso Promedio</h5>
                    <p class="h4">{{ round($estadisticas['promedio_atraso']) }} días</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Lista de Clientes Morosos</h3>
            <div class="card-tools">
                <form method="GET" action="{{ route('morosos.index') }}" class="form-inline">
                    <div class="input-group input-group-sm">
                        <input type="date" name="fecha_desde" class="form-control mr-2" 
                               value="{{ request('fecha_desde') }}" placeholder="Desde">
                        <input type="date" name="fecha_hasta" class="form-control mr-2" 
                               value="{{ request('fecha_hasta') }}" placeholder="Hasta">
                        <select name="tipo_cliente" class="form-control mr-2">
                            <option value="">Todos los clientes</option>
                            <option value="natural" {{ request('tipo_cliente') == 'natural' ? 'selected' : '' }}>Natural</option>
                            <option value="establecimiento" {{ request('tipo_cliente') == 'establecimiento' ? 'selected' : '' }}>Establecimiento</option>
                        </select>
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i> Filtrar
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-hover">
                <thead class="thead-dark">
                    <tr>
                        <th>Cliente</th>
                        <th>Tipo</th>
                        <th>Total Venta</th>
                        <th>Saldo Pendiente</th>
                        <th>Días de Atraso</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ventas as $venta)
                        <tr>
                            <td>{{ $venta->nombre_cliente }}</td>
                            <td>{{ $venta->tipo_cliente }}</td>
                            <td>S/ {{ number_format($venta->total, 2) }}</td>
                            <td class="font-weight-bold text-danger">S/ {{ number_format($venta->saldo, 2) }}</td>
                            <td class="{{ $venta->dias_atraso < 0 ? 'text-danger' : 'text-warning' }}">
                                {{ abs($venta->dias_atraso) }} días
                            </td>
                            <td>
                                @if($venta->estado_pago == 'PAGADO')
                                    <span class="badge bg-success">{{ $venta->estado_pago }}</span>
                                @elseif($venta->estado_pago == 'PARCIAL')
                                    <span class="badge bg-warning">{{ $venta->estado_pago }} ({{ $venta->porcentaje_pagado }}%)</span>
                                @else
                                    <span class="badge bg-danger">{{ $venta->estado_pago }}</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('morosos.show', $venta->idVenta) }}" 
                                   class="btn btn-sm btn-info" title="Ver detalles">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if($venta->saldo > 0)
                                    <button class="btn btn-sm btn-success" 
                                            data-toggle="modal" 
                                            data-target="#modalPago" 
                                            data-venta-id="{{ $venta->idVenta }}"
                                            data-cliente="{{ $venta->nombre_cliente }}"
                                            data-saldo="{{ $venta->saldo }}">
                                        <i class="fas fa-money-bill-wave"></i> Pagar
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">No hay clientes morosos registrados</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-3">
                {{ $ventas->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Modal para registrar pago -->
<div class="modal fade" id="modalPago" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Registrar Pago</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formPago" method="POST" action="{{ route('morosos.store') }}">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="idVenta" id="ventaId">
                    
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
                        <input type="number" name="monto" id="monto" 
                               class="form-control" step="0.01" min="0.01" required>
                    </div>

                    <div class="form-group">
                        <label for="metodoPago">Método de Pago: *</label>
                        <select name="metodoPago" id="metodoPago" class="form-control" required>
                            <option value="efectivo">Efectivo</option>
                            <option value="transferencia">Transferencia</option>
                            <option value="tarjeta">Tarjeta</option>
                            <option value="deposito">Depósito</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="observaciones">Observaciones:</label>
                        <textarea name="observaciones" id="observaciones" 
                                 class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary">Registrar Pago</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Configurar el modal de pago
    $('#modalPago').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var ventaId = button.data('venta-id');
        var cliente = button.data('cliente');
        var saldo = parseFloat(button.data('saldo'));

        var modal = $(this);
        modal.find('#ventaId').val(ventaId);
        modal.find('#clienteNombre').text(cliente);
        modal.find('#saldoPendiente').text('S/ ' + saldo.toFixed(2));
        modal.find('#monto').attr('max', saldo).val(saldo);
    });

    // Validar monto máximo
    $('#monto').on('change', function() {
        var max = parseFloat($(this).attr('max'));
        var value = parseFloat($(this).val());
        
        if (value > max) {
            alert('El monto no puede ser mayor al saldo pendiente');
            $(this).val(max.toFixed(2));
        }
    });

    // Enviar formulario con AJAX
    $('#formPago').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        var url = form.attr('action');
        
        $.ajax({
            type: 'POST',
            url: url,
            data: form.serialize(),
            success: function(response) {
                $('#modalPago').modal('hide');
                Swal.fire({
                    icon: 'success',
                    title: '¡Pago registrado!',
                    text: 'El pago se ha registrado correctamente.',
                    showConfirmButton: false,
                    timer: 2000
                }).then(() => {
                    location.reload();
                });
            },
            error: function(xhr) {
                var errors = xhr.responseJSON.errors;
                var errorMessage = 'Ocurrió un error al procesar el pago.';
                
                if (errors) {
                    errorMessage = Object.values(errors)[0][0];
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: errorMessage
                });
            }
        });
    });
</script>
@endpush