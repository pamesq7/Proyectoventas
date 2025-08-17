@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Gestión de Producto</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('productos.index') }}">Productos</a></li>
        <li class="breadcrumb-item active">{{ $producto->nombre }}</li>
    </ol>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-cogs me-2"></i>
                Gestión Avanzada: {{ $producto->nombre }}
            </h5>
        </div>
        <div class="card-body">
            <!-- Pestañas de Navegación -->
            <ul class="nav nav-tabs" id="productTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="datos-basicos-tab" data-bs-toggle="tab" data-bs-target="#datos-basicos" type="button" role="tab">
                        <i class="fas fa-info-circle me-1"></i>Datos Básicos
                    </button>
                </li>
                
            </ul>

            <!-- Contenido de las Pestañas -->
            <div class="tab-content" id="productTabsContent">
                <!-- PESTAÑA 1: DATOS BÁSICOS -->
                <div class="tab-pane fade show active" id="datos-basicos" role="tabpanel">
                    <div class="row mt-4">
                        <div class="col-md-8">
                            <form action="{{ route('productos.update', $producto->idProducto) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                
                                {{-- Mostrar errores de validación --}}
                                @if($errors->any())
                                    <div class="alert alert-danger">
                                        <h6><i class="fas fa-exclamation-triangle me-1"></i>Errores de validación:</h6>
                                        <ul class="mb-0">
                                            @foreach($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="SKU" class="form-label">SKU *</label>
                                            <input type="text" class="form-control" id="SKU" name="SKU" value="{{ old('SKU', $producto->SKU) }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="idCategoria" class="form-label">Categoría *</label>
                                            <select class="form-select" id="idCategoria" name="idCategoria" required>
                                                @foreach($categorias as $categoria)
                                                    <option value="{{ $categoria->idCategoria }}" {{ old('idCategoria', $producto->idCategoria) == $categoria->idCategoria ? 'selected' : '' }}>
                                                        {{ $categoria->nombreCategoria }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="idVariante" class="form-label">Variante</label>
                                            <select class="form-select @error('idVariante') is-invalid @enderror" 
                                                    id="idVariante" name="idVariante">
                                                <option value="">Sin variante</option>
                                                @foreach($variantes as $variante)
                                                    <option value="{{ $variante->idVariante }}" 
                                                            {{ old('idVariante', $producto->idVariante) == $variante->idVariante ? 'selected' : '' }}>
                                                        {{ $variante->nombre }}
                                                        @if($variante->varianteCaracteristicas->count() > 0)
                                                            ({{ $variante->varianteCaracteristicas->count() }} características)
                                                        @endif
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('idVariante')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="estado" class="form-label">Estado *</label>
                                            <select class="form-select" id="estado" name="estado" required>
                                                <option value="1" {{ old('estado', $producto->estado) == 1 ? 'selected' : '' }}>Activo</option>
                                                <option value="0" {{ old('estado', $producto->estado) == 0 ? 'selected' : '' }}>Inactivo</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="nombre" class="form-label">Nombre del Producto *</label>
                                    <input type="text" class="form-control" id="nombre" name="nombre" value="{{ old('nombre', $producto->nombre) }}" required>
                                </div>

                                <div class="mb-3">
                                    <label for="descripcion" class="form-label">Descripción</label>
                                    <textarea class="form-control" id="descripcion" name="descripcion" rows="3">{{ old('descripcion', $producto->descripcion) }}</textarea>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="precioVenta" class="form-label">Precio de Venta *</label>
                                            <input type="number" class="form-control" id="precioVenta" name="precioVenta" value="{{ old('precioVenta', $producto->precioVenta) }}" step="0.01" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="precioProduccion" class="form-label">Precio de Producción</label>
                                            <input type="number" class="form-control" id="precioProduccion" name="precioProduccion" value="{{ old('precioProduccion', $producto->precioProduccion) }}" step="0.01">
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="foto" class="form-label">Foto Principal</label>
                                    @if($producto->foto && file_exists(public_path('storage/' . $producto->foto)))
                                        <div class="mb-2">
                                            <img src="{{ asset('storage/' . $producto->foto) }}" alt="{{ $producto->nombre }}" class="img-thumbnail" style="max-width: 200px;">
                                        </div>
                                    @elseif($producto->foto)
                                        <div class="mb-2">
                                            <div class="alert alert-warning">
                                                <i class="fas fa-exclamation-triangle me-1"></i>
                                                Imagen no encontrada: {{ $producto->foto }}
                                            </div>
                                        </div>
                                    @endif
                                    <input type="file" class="form-control" id="foto" name="foto" accept="image/*">
                                </div>

                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i>Guardar Cambios
                                </button>
                            </form>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">Información del Sistema</h6>
                                    <p><strong>ID:</strong> #{{ $producto->idProducto }}</p>
                                    <p><strong>Creado:</strong> {{ $producto->created_at->format('d/m/Y H:i') }}</p>
                                    <p><strong>Actualizado:</strong> {{ $producto->updated_at->format('d/m/Y H:i') }}</p>
                                    <p><strong>Estado:</strong> 
                                        <span class="badge {{ $producto->estado ? 'bg-success' : 'bg-danger' }}">
                                            {{ $producto->estado ? 'Activo' : 'Inactivo' }}
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- Modal Nueva Variante -->
<div class="modal fade" id="modalNuevaVariante" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nueva Variante</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formNuevaVariante">
                    <input type="hidden" name="producto_id" value="{{ $producto->idProducto }}">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="variante_nombre" class="form-label">Nombre *</label>
                                <input type="text" class="form-control" id="variante_nombre" name="nombre" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="variante_descripcion" class="form-label">Descripción *</label>
                                <input type="text" class="form-control" id="variante_descripcion" name="descripcion" required>
                            </div>
                        </div>
                    </div>


                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="guardarVariante()">Guardar Variante</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Habilitar/deshabilitar input de precio según checkbox
document.querySelectorAll('.caracteristica-check').forEach(function(checkbox) {
    checkbox.addEventListener('change', function() {
        const precioInput = this.closest('.form-check').querySelector('.precio-adicional');
        precioInput.disabled = !this.checked;
        if (!this.checked) {
            precioInput.value = '0';
        }
    });
});

// Guardar nueva variante
function guardarVariante() {
    const form = document.getElementById('formNuevaVariante');
    const formData = new FormData(form);
    
    // Convertir a objeto para envío JSON
    const data = {
        producto_id: formData.get('producto_id'),
        nombre: formData.get('nombre'),
        descripcion: formData.get('descripcion'),
        caracteristicas: []
    };
    
    // Recopilar características seleccionadas
    document.querySelectorAll('.caracteristica-check:checked').forEach(function(checkbox) {
        const caracteristicaId = checkbox.value;
        const precioInput = checkbox.closest('.form-check').querySelector('.precio-adicional');
        
        data.caracteristicas.push({
            id: caracteristicaId,
            precio_adicional: parseFloat(precioInput.value) || 0
        });
    });
    
    if (data.caracteristicas.length === 0) {
        alert('Debe seleccionar al menos una característica');
        return;
    }
    
    // Enviar datos
    fetch('{{ route("productos.storeVariante") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al guardar la variante');
    });
}


</script>
@endpush