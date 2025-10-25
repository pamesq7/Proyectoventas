@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Mi Cuenta</h4>
                </div>

                <div class="card-body">
                    <div class="row">
                        <!-- Información del Cliente -->
                        <div class="col-md-4 mb-4">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0">Mi Perfil</h5>
                                </div>
                                <div class="card-body">
                                    @if(auth()->user()->clienteNatural)
                                    @php $cliente = auth()->user()->clienteNatural; @endphp
                                    <div class="text-center mb-3">
                                        <div class="avatar-circle mb-2">
                                            <span class="initials">
                                                {{ substr($cliente->nombre, 0, 1) }}{{ substr($cliente->apellido_paterno, 0, 1) }}
                                            </span>
                                        </div>
                                        <h5>{{ $cliente->nombre }} {{ $cliente->apellido_paterno }} {{ $cliente->apellido_materno }}</h5>
                                        <p class="text-muted">Cliente desde {{ auth()->user()->created_at->format('d/m/Y') }}</p>
                                    </div>
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item d-flex justify-content-between">
                                            <span>Email:</span>
                                            <span>{{ auth()->user()->email }}</span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between">
                                            <span>Teléfono:</span>
                                            <span>{{ $cliente->telefono ?? 'No especificado' }}</span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between">
                                            <span>CI:</span>
                                            <span>{{ $cliente->ci }}</span>
                                        </li>
                                    </ul>
                                    @elseif(auth()->user()->clienteEstablecimiento)
                                    @php $establecimiento = auth()->user()->clienteEstablecimiento; @endphp
                                    <div class="text-center mb-3">
                                        <div class="avatar-circle mb-2">
                                            <span class="initials">
                                                {{ substr($establecimiento->nombre_establecimiento, 0, 2) }}
                                            </span>
                                        </div>
                                        <h5>{{ $establecimiento->nombre_establecimiento }}</h5>
                                        <p class="text-muted">Cliente desde {{ auth()->user()->created_at->format('d/m/Y') }}</p>
                                        <p class="text-muted">NIT: {{ $establecimiento->nit }}</p>
                                    </div>
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item d-flex justify-content-between">
                                            <span>Email:</span>
                                            <span>{{ auth()->user()->email }}</span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between">
                                            <span>Teléfono:</span>
                                            <span>{{ $establecimiento->telefono ?? 'No especificado' }}</span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between">
                                            <span>Dirección:</span>
                                            <span>{{ $establecimiento->direccion }}</span>
                                        </li>
                                    </ul>
                                    @endif
                                    <div class="mt-3">
                                        <a href="#" class="btn btn-primary btn-block" data-bs-toggle="modal" data-bs-target="#editarPerfilModal">
                                            <i class="fas fa-edit"></i> Editar Perfil
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Resumen de Pedidos -->
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">Mis Pedidos Recientes</h5>
                                    <a href="{{ route('rolCliente.historial') }}" class="btn btn-sm btn-outline-primary">
                                        Ver Historial Completo
                                    </a>
                                </div>
                                <div class="card-body p-0">
                                    @php
                                    $pedidos = auth()->user()->pedidos()->with('detalleVentas.producto')
                                    ->latest()
                                    ->take(5)
                                    ->get();
                                    @endphp

                                    @if($pedidos->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table table-hover mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Pedido #</th>
                                                    <th>Fecha</th>
                                                    <th>Total</th>
                                                    <th>Estado</th>
                                                    <th>Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($pedidos as $pedido)
                                                <tr>
                                                    <td>#{{ $pedido->id }}</td>
                                                    <td>{{ $pedido->created_at->format('d/m/Y') }}</td>
                                                    <td>{{ number_format($pedido->total, 2) }} Bs.</td>
                                                    <td>
                                                        <span class="badge bg-{{ 
                                                                    $pedido->estado == 'completado' ? 'success' : 
                                                                    ($pedido->estado == 'procesando' ? 'info' : 'warning') 
                                                                }}">
                                                            {{ ucfirst($pedido->estado) }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <a href="{{ route('rolCliente.detalle-pedido', $pedido->idVenta) }}" class="btn btn-sm btn-outline-primary">
                                                            <i class="fas fa-eye"></i> Ver
                                                        </a>
                                                        @if($pedido->estado == 'pendiente')
                                                        <button class="btn btn-sm btn-outline-danger"
                                                            onclick="cancelarPedido({{ $pedido->id }})">
                                                            <i class="fas fa-times"></i> Cancelar
                                                        </button>
                                                        @endif
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    @else
                                    <div class="text-center p-4">
                                        <div class="mb-3">
                                            <i class="fas fa-shopping-cart fa-3x text-muted"></i>
                                        </div>
                                        <h5>No has realizado ningún pedido aún</h5>
                                        <p class="text-muted">Explora nuestros productos y realiza tu primer pedido.</p>
                                        <a href="{{ route('rolOperador.catalogo') }}" class="btn btn-primary">
                                            <i class="fas fa-store"></i> Ver Productos
                                        </a>
                                    </div>
                                    @endif
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@push('styles')
<style>
    .avatar-circle {
        width: 80px;
        height: 80px;
        background-color: #4e73df;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
    }

    .avatar-circle .initials {
        color: white;
        font-size: 2rem;
        line-height: 1;
        font-weight: bold;
    }

    .card {
        border: none;
        border-radius: 10px;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        margin-bottom: 1.5rem;
    }

    .card-header {
        background-color: #f8f9fc;
        border-bottom: 1px solid #e3e6f0;
        padding: 1rem 1.25rem;
        border-top-left-radius: 10px !important;
        border-top-right-radius: 10px !important;
    }

    .bg-primary {
        background-color: #4e73df !important;
    }

    .bg-success {
        background-color: #1cc88a !important;
    }

    .bg-info {
        background-color: #36b9cc !important;
    }

    .bg-warning {
        background-color: #f6c23e !important;
    }

    .bg-danger {
        background-color: #e74a3b !important;
    }

    .text-primary {
        color: #4e73df !important;
    }

    .btn-primary {
        background-color: #4e73df;
        border-color: #4e73df;
    }

    .btn-primary:hover {
        background-color: #2e59d9;
        border-color: #2653d4;
    }

    .table th,
    .table td {
        padding: 0.75rem;
        vertical-align: middle;
        border-top: 1px solid #e3e6f0;
    }

    .badge {
        font-weight: 500;
        padding: 0.35em 0.65em;
        font-size: 0.75em;
    }
</style>
@endpush

@push('scripts')
<script>
    function cancelarPedido(pedidoId) {
        if (confirm('¿Estás seguro de que deseas cancelar este pedido?')) {
            fetch(`/pedidos/${pedidoId}/cancelar`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Error al cancelar el pedido: ' + (data.message || 'Inténtalo de nuevo más tarde.'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al procesar la solicitud');
                });
        }
    }

    // Inicializar el plugin de máscara para el número de tarjeta
    $(document).ready(function() {
        $('#numero_tarjeta').inputmask('9999 9999 9999 9999');
        $('#fecha_vencimiento').inputmask('99/99');
        $('#cvv').inputmask('999');

        // Validación del formulario de tarjeta
        $('#agregarTarjetaForm').on('submit', function(e) {
            e.preventDefault();
            // Aquí iría la lógica para procesar el pago
            alert('Tarjeta agregada correctamente');
            $('#agregarTarjetaModal').modal('hide');
        });

        // Validación del formulario de perfil
        $('#editarPerfilForm').on('submit', function(e) {
            e.preventDefault();
            // Aquí iría la lógica para actualizar el perfil
            alert('Perfil actualizado correctamente');
            $('#editarPerfilModal').modal('hide');
            // Recargar la página para ver los cambios
            setTimeout(() => {
                location.reload();
            }, 1000);
        });
    });
</script>
@endpush
@endsection