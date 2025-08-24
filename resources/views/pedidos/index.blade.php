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
                <a href="{{ route('pedidos.catalogo') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus me-1"></i>
                    Crear Pedido
                </a>
            </div>
        </div>
        <div class="card-body">
            @if(isset($pedidos) && $pedidos->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="pedidosTable">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Cliente</th>
                                <th>Total</th>
                                <th>Fecha Entrega</th>
                                <th>Estado</th>
                                <th>Creado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pedidos as $pedido)
                                <tr>
                                    <td>#{{ $pedido->idVenta }}</td>
                                    <td>
                                        @php
                                            $nombreCliente = $pedido->clienteNatural->nombre ?? $pedido->clienteEstablecimiento->nombreEstablecimiento ?? '—';
                                        @endphp
                                        {{ $nombreCliente }}
                                    </td>
                                    <td>
                                        <span class="fw-bold text-success">${{ number_format($pedido->total, 0) }}</span>
                                    </td>
                                    <td>{{ $pedido->fechaEntrega ? \Carbon\Carbon::parse($pedido->fechaEntrega)->format('d/m/Y') : '—' }}</td>
                                    <td>
                                        @php
                                            $estados = ['0' => 'Solicitado', '1' => 'En proceso', '2' => 'Listo', '3' => 'Entregado'];
                                            $badges = ['0' => 'secondary', '1' => 'warning', '2' => 'info', '3' => 'success'];
                                            $estadoKey = (string)($pedido->estadoPedido ?? '0');
                                        @endphp
                                        <span class="badge bg-{{ $badges[$estadoKey] ?? 'secondary' }}">{{ $estados[$estadoKey] ?? 'Solicitado' }}</span>
                                    </td>
                                    <td>{{ $pedido->created_at ? $pedido->created_at->format('d/m/Y') : '—' }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('pedidos.show', $pedido->idVenta) }}" 
                                               class="btn btn-info btn-sm" 
                                               title="Ver detalles">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('pedidos.edit', $pedido->idVenta) }}"
                                               class="btn btn-warning btn-sm"
                                               title="Editar pedido">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('pedidos.destroy', $pedido->idVenta) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('¿Seguro que deseas eliminar el pedido #{{ $pedido->idVenta }}? Esta acción no se puede deshacer.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" title="Eliminar pedido">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
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
    // Inicializar DataTable
    $(document).ready(function() {
        $('#pedidosTable').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json"
            },
            "responsive": true,
            "order": [[ 0, "desc" ]],
            "pageLength": 25
        });
    });
</script>
@endpush