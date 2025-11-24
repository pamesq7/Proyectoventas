@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Editar Pedido #{{ $pedido->idVenta }}</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('pedidos.index') }}">Pedidos</a></li>
        <li class="breadcrumb-item active">Editar #{{ $pedido->idVenta }}</li>
    </ol>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <i class="fas fa-edit me-1"></i>
                Editar Pedido #{{ $pedido->idVenta }}
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('pedidos.show', $pedido->idVenta) }}" class="btn btn-sm btn-info">
                    <i class="fas fa-eye me-1"></i> Ver
                </a>
                <a href="{{ route('pedidos.index') }}" class="btn btn-sm btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Volver
                </a>
            </div>
        </div>
        <div class="card-body">
            <form id="formEditarPedido" action="{{ route('pedidos.update', $pedido->idVenta) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label">Fecha de Entrega *</label>
                        <input type="date" name="fechaEntrega" class="form-control"
       value="{{ old('fechaEntrega', $pedido->fechaEntrega ? (is_string($pedido->fechaEntrega) ? $pedido->fechaEntrega : $pedido->fechaEntrega->format('Y-m-d')) : '') }}" 
       required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Lugar de Entrega *</label>
                        <input type="text" name="lugarEntrega" class="form-control" 
                               value="{{ old('lugarEntrega', $pedido->lugarEntrega) }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Estado del Pedido *</label>
                        <select name="estadoPedido" class="form-select" required>
                            <option value="pendiente" {{ $pedido->estadoPedido === 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                            <option value="en_proceso" {{ $pedido->estadoPedido === 'en_proceso' ? 'selected' : '' }}>En Proceso</option>
                            <option value="completado" {{ $pedido->estadoPedido === 'completado' ? 'selected' : '' }}>Completado</option>
                            <option value="entregado" {{ $pedido->estadoPedido === 'entregado' ? 'selected' : '' }}>Entregado</option>
                            <option value="cancelado" {{ $pedido->estadoPedido === 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                        </select>
                    </div>
                </div>

                <div class="table-responsive mb-4">
                    <table class="table table-bordered table-hover" id="tablaItems">
                        <thead class="table-light">
                            <tr>
                                <th>Producto</th>
                                <th style="width: 120px">Talla</th>
                                <th style="width: 100px">Cantidad</th>
                                <th style="width: 120px">Precio Unit.</th>
                                <th style="width: 120px">Subtotal</th>
                                <th>Personalización</th>
                                <th style="width: 50px"></th>
                            </tr>
                        </thead>
                        <tbody id="tbodyItems">
                            @foreach($pedido->detalleVentas as $index => $detalle)
                            <tr class="item-row" data-id="{{ $detalle->iddetalleVenta }}">
                                <td>
                                    {{ $detalle->producto->nombre ?? 'Producto no encontrado' }}
                                    <input type="hidden" name="detalles[{{ $index }}][iddetalleVenta]" 
                                           value="{{ $detalle->iddetalleVenta }}">
                                    <input type="hidden" name="detalles[{{ $index }}][idProducto]" 
                                           value="{{ $detalle->idProducto }}">
                                </td>
                                <td>
                                    <select name="detalles[{{ $index }}][idTallas]" class="form-select form-select-sm sel-talla" required>
                                        @foreach($tallas as $talla)
                                        <option value="{{ $talla->idTallas }}" 
                                            {{ $detalle->detalleTallas->first() && $detalle->detalleTallas->first()->idTallas == $talla->idTallas ? 'selected' : '' }}>
                                            {{ $talla->nombre }}
                                        </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input type="number" name="detalles[{{ $index }}][cantidad]" 
                                           class="form-control form-control-sm inp-cantidad" 
                                           min="1" value="{{ $detalle->cantidad }}" required>
                                </td>
                                <td>
                                    <input type="number" step="0.01" name="detalles[{{ $index }}][precioUnitario]"
                                           class="form-control form-control-sm inp-precio" 
                                           value="{{ number_format($detalle->precioUnitario, 2, '.', '') }}" required>
                                </td>
                                <td class="text-end subtotal">
                                    {{ number_format($detalle->subtotal, 2) }}
                                </td>
                                <td>
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <input type="text" name="detalles[{{ $index }}][nombrePersonalizado]" 
                                                   class="form-control form-control-sm" 
                                                   placeholder="Nombre" value="{{ $detalle->nombrePersonalizado }}">
                                        </div>
                                        <div class="col-6">
                                            <input type="text" name="detalles[{{ $index }}][numeroPersonalizado]" 
                                                   class="form-control form-control-sm" 
                                                   placeholder="Número" value="{{ $detalle->numeroPersonalizado }}">
                                        </div>
                                        <div class="col-12">
                                            <input type="text" name="detalles[{{ $index }}][observaciones]" 
                                                   class="form-control form-control-sm mt-1" 
                                                   placeholder="Observaciones" value="{{ $detalle->observaciones }}">
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    @if($loop->first)
                                    <button type="button" class="btn btn-sm btn-outline-success btnAddRow" title="Agregar fila">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                    @else
                                    <button type="button" class="btn btn-sm btn-outline-danger btnRemoveRow" title="Eliminar fila">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="4" class="text-end">TOTAL</th>
                                <th id="totalPedido" class="text-end">{{ number_format($pedido->total, 2) }}</th>
                                <th colspan="2"></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-4">
                    <button type="button" class="btn btn-outline-secondary" onclick="history.back()">
                        <i class="fas fa-arrow-left me-1"></i> Cancelar
                    </button>
                    <div>
                        <a href="{{ route('pedidos.show', $pedido->idVenta) }}" class="btn btn-outline-info me-2">
                            <i class="fas fa-eye me-1"></i> Ver Pedido
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Guardar Cambios
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    let rowIndex = {{ count($pedido->detalleVentas) }};
    const tallas = @json($tallas);

    // Agregar nueva fila
    $(document).on('click', '.btnAddRow', function() {
        const newRow = $(`
            <tr class="item-row" data-id="new-${rowIndex}">
                <td>
                    <select name="detalles[${rowIndex}][idProducto]" class="form-select form-select-sm" required>
                        @foreach($productos as $producto)
                        <option value="{{ $producto->idProducto }}">{{ $producto->nombre }}</option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <select name="detalles[${rowIndex}][idTallas]" class="form-select form-select-sm sel-talla" required>
                        @foreach($tallas as $talla)
                        <option value="{{ $talla->idTallas }}">{{ $talla->nombre }}</option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <input type="number" name="detalles[${rowIndex}][cantidad]" 
                           class="form-control form-control-sm inp-cantidad" min="1" value="1" required>
                </td>
                <td>
                    <input type="number" step="0.01" name="detalles[${rowIndex}][precioUnitario]"
                           class="form-control form-control-sm inp-precio" value="0.00" required>
                </td>
                <td class="text-end subtotal">0.00</td>
                <td>
                    <div class="row g-2">
                        <div class="col-6">
                            <input type="text" name="detalles[${rowIndex}][nombrePersonalizado]" 
                                   class="form-control form-control-sm" placeholder="Nombre">
                        </div>
                        <div class="col-6">
                            <input type="text" name="detalles[${rowIndex}][numeroPersonalizado]" 
                                   class="form-control form-control-sm" placeholder="Número">
                        </div>
                        <div class="col-12">
                            <input type="text" name="detalles[${rowIndex}][observaciones]" 
                                   class="form-control form-control-sm mt-1" placeholder="Observaciones">
                        </div>
                    </div>
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-outline-danger btnRemoveRow" title="Eliminar fila">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `);
        
        $('#tbodyItems').append(newRow);
        rowIndex++;
        calcularTotales();
    });

    // Eliminar fila
    $(document).on('click', '.btnRemoveRow', function() {
        if ($('#tbodyItems tr').length > 1) {
            $(this).closest('tr').remove();
            recalcularIndices();
            calcularTotales();
        } else {
            alert('Debe haber al menos un ítem en el pedido');
        }
    });

    // Recalcular totales al cambiar cantidad o precio
    $(document).on('input', '.inp-cantidad, .inp-precio', function() {
        const row = $(this).closest('tr');
        const cantidad = parseFloat(row.find('.inp-cantidad').val()) || 0;
        const precio = parseFloat(row.find('.inp-precio').val()) || 0;
        const subtotal = cantidad * precio;
        row.find('.subtotal').text(subtotal.toFixed(2));
        calcularTotales();
    });

    // Función para recalcular índices de filas
    function recalcularIndices() {
        let newIndex = 0;
        $('#tbodyItems tr').each(function() {
            $(this).find('input, select').each(function() {
                let name = $(this).attr('name');
                name = name.replace(/\[(\d+)\]/g, `[${newIndex}]`);
                $(this).attr('name', name);
            });
            newIndex++;
        });
        rowIndex = newIndex;
    }

    // Calcular totales generales
    function calcularTotales() {
        let total = 0;
        $('.item-row').each(function() {
            const subtotal = parseFloat($(this).find('.subtotal').text()) || 0;
            total += subtotal;
        });
        $('#totalPedido').text(total.toFixed(2));
    }

    // Inicializar cálculos
    calcularTotales();
});
</script>
@endpush