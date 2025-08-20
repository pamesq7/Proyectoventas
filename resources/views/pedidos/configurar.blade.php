@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">
        <i class="fas fa-cog me-2"></i>Configurar Producto
    </h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('pedidos.catalogo') }}">Catálogo</a></li>
        <li class="breadcrumb-item active">Configurar</li>
    </ol>

    <div class="row">
        {{-- Imagen del producto --}}
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-image me-2"></i>Vista del Producto
                    </h5>
                </div>
                <div class="card-body text-center">
                    @php
                        $imagenSrc = null;
                        if ($producto->foto) {
                            $imagenSrc = asset('storage/' . $producto->foto);
                        } elseif ($producto->diseno && $producto->diseno->archivo) {
                            $imagenSrc = asset('storage/' . $producto->diseno->archivo);
                        }
                    @endphp
                    
                    @if($imagenSrc)
                        <img src="{{ $imagenSrc }}" 
                             id="imagenProducto"
                             class="img-fluid rounded shadow" 
                             style="max-height: 400px;"
                             alt="{{ $producto->nombre }}">
                    @else
                        <div class="bg-light d-flex align-items-center justify-content-center rounded" 
                             style="height: 400px;">
                            <i class="fas fa-image fa-4x text-muted"></i>
                        </div>
                    @endif

                    <div class="mt-3">
                        <h4>{{ $producto->nombre }}</h4>
                        <p class="text-muted">{{ $producto->descripcion }}</p>
                        <h3 class="text-success">
                            $<span id="precioFinal">{{ number_format($producto->precioVenta, 0) }}</span>
                        </h3>
                    </div>
                </div>
            </div>
        </div>

        {{-- Formulario de configuración --}}
        <div class="col-lg-6">
            <form action="{{ route('pedidos.agregar-carrito') }}" method="POST" enctype="multipart/form-data" id="formConfigurar">
                @csrf
                <input type="hidden" name="idProducto" value="{{ $producto->idProducto }}">

                {{-- Información básica --}}
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-info-circle me-2"></i>Información Básica
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Cantidad *</label>
                                    <input type="number" 
                                           class="form-control" 
                                           name="cantidad" 
                                           value="1" 
                                           min="1" 
                                           max="100" 
                                           required
                                           id="inputCantidad">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Talla *</label>
                                    <select class="form-select" name="idTalla" required id="selectTalla">
                                        <option value="">Seleccionar talla</option>
                                        @foreach($tallas as $talla)
                                            <option value="{{ $talla->idTalla }}">{{ $talla->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Características por variante --}}
                @if($producto->variante && $producto->variante->varianteCaracteristicas->count() > 0)
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-sliders-h me-2"></i>Opciones de {{ $producto->variante->nombre }}
                            </h5>
                        </div>
                        <div class="card-body">
                            @foreach($producto->variante->varianteCaracteristicas->groupBy('caracteristica.opcion.nombre') as $opcionNombre => $caracteristicas)
                                <div class="mb-4">
                                    <label class="form-label fw-bold">{{ $opcionNombre }}</label>
                                    <div class="row">
                                        @foreach($caracteristicas as $varCaracteristica)
                                            <div class="col-md-6 mb-2">
                                                <div class="form-check">
                                                    <input class="form-check-input" 
                                                           type="radio" 
                                                           name="caracteristicas[{{ $varCaracteristica->caracteristica->opcion->idOpcion }}]" 
                                                           value="{{ $varCaracteristica->caracteristica->idCaracteristica }}"
                                                           id="caracteristica{{ $varCaracteristica->caracteristica->idCaracteristica }}">
                                                    <label class="form-check-label" 
                                                           for="caracteristica{{ $varCaracteristica->caracteristica->idCaracteristica }}">
                                                        {{ $varCaracteristica->caracteristica->nombre }}
                                                        @if($varCaracteristica->caracteristica->descripcion)
                                                            <small class="text-muted d-block">
                                                                {{ $varCaracteristica->caracteristica->descripcion }}
                                                            </small>
                                                        @endif
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Personalización --}}
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-edit me-2"></i>Personalización
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Nombre Personalizado</label>
                                    <input type="text" 
                                           class="form-control" 
                                           name="nombrePersonalizado" 
                                           maxlength="50"
                                           placeholder="Ej: Juan Pérez">
                                    <small class="text-muted">Para poleras, chaquetas, etc.</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Número Personalizado</label>
                                    <input type="text" 
                                           class="form-control" 
                                           name="numeroPersonalizado" 
                                           maxlength="10"
                                           placeholder="Ej: 10">
                                    <small class="text-muted">Para poleras deportivas</small>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Texto Adicional</label>
                            <textarea class="form-control" 
                                      name="textoAdicional" 
                                      rows="3" 
                                      maxlength="200"
                                      placeholder="Instrucciones especiales, texto adicional, etc."></textarea>
                        </div>
                    </div>
                </div>

                {{-- Diseño personalizado --}}
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-upload me-2"></i>Diseño Personalizado (Opcional)
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Subir tu propio diseño</label>
                            <input type="file" 
                                   class="form-control" 
                                   name="disenoPersonalizado" 
                                   accept=".jpg,.jpeg,.png,.pdf"
                                   id="inputDisenoPersonalizado">
                            <small class="text-muted">
                                Formatos permitidos: JPG, PNG, PDF. Tamaño máximo: 5MB
                            </small>
                        </div>
                        <div id="previewDisenoPersonalizado" class="mt-3" style="display: none;">
                            <img id="imagenPreview" class="img-thumbnail" style="max-height: 150px;">
                        </div>
                    </div>
                </div>

                {{-- Resumen del pedido --}}
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-receipt me-2"></i>Resumen del Pedido
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Producto:</span>
                            <span>{{ $producto->nombre }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Precio unitario:</span>
                            <span>${{ number_format($producto->precioVenta, 0) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Cantidad:</span>
                            <span id="resumenCantidad">1</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between fw-bold">
                            <span>Total:</span>
                            <span class="text-success">$<span id="resumenTotal">{{ number_format($producto->precioVenta, 0) }}</span></span>
                        </div>
                    </div>
                </div>

                {{-- Botones de acción --}}
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <a href="{{ route('pedidos.catalogo') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Volver al Catálogo
                    </a>
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-cart-plus me-2"></i>Agregar al Carrito
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const inputCantidad = document.getElementById('inputCantidad');
    const resumenCantidad = document.getElementById('resumenCantidad');
    const resumenTotal = document.getElementById('resumenTotal');
    const precioUnitario = {{ $producto->precioVenta }};
    const inputDisenoPersonalizado = document.getElementById('inputDisenoPersonalizado');
    const previewDisenoPersonalizado = document.getElementById('previewDisenoPersonalizado');
    const imagenPreview = document.getElementById('imagenPreview');

    // Actualizar resumen cuando cambie la cantidad
    inputCantidad.addEventListener('input', function() {
        const cantidad = parseInt(this.value) || 1;
        const total = cantidad * precioUnitario;
        
        resumenCantidad.textContent = cantidad;
        resumenTotal.textContent = total.toLocaleString();
    });

    // Preview del diseño personalizado
    inputDisenoPersonalizado.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            // Validar tipo de archivo
            const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'application/pdf'];
            if (!allowedTypes.includes(file.type)) {
                alert('Formato de archivo no permitido. Use JPG, PNG o PDF.');
                this.value = '';
                previewDisenoPersonalizado.style.display = 'none';
                return;
            }

            // Validar tamaño (5MB)
            if (file.size > 5 * 1024 * 1024) {
                alert('El archivo es demasiado grande. Máximo 5MB.');
                this.value = '';
                previewDisenoPersonalizado.style.display = 'none';
                return;
            }

            // Mostrar preview solo para imágenes
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    imagenPreview.src = e.target.result;
                    previewDisenoPersonalizado.style.display = 'block';
                };
                reader.readAsDataURL(file);
            } else {
                // Para PDFs, mostrar solo el nombre
                previewDisenoPersonalizado.innerHTML = `
                    <div class="alert alert-info">
                        <i class="fas fa-file-pdf me-2"></i>
                        Archivo PDF seleccionado: ${file.name}
                    </div>
                `;
                previewDisenoPersonalizado.style.display = 'block';
            }
        } else {
            previewDisenoPersonalizado.style.display = 'none';
        }
    });

    // Validación del formulario
    document.getElementById('formConfigurar').addEventListener('submit', function(e) {
        const talla = document.getElementById('selectTalla').value;
        if (!talla) {
            e.preventDefault();
            alert('Por favor selecciona una talla.');
            return;
        }

        const cantidad = parseInt(inputCantidad.value);
        if (!cantidad || cantidad < 1) {
            e.preventDefault();
            alert('La cantidad debe ser mayor a 0.');
            return;
        }
    });
});
</script>
@endpush
@endsection
