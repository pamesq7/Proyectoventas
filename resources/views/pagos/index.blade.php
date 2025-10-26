@extends('layouts.app')

@section('title', 'Gestión de Pagos')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">
        <i class="fas fa-money-bill-wave text-primary me-2"></i>
        Gestión de Pagos
    </h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Gestión de Pagos</li>
    </ol>

    <!-- Estadísticas rápidas -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card bg-primary text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Total Pagos</h6>
                            <h4>{{ $transacciones->total() }}</h4>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-dollar-sign fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-success text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Monto Total</h6>
                            <h4>Bs. {{ number_format($transacciones->sum('monto'), 2) }}</h4>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-coins fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-warning text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Este Mes</h6>
                            <h4>Bs. {{ number_format($transacciones->where('created_at', '>=', now()->startOfMonth())->sum('monto'), 2) }}</h4>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-calendar-alt fa-2x opacity-75"></i>
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
                            <h6 class="card-title">Pagos Hoy</h6>
                            <h4>{{ $transacciones->whereDate('created_at', today())->count() }}</h4>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-clock fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de transacciones -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="mb-0"><i class="fas fa-list me-1"></i> Historial de Pagos</h6>
            <div class="btn-group btn-group-sm">
                <button class="btn btn-outline-primary" onclick="window.print()">
                    <i class="fas fa-print me-1"></i> Imprimir
                </button>
                <a href="{{ route('reportes.index') }}" class="btn btn-outline-success">
                    <i class="fas fa-chart-bar me-1"></i> Reportes
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover">
                    <thead class="thead-dark">
                        <tr>
                            <th>N°</th>
                            <th>Fecha</th>
                            <th>Cliente</th>
                            <th>Monto</th>
                            <th>Método</th>
                            <th>Estado</th>
                            <th>Observaciones</th>
                            <th>Usuario</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $contador = ($transacciones->currentPage() - 1) * $transacciones->perPage() + 1; @endphp
                        @forelse($transacciones as $transaccion)
                        <tr>
                            <td>{{ $contador++ }}</td>
                            <td>{{ $transaccion->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <strong>
                                    @if($transaccion->venta->clienteNatural && $transaccion->venta->clienteNatural->user)
                                        {{ $transaccion->venta->clienteNatural->user->name }} {{ $transaccion->venta->clienteNatural->user->primerApellido }}
                                    @elseif($transaccion->venta->clienteEstablecimiento)
                                        {{ $transaccion->venta->clienteEstablecimiento->razonSocial }}
                                    @else
                                        Cliente no especificado
                                    @endif
                                </strong>
                                <br><small class="text-muted">Venta #{{ $transaccion->venta->idVenta }}</small>
                            </td>
                            <td><strong>Bs. {{ number_format($transaccion->monto, 2) }}</strong></td>
                            <td>
                                <span class="badge bg-secondary">{{ $transaccion->metodoPago }}</span>
                            </td>
                            <td>
                                @if($transaccion->estado == 1)
                                    <span class="badge bg-success">✅ Confirmado</span>
                                @else
                                    <span class="badge bg-danger">❌ Anulado</span>
                                @endif
                            </td>
                            <td>{{ $transaccion->observaciones ?? '-' }}</td>
                            <td>
                                @if($transaccion->user)
                                    <strong>{{ $transaccion->user->name }} {{ $transaccion->user->primerApellido }}</strong>
                                    <br><small class="text-muted">{{ $transaccion->user->email }}</small>
                                @else
                                    <span class="text-muted">No registrado</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('ventas.show', $transaccion->venta->idVenta) }}"
                                       class="btn btn-info btn-sm" title="Ver venta">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($transaccion->tipoTransaccion === 'pago' && $transaccion->estado == 1)
                                        <a href="{{ route('pagos.edit', $transaccion->idTransaccion) }}"
                                           class="btn btn-warning btn-sm" title="Editar pago">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button"
                                                class="btn btn-danger btn-sm"
                                                data-transaccion-id="{{ $transaccion->idTransaccion }}"
                                                data-monto="{{ $transaccion->monto }}"
                                                data-venta-id="{{ $transaccion->venta->idVenta }}"
                                                onclick="eliminarPago(this)"
                                                title="Eliminar pago">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center">No se encontraron pagos</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <div class="d-flex justify-content-center">
                {{ $transacciones->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Función para eliminar pago
function eliminarPago(button) {
    const idTransaccion = button.getAttribute('data-transaccion-id');
    const monto = parseFloat(button.getAttribute('data-monto'));
    const idVenta = button.getAttribute('data-venta-id');

    if (confirm(`¿Está seguro que desea eliminar el pago de Bs. ${monto.toFixed(2)}? Esta acción no se puede deshacer.`)) {
        // Deshabilitar el botón mientras se procesa
        button.disabled = true;
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

        $.ajax({
            url: `/ventas/eliminar-transaccion/${idTransaccion}`,
            method: 'DELETE',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (window.showNotification) {
                    window.showNotification('success', 'Pago eliminado correctamente. Redirigiendo...');
                }
                // Redirigir a la venta para ver los cambios
                setTimeout(() => {
                    window.location.href = `/ventas/${idVenta}`;
                }, 1500);
            },
            error: function(xhr) {
                let msg = 'No se pudo eliminar el pago.';
                if (xhr.status === 403) {
                    msg = 'No tienes permisos para eliminar este pago.';
                } else if (xhr.status === 404) {
                    msg = 'Pago no encontrado.';
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }

                if (window.showNotification) {
                    window.showNotification('error', msg);
                }

                // Rehabilitar el botón
                button.disabled = false;
                button.innerHTML = '<i class="fas fa-trash"></i>';
            }
        });
    }
}
</script>
@endpush
