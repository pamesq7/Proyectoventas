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
                <table class="table table-striped table-hover" id="pedidosTable">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th> <!-- Columna para el número consecutivo -->
                            <th>Cliente</th>
                            <th>Total</th>
                            <th>Pago</th>
                            <th>Fecha Entrega</th>
                            <th>Estado</th>
                            <th>Creado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pedidos as $pedido)
                        <tr>
                        <td>{{ $pedido->contador }}</td>
                            <td>
                                @php
                                if ($pedido->clienteNatural && $pedido->clienteNatural->user) {
                                    $nombreCliente = trim($pedido->clienteNatural->user->name . ' ' . 
                                                         $pedido->clienteNatural->user->primerApellido . ' ' . 
                                                         ($pedido->clienteNatural->user->segundApellido ?? ''));
                                } elseif ($pedido->clienteEstablecimiento) {
                                    $nombreCliente = $pedido->clienteEstablecimiento->razonSocial ?? '—';
                                } else {
                                    $nombreCliente = '—';
                                }
                                @endphp
                                {{ $nombreCliente }}
                            </td>
                            <td>
                                <span class="fw-bold text-success">${{ number_format($pedido->total, 0) }}</span>
                            </td>
                            <td>
                                @php $pagado = (float)($pedido->saldo ?? 0) <= 0; @endphp
                                    <span class="badge bg-{{ $pagado ? 'success' : 'danger' }}">{{ $pagado ? 'Pago completado' : 'Debe' }}</span>
                            </td>
                            <td>{{ $pedido->fechaEntrega ? \Carbon\Carbon::parse($pedido->fechaEntrega)->format('d/m/Y') : '—' }}</td>
                            <td>
                                @php
                                $estados = [
                                    '0' => ['nombre' => 'En Diseño', 'badge' => 'info', 'icon' => '🎨'],
                                    '1' => ['nombre' => 'Producción', 'badge' => 'warning', 'icon' => '⚙️'],
                                    '2' => ['nombre' => 'Terminado', 'badge' => 'success', 'icon' => '✅'],
                                    '3' => ['nombre' => 'Entregado', 'badge' => 'primary', 'icon' => '📦'],
                                    '4' => ['nombre' => 'Cancelado', 'badge' => 'danger', 'icon' => '❌']
                                ];
                                $estadoKey = (string)($pedido->estadoPedido ?? '0');
                                $estadoActual = $estados[$estadoKey] ?? $estados['0'];
                                @endphp
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-{{ $estadoActual['badge'] }} estado-badge" id="badge-{{ $pedido->idVenta }}">
                                        {{ $estadoActual['icon'] }} {{ $estadoActual['nombre'] }}
                                    </span>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle p-1" 
                                                type="button" 
                                                id="dropdownEstado{{ $pedido->idVenta }}" 
                                                data-bs-toggle="dropdown" 
                                                aria-expanded="false"
                                                title="Cambiar estado">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <ul class="dropdown-menu" aria-labelledby="dropdownEstado{{ $pedido->idVenta }}">
                                            @foreach($estados as $key => $estado)
                                                <li>
                                                    <a class="dropdown-item cambiar-estado" 
                                                       href="#" 
                                                       data-pedido-id="{{ $pedido->idVenta }}" 
                                                       data-estado="{{ $key }}"
                                                       data-estado-actual="{{ $estadoKey }}">
                                                        <span class="badge bg-{{ $estado['badge'] }} me-2">{{ $estado['icon'] }}</span>
                                                        {{ $estado['nombre'] }}
                                                        @if($key == $estadoKey)
                                                            <i class="fas fa-check text-success ms-2"></i>
                                                        @endif
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
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
    console.log('Script cargado');
    
    // Mapa de estados con sus configuraciones
    const estadosConfig = {
        '0': { nombre: 'En Diseño', badge: 'info', icon: '🎨' },
        '1': { nombre: 'Producción', badge: 'warning', icon: '⚙️' },
        '2': { nombre: 'Terminado', badge: 'success', icon: '✅' },
        '3': { nombre: 'Entregado', badge: 'primary', icon: '📦' },
        '4': { nombre: 'Cancelado', badge: 'danger', icon: '❌' }
    };

    // Inicializar DataTable
    $(document).ready(function() {
        console.log('Document ready');
        
        const table = $('#pedidosTable').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json"
            },
            "responsive": true,
            "order": [
                [0, "desc"]
            ],
            "pageLength": 25,
            "drawCallback": function() {
                console.log('DataTable dibujado');
                // Reinicializar dropdowns de Bootstrap después de cada redibujado
                initDropdowns();
            }
        });

        // Inicializar dropdowns
        function initDropdowns() {
            console.log('Inicializando dropdowns');
            $('.dropdown-toggle').each(function() {
                if (!$(this).hasClass('dropdown-initialized')) {
                    $(this).addClass('dropdown-initialized');
                    console.log('Dropdown inicializado:', $(this).attr('id'));
                }
            });
        }

        // Inicializar al cargar
        initDropdowns();

        // Cambiar estado mediante AJAX
        $(document).on('click', '.cambiar-estado', function(e) {
            e.preventDefault();
            console.log('Click en cambiar estado');
            
            const pedidoId = $(this).data('pedido-id');
            const nuevoEstado = $(this).data('estado');
            const estadoActual = $(this).data('estado-actual');
            
            console.log('Pedido ID:', pedidoId, 'Estado actual:', estadoActual, 'Nuevo estado:', nuevoEstado);
            
            // No hacer nada si es el mismo estado
            if (nuevoEstado === estadoActual) {
                console.log('Mismo estado, no hacer nada');
                return;
            }

            const badge = $('#badge-' + pedidoId);
            const estadoInfo = estadosConfig[nuevoEstado];
            
            console.log('Estado info:', estadoInfo);
            
            // Mostrar loading en el badge
            const badgeOriginal = badge.html();
            badge.html('<span class="spinner-border spinner-border-sm me-1"></span>Actualizando...');
            badge.removeClass().addClass('badge bg-secondary');

            // Cerrar dropdown
            $(this).closest('.dropdown-menu').prev('.dropdown-toggle').dropdown('hide');

            // Realizar petición AJAX
            console.log('Enviando petición AJAX a:', '/pedidos/' + pedidoId + '/estado');
            $.ajax({
                url: '/pedidos/' + pedidoId + '/estado',
                type: 'PATCH',
                data: {
                    _token: '{{ csrf_token() }}',
                    estadoPedido: nuevoEstado
                },
                success: function(response) {
                    console.log('Respuesta exitosa:', response);
                    // Actualizar badge con el nuevo estado
                    badge.html(estadoInfo.icon + ' ' + estadoInfo.nombre);
                    badge.removeClass().addClass('badge bg-' + estadoInfo.badge + ' estado-badge');
                    
                    // Actualizar el data-estado-actual en todos los items del dropdown
                    $('#dropdownEstado' + pedidoId).next('.dropdown-menu').find('.cambiar-estado').attr('data-estado-actual', nuevoEstado);
                    
                    // Mostrar mensaje de éxito
                    mostrarMensaje('success', '✓ Estado actualizado correctamente a: ' + estadoInfo.nombre);
                },
                error: function(xhr, status, error) {
                    console.error('Error AJAX:', status, error);
                    console.error('Respuesta:', xhr.responseText);
                    
                    // Restaurar badge original
                    badge.html(badgeOriginal);
                    badge.removeClass().addClass('badge bg-' + estadosConfig[estadoActual].badge + ' estado-badge');
                    
                    // Mostrar mensaje de error
                    const mensaje = xhr.responseJSON?.message || 'Error al actualizar el estado';
                    mostrarMensaje('error', '✗ ' + mensaje);
                }
            });
        });

        // Función para mostrar mensajes temporales
        function mostrarMensaje(tipo, texto) {
            const alertClass = tipo === 'success' ? 'alert-success' : 'alert-danger';
            const iconClass = tipo === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle';
            
            const alerta = $('<div class="alert ' + alertClass + ' alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3" role="alert" style="z-index: 9999; min-width: 300px;">' +
                '<i class="fas ' + iconClass + ' me-2"></i>' + texto +
                '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
                '</div>');
            
            $('body').append(alerta);
            
            // Auto-cerrar después de 3 segundos
            setTimeout(function() {
                alerta.alert('close');
            }, 3000);
        }
    });
</script>

<style>
    .estado-badge {
        min-width: 120px;
        display: inline-block;
        text-align: center;
    }
    
    .dropdown-item.cambiar-estado:hover {
        background-color: #f8f9fa;
        cursor: pointer;
    }
    
    .dropdown-item .badge {
        width: 30px;
        display: inline-block;
        text-align: center;
    }
</style>
@endpush