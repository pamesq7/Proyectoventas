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
        <div class="col-12 mt-2">
                                <div class="table-responsive">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <strong class="mb-0">Datos del producto</strong>
                                        <div class="d-flex gap-2">
                                            <button type="button" id="btnAddRow" class="btn btn-sm btn-primary">
                                                <i class="fas fa-plus"></i> Agregar fila
                                            </button>
                                            <button type="submit" form="formNuevoPedido" class="btn btn-sm btn-success">
                                                <i class="fas fa-save"></i> Guardar Pedido
                                            </button>
                                        </div>
                                    </div>
                                    <table class="table table-sm align-middle mb-0" id="tablaItems">
                                        <thead class="table-light">
                                            <tr>
                                                <th style="width: 20%">Talla *</th>
                                                <th style="width: 10%">Cantidad *</th>
                                                <th style="width: 20%">Nombre</th>
                                                <th style="width: 20%" id="thNumero">Número</th>
                                                <th>Observaciones</th>
                                                <th style="width: 5%">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tbodyItems">
                                            <tr class="item-row">
                                                <td>
                                                    <select name="idTalla[]" class="form-select form-select-sm sel-talla" required>
                                                        <option value="">Seleccionar talla</option>
                                                        @foreach($tallas as $t)
                                                            <option value="{{ $t->idTalla }}">{{ $t->nombre }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="number" name="cantidad[]" class="form-control form-control-sm inp-cantidad" min="1" value="1" required>
                                                </td>
                                                <td>
                                                    <input type="text" name="nombrePersonalizado[]" class="form-control form-control-sm inp-nombre" placeholder="Nombre">
                                                </td>
                                                <td class="col-numero">
                                                    <input type="text" name="numeroPersonalizado[]" class="form-control form-control-sm inp-numero" placeholder="Número">
                                                </td>
                                                <td>
                                                    <input type="text" name="observaciones[]" class="form-control form-control-sm inp-obs" placeholder="Detalles adicionales...">
                                                </td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-sm btn-outline-danger btnRemoveRow" title="Quitar fila">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
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
    const precioUnitario = {{ number_format($producto->precioVenta, 2, '.', '') }};
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
