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
                        <a href="{{ route('ventas.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Registrar Pago
                        </a>
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
                                <label for="estado_pedido">Estado Pedido:</label>
                                <select name="estado_pedido" id="estado_pedido" class="form-control">
                                    <option value="">Todos</option>
                                    <option value="0" {{ request('estado_pedido') == '0' ? 'selected' : '' }}>Solicitado</option>
                                    <option value="1" {{ request('estado_pedido') == '1' ? 'selected' : '' }}>Diseño</option>
                                    <option value="2" {{ request('estado_pedido') == '2' ? 'selected' : '' }}>Confección</option>
                                    <option value="3" {{ request('estado_pedido') == '3' ? 'selected' : '' }}>Entregado</option>
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
                                    <span class="info-box-number">S/. {{ number_format($estadisticas['monto_pendiente'], 2) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tabla de ventas -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Fecha</th>
                                    <th>Total</th>
                                    <th>Cliente</th>
                                    <th>Estado Pago</th>
                                    <th>Estado Pedido</th>
                                    <th>Vendedor</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($ventas as $venta)
                                <tr>
                                    <td>{{ $venta->created_at->format('d/m/Y') }}</td>
                                    <td>
                                        <strong>S/. {{ number_format($venta->total, 2) }}</strong>
                                        @if($venta->estado_pago == 'PARCIAL')
                                            <br><small class="text-muted">Pagado: S/. {{ number_format($venta->monto_pagado, 2) }}</small>
                                            <br><small class="text-danger">Debe: S/. {{ number_format($venta->saldo, 2) }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <strong>{{ $venta->nombre_cliente }}</strong>
                                        <br><small class="text-muted">{{ $venta->tipo_cliente }}</small>
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
                                        <select class="form-control form-control-sm estado-pedido" 
                                                data-venta-id="{{ $venta->idVenta }}"
                                                {{ $venta->estado == '3' ? 'disabled' : '' }}>
                                            <option value="0" {{ $venta->estado == '0' ? 'selected' : '' }}>Solicitado</option>
                                            <option value="1" {{ $venta->estado == '1' ? 'selected' : '' }}>Diseño</option>
                                            <option value="2" {{ $venta->estado == '2' ? 'selected' : '' }}>Confección</option>
                                            <option value="3" {{ $venta->estado == '3' ? 'selected' : '' }}>Entregado</option>
                                        </select>
                                    </td>
                                    <td>{{ $venta->empleado->nombre ?? 'No asignado' }}</td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('ventas.show', $venta->idVenta) }}" 
                                               class="btn btn-info" title="Ver detalles">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if($venta->puedeRecibirPagos())
                                                <button type="button" class="btn btn-success btn-cobrar" 
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
                                    <td colspan="7" class="text-center">No se encontraron ventas</td>
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

<!-- Modal para registrar pago -->
<div class="modal fade" id="modalPago" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Registrar Pago</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
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
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Registrar Pago</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Manejar cambio de estado de pedido
    $('.estado-pedido').change(function() {
        const ventaId = $(this).data('venta-id');
        const nuevoEstado = $(this).val();
        
        $.ajax({
            url: `/ventas/${ventaId}/estado`,
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                estado: nuevoEstado
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    location.reload();
                }
            },
            error: function() {
                toastr.error('Error al actualizar el estado');
                location.reload();
            }
        });
    });
    
    // Abrir modal de pago
    $('.btn-cobrar').click(function() {
        const ventaId = $(this).data('venta-id');
        const cliente = $(this).data('cliente');
        const saldo = $(this).data('saldo');
        
        $('#ventaId').val(ventaId);
        $('#clienteNombre').text(cliente);
        $('#saldoPendiente').text('S/. ' + parseFloat(saldo).toFixed(2));
        $('#monto').attr('max', saldo);
        $('#modalPago').modal('show');
    });
    
    // Limpiar modal al cerrar
    $('#modalPago').on('hidden.bs.modal', function() {
        $('#formPago')[0].reset();
    });
});
</script>
@endsection
