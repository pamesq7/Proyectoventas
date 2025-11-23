@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Confirmación de Pedido</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('pedidos.index') }}">Pedidos</a></li>
        <li class="breadcrumb-item active">Pedido #{{ $venta->idVenta }}</li>
    </ol>

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    @if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-receipt me-1"></i> Resumen del pedido</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-2"><strong>Total:</strong> ${{ number_format($venta->total, 2) }}</div>
                            <div class="mb-2"><strong>Saldo pendiente:</strong> <span class="badge bg-{{ $venta->saldo > 0 ? 'warning' : 'success' }}">${{ number_format($venta->saldo, 2) }}</span></div>
                            <div class="mb-2"><strong>Entrega:</strong> {{ \Carbon\Carbon::parse($venta->fechaEntrega)->format('d/m/Y') }} en {{ $venta->lugarEntrega }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-2"><strong>Cliente:</strong>
                                @if($venta->clienteNatural)
                                {{ $venta->clienteNatural->nombres }} {{ $venta->clienteNatural->apellidos }}
                                @elseif($venta->clienteEstablecimiento)
                                {{ $venta->clienteEstablecimiento->razonSocial }}
                                @else
                                -
                                @endif
                            </div>
                            <div class="mb-2"><strong>Estado:</strong> {{ $venta->estadoTexto }}</div>
                            <div class="text-muted small">Creado: {{ $venta->created_at->format('d/m/Y H:i') }}</div>
                        </div>
                    </div>

                    <hr>
                    <h6 class="mb-3">Detalles</h6>

                    <div class="table-responsive">
                        <table class="table table-sm align-middle">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th class="text-center">Talla</th>
                                    <th class="text-center">Cant.</th>
                                    <th class="text-end">P. Unit</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($venta->detalleVentas as $det)
                                <tr>
                                    <td>{{ $det->descripcion }}</td>
                                    <td class="align-middle">
                                        <div class="d-flex flex-column">
                                            @if($det->talla)
                                                <div class="text-center">
                                                    <span class="badge bg-primary">{{ $det->talla->nombre }}</span>
                                                </div>
                                            @endif
                                            @if($det->detalleTallas->isNotEmpty())
                                                <div class="mt-1">
                                                    @foreach($det->detalleTallas->groupBy('talla.nombre') as $tallaNombre => $detallesTalla)
                                                        @php
                                                            $totalTalla = $detallesTalla->sum('cantidad');
                                                            $esTallaPrincipal = $det->talla && $tallaNombre === $det->talla->nombre;
                                                        @endphp
                                                        @if(!$esTallaPrincipal)
                                                            <div class="d-flex justify-content-between small">
                                                                <span>{{ $tallaNombre }}:</span>
                                                                <span class="fw-medium ms-2">{{ $totalTalla }} und</span>
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="align-middle text-center">
                                        <div class="d-flex flex-column">
                                            <span>{{ $det->cantidad }}</span>
                                            @if($det->detalleTallas->isNotEmpty())
                                                <span class="small text-muted">
                                                    Total: {{ $det->detalleTallas->sum('cantidad') }}
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="text-end">${{ number_format($det->precioUnitario, 2) }}</td>
                                    <td class="text-end">${{ number_format((float)$det->cantidad * (float)$det->precioUnitario, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="fas fa-money-bill-wave me-1"></i> Pagos registrados</h6>
                    <span class="text-muted small">Total pagos: ${{ number_format($venta->transacciones->where('tipoTransaccion','pago')->sum('monto'), 2) }}</span>
                </div>
                <div class="card-body">
                    @if($venta->transacciones->isEmpty())
                    <div class="alert alert-info mb-0">Aún no hay pagos registrados.</div>
                    @else
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Pagos Registrados</h5>
                        </div>
                        <div class="card-body p-0">
                            @if($venta->transacciones->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Fecha</th>
                                            <th>Monto</th>
                                            <th>Método</th>
                                            <th>Estado</th>
                                            <th>Observaciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($venta->transacciones as $transaccion)
                                        <tr>
                                            <td>{{ $transaccion->created_at->format('d/m/Y H:i') }}</td>
                                            <td>${{ number_format($transaccion->monto, 2) }}</td>
                                            <td>
                                                <span class="badge bg-info">{{ strtoupper($transaccion->metodoPago) }}</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-success">Confirmada</span>
                                            </td>
                                            <td>{{ $transaccion->observaciones ?? 'Ninguna' }}</td>
                                        </tr>
                                        @endforeach
                                        <tr class="table-light">
                                            <td colspan="5" class="text-end fw-bold">
                                                Total pagado: ${{ number_format($venta->transacciones->sum('monto'), 2) }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            @else
                            <div class="text-center p-4">
                                <div class="text-muted">No hay pagos registrados</div>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-cash-register me-1"></i> Registrar pago</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('pedidos.registrar-pago', $venta->idVenta) }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Monto</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                @php
                                $saldo = (float)$venta->saldo;
                                $montoMaximo = max(0.01, $saldo); // Asegura que el mínimo sea 0.01
                                $valorInicial = min($montoMaximo, $saldo > 0 ? $saldo : 0.01);
                                @endphp
                                <input
                                    type="number"
                                    name="monto"
                                    class="form-control"
                                    step="0.01"
                                    min="0.01"
                                    max="{{ number_format($montoMaximo, 2, '.', '') }}"
                                    value="{{ number_format($valorInicial, 2, '.', '') }}"
                                    required
                                    oninput="validarMonto(this)">
                                <input type="hidden" id="saldo-disponible" value="{{ $saldo }}">
                            </div>
                            <small class="text-muted">Saldo pendiente: ${{ number_format($saldo, 2) }}</small>
                            <div id="monto-error" class="text-danger small d-none">El monto no puede ser mayor al saldo pendiente</div>
                        </div>

                        @push('scripts')
                        <script>
                            function validarMonto(input) {
                                const monto = parseFloat(input.value) || 0;
                                const saldo = parseFloat(document.getElementById('saldo-disponible').value) || 0;
                                const errorElement = document.getElementById('monto-error');

                                if (monto > saldo) {
                                    errorElement.classList.remove('d-none');
                                    input.setCustomValidity('El monto no puede ser mayor al saldo pendiente');
                                } else {
                                    errorElement.classList.add('d-none');
                                    input.setCustomValidity('');
                                }
                            }

                            // Validar al cargar la página
                            document.addEventListener('DOMContentLoaded', function() {
                                const inputMonto = document.querySelector('input[name="monto"]');
                                if (inputMonto) {
                                    validarMonto(inputMonto);
                                }
                            });
                        </script>
                        @endpush

                        <div class="mb-3">
                            <label class="form-label">Método de pago</label>
                            <input list="metodos" name="metodoPago" class="form-control" placeholder="Efectivo, Qr, Transferencia, ..." required>
                            <datalist id="metodos">
                                @foreach($metodosPago as $mp)
                                <option value="{{ $mp['nombre'] }}">{{ $mp['nombre'] }}</option>
                                @endforeach
                            </datalist>
                            <small class="text-muted">Puedes elegir de la lista o escribir uno personalizado.</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Observaciones</label>
                            <textarea name="observaciones" class="form-control" rows="3" placeholder="Referencia, voucher, notas..."></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-save me-1"></i> Guardar pago
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection