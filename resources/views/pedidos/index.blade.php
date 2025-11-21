@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Gestión de Pedidos</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Pedidos</li>
    </ol>

    {{-- Mensajes de éxito o error --}}
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- Mensaje específico para eliminación exitosa --}}
    @if(session('successdelete'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-trash me-2"></i>
        {{ session('successdelete') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i>
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="card mb-4">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <i class="fas fa-clipboard-list me-1"></i>
                    Lista de Pedidos
                </div>
                <div>
                    <a href="{{ route('pedidos.catalogo') }}" class="btn btn-primary btn-sm me-2">
                        <i class="fas fa-plus me-1"></i>
                        Crear Pedido
                    </a>
                    <a href="{{ route('export.pedidos.pdf') }}" class="btn btn-success btn-sm" target="_blank">
                        <i class="fas fa-file-pdf me-1"></i>
                        Exportar PDF
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            @if(isset($pedidos) && $pedidos->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Cliente</th>
                            <th>Producto/Diseño</th>
                            <th>Total</th>
                            <th>Estado Pago</th>
                            <th>Fecha Entrega</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pedidos as $venta)
                        <tr>
                            <td>{{ $venta->idVenta }}</td>
                            <td>
                                @php
                                $nombreCliente = 'Cliente no especificado';
                                if ($venta->clienteNatural && $venta->clienteNatural->user) {
                                $user = $venta->clienteNatural->user;
                                $nombreCliente = trim($user->name . ' ' . ($user->primerApellido ?? ''));
                                } elseif ($venta->clienteEstablecimiento) {
                                $nombreCliente = $venta->clienteEstablecimiento->razonSocial;
                                }
                                @endphp
                                {{ $nombreCliente }}
                            </td>
                            <td>
                                @foreach($venta->detalleVentas as $detalle)
                                <div class="d-flex align-items-center mb-2">
                                    @if($detalle->diseno && $detalle->diseno->isNotEmpty() && $detalle->diseno->first()->archivo)
                                    <img src="{{ asset('storage/' . $detalle->diseno->first()->archivo) }}"
                                        alt="Diseño"
                                        class="img-thumbnail"
                                        style="width: 50px; height: 50px; object-fit: cover;">
                                    <span class="ms-2">Diseño personalizado</span>
                                    @elseif($detalle->producto && $detalle->producto->foto)
                                    <img src="{{ asset('storage/' . $detalle->producto->foto) }}"
                                        alt="{{ $detalle->producto->nombre }}"
                                        class="img-thumbnail"
                                        style="width: 50px; height: 50px; object-fit: cover;">
                                    <span class="ms-2">{{ $detalle->producto->nombre }}</span>
                                    @else
                                    <div class="bg-light d-flex align-items-center justify-content-center"
                                        style="width: 50px; height: 50px;">
                                        <i class="fas fa-box-open text-muted"></i>
                                    </div>
                                    <span class="ms-2">Sin imagen</span>
                                    @endif
                                </div>
                                @endforeach
                            </td>
                            <td>Bs. {{ number_format($venta->total, 2) }}</td>
                            <td>
                                @php
                                $pagado = (float)($venta->saldo ?? 0) <= 0;
                                    @endphp
                                    <span class="badge bg-{{ $pagado ? 'success' : 'danger' }}">
                                    {{ $pagado ? 'Pago completado' : 'Pendiente de pago' }}
                                    </span>
                                    @if(!$pagado && $venta->saldo < $venta->total)
                                        <small class="d-block text-muted">Saldo: Bs. {{ number_format($venta->saldo, 2) }}</small>
                                        @endif
                            </td>
                            <td>{{ $venta->fechaEntrega ? \Carbon\Carbon::parse($venta->fechaEntrega)->format('d/m/Y') : 'Pendiente' }}</td>
                            <td>
                                @php
                                // Definir los estados posibles
                                $estados = [
                                0 => ['nombre' => 'Pendiente', 'clase' => 'bg-warning'],
                                1 => ['nombre' => 'En Proceso', 'clase' => 'bg-primary'],
                                2 => ['nombre' => 'Listo', 'clase' => 'bg-info'],
                                3 => ['nombre' => 'Entregado', 'clase' => 'bg-success'],
                                4 => ['nombre' => 'Pagado', 'clase' => 'bg-success'],
                                5 => ['nombre' => 'Pago Parcial', 'clase' => 'bg-info']
                                ];

                                // Obtener el estado actual o usar 'Pendiente' como predeterminado
                                $estadoActual = $venta->estadoPedido ?? 0;
                                $estado = $estados[$estadoActual] ?? $estados[0];
                                $estadoPedido = $estado['nombre'];
                                $claseBadge = $estado['clase'];
                                @endphp

                                <span class="badge {{ $claseBadge }}">{{ $estadoPedido }}</span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('pedidos.show', $venta->idVenta) }}"
                                        class="btn btn-info"
                                        title="Ver detalle">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('pedidos.edit', $venta->idVenta) }}"
                                        class="btn btn-warning"
                                        title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="mt-3">
                    {{ $pedidos->links() }}
                </div>
            </div>
            @else
            <div class="text-center py-4">
                <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No hay pedidos registrados</h5>
                <p class="text-muted">Comienza creando tu primer pedido desde el catálogo</p>
                <a href="{{ route('pedidos.catalogo') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i>
                    Crear Pedido
                </a>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Inicializar Simple DataTable
    document.addEventListener('DOMContentLoaded', function() {
        if (document.getElementById('pedidosTable')) {
            new DataTable(document.getElementById('pedidosTable'), {
                responsive: true,
                paging: true,
                searching: true,
                ordering: true,
                order: [
                    [0, 'desc']
                ],
                pageLength: 25,
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json'
                }
            });
        }
    });
</script>
@endpush