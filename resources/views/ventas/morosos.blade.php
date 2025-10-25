@extends('layouts.app')

@section('title', 'Gestión de Ventas')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">
                        <i class="fas fa-shopping-cart"></i> Clientes Mororsos
                    </h3>
                </div>

                <!-- Filtros -->
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="small text-white-50">Deuda Total</div>
                            <div class="h5 mb-0">Bs. {{ number_format($clientesMorosos->sum('saldo_total'), 2) }}</div>
                        </div>
                        <div class="fa-2x">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-info text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="small text-white-50">Clientes Naturales</div>
                            <div class="h5 mb-0">{{ $clientesMorosos->where('tipo_cliente', 'Natural')->count() }}</div>
                        </div>
                    </form>



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
                                    <th>Fecha Creación</th>
                                    <th>Fecha Actualización</th>
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
                                        <span class="badge bg-warning">{{ $cliente->ventas_pendientes }}</span>
                                    </td>
                                    <td>
                                        <strong class="text-danger">
                                            Bs. {{ number_format($cliente->saldo_total, 2) }}
                                        </strong>
                                    </td>
                                    <td>
                                        @if($diasAtraso > 0)
                                            <span class="text-{{ $colorRiesgo }}">
                                                {{ $diasAtraso }} días
                                            </span>
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
                                        <div class="btn-group" role="group">
                                            @if($cliente->telefono)
                                                <a href="https://wa.me/51{{ $cliente->telefono }}?text=Hola {{ $cliente->nombre_cliente }}, te contactamos por el saldo pendiente de Bs. {{ number_format($cliente->saldo_total, 2) }}" 
                                                   target="_blank" 
                                                   class="btn btn-success btn-sm" 
                                                   title="Contactar por WhatsApp">
                                                    <i class="fab fa-whatsapp"></i>
                                                </a>
                                            @endif
                                            <a href="{{ route('ventas.index', ['cliente_id' => $cliente->id_cliente, 'estado_pago' => 'pendiente']) }}" 
                                               class="btn btn-primary btn-sm" 
                                               title="Ver ventas pendientes">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('ventas.create', ['cliente_id' => $cliente->id_cliente]) }}" 
                                               class="btn btn-warning btn-sm" 
                                               title="Registrar pago">
                                                <i class="fas fa-money-bill"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center">No se encontraron ventas</td>
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

                    <div class="mb-2">
                        <label class="form-label">Tipo Pago *</label>
                        <select class="form-select" id="tipoPago" name="tipoTransaccion">
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
$(document).ready(function() {
    $('#dataTable').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json"
        },
        "order": [[ 4, "desc" ]], // Ordenar por saldo total descendente
        "pageLength": 25,
        "responsive": true
    });
});
</script>
@endpush