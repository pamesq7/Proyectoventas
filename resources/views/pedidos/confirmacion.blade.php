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
                            <div class="mb-2"><strong>Subtotal:</strong> ${{ number_format($venta->subtotal, 2) }}</div>
                            <div class="mb-2"><strong>Saldo pendiente:</strong> <span class="badge bg-{{ $venta->saldo > 0 ? 'warning' : 'success' }}">${{ number_format($venta->saldo, 2) }}</span></div>
                            <div class="mb-2"><strong>Entrega:</strong> {{ \Carbon\Carbon::parse($venta->fechaEntrega)->format('d/m/Y') }} en {{ $venta->lugarEntrega }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-2"><strong>Cliente:</strong>
                                @if($venta->clienteNatural)
                                    {{ $venta->clienteNatural->nombres }} {{ $venta->clienteNatural->apellidos }}
                                @elseif($venta->clienteEstablecimiento)
                                    {{ $venta->clienteEstablecimiento->nombreEstablecimiento }}
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
                    <div class="mb-2 d-flex justify-content-end">
                        <a href="{{ route('pedidos.personalizar', ['venta' => $venta->idVenta]) }}" class="btn btn-outline-primary">
                            <i class="fas fa-plus me-1"></i> Agregar otro pedido
                        </a>
                    </div>
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
                                        <td>{{ $det->descripcion ?? 'Producto' }}</td>
                                        <td class="text-center">{{ $det->talla->nombre ?? '-' }}</td>
                                        <td class="text-center">{{ $det->cantidad }}</td>
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
                        <div class="table-responsive">
                            <table class="table table-striped table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th class="text-end">Monto</th>
                                        <th>Método</th>
                                        <th>Estado</th>
                                        <th>Obs.</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($venta->transacciones->sortByDesc('created_at') as $trx)
                                        @if($trx->tipoTransaccion === 'pago')
                                        <tr>
                                            <td>{{ optional($trx->created_at)->format('d/m/Y H:i') }}</td>
                                            <td class="text-end">${{ number_format($trx->monto, 2) }}</td>
                                            <td>{{ $trx->metodoPago }}</td>
                                            <td><span class="badge bg-{{ $trx->estado == 1 ? 'success' : 'secondary' }}">{{ $trx->estado_texto }}</span></td>
                                            <td class="text-truncate" style="max-width: 240px;" title="{{ $trx->observaciones }}">{{ $trx->observaciones }}</td>
                                        </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
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
                                <input type="number" name="monto" class="form-control" step="0.01" min="0.01" max="{{ max(0, (float)$venta->saldo) }}" value="{{ number_format((float)min(max(0, (float)$venta->saldo), (float)$venta->total), 2, '.', '') }}" required>
                            </div>
                            <small class="text-muted">Saldo pendiente: ${{ number_format($venta->saldo, 2) }}</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Método de pago</label>
                            <input list="metodos" name="metodoPago" class="form-control" placeholder="Efectivo, Yape, Transferencia, ..." required>
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
