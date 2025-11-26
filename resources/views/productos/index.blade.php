@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Gestión de Productos</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Productos</li>
    </ol>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('successdelete'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('successdelete') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between">
            <div>
                <i class="fas fa-boxes me-1"></i>
                Lista de Productos
            </div>
            <div>
                <a href="{{ route('productos.create') }}" class="btn btn-primary btn-sm me-2">
                    <i class="fas fa-plus me-1"></i> Nuevo Producto
                </a>
            </div>
        </div>

        <div class="card-body">
            @if($productos->count())
                <div class="table-responsive">
                    <table class="table table-striped" id="productosTable">
                        <thead class="table-dark">
                            <tr>
                                <th>Foto</th>
                                <th>SKU</th>
                                <th>Nombre</th>
                                <th>Categoría</th>
                                <th>Precio</th>
                                <th>Creado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($productos as $producto)
                            <tr>
                                <td>
                                    @if($producto->foto)
                                        <img src="{{ asset('storage/' . $producto->foto) }}"
                                            class="img-thumbnail"
                                            style="width:50px; height:50px; object-fit:cover;">
                                    @else
                                        <div class="bg-light d-flex justify-content-center align-items-center"
                                             style="width:50px; height:50px;">
                                            <i class="fas fa-image text-muted"></i>
                                        </div>
                                    @endif
                                </td>
                                <td><code>{{ $producto->SKU }}</code></td>
                                <td>{{ $producto->nombre }}</td>
                                <td>{{ $producto->categoria->nombreCategoria ?? 'Sin categoría' }}</td>
                                <td>Bs. {{ number_format($producto->precioVenta, 2) }}</td>
                                <td>{{ $producto->created_at?->format('d/m/Y') }}</td>

                                <td>
                                    <a href="{{ route('productos.edit', $producto->idProducto) }}"
                                        class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    <form action="{{ route('productos.destroy', $producto->idProducto) }}"
                                        method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger btn-sm"
                                            onclick="return confirm('¿Eliminar este producto?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>

                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <h5 class="text-center text-muted">No hay productos registrados</h5>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#productosTable').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json"
        }
    });
});
</script>
@endpush
