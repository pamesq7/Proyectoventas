@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">

            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0"><i class="fas fa-plus me-2"></i>Crear Nuevo Producto</h4>
                            <small class="text-muted">Agrega un nuevo producto al inventario</small>
                        </div>
                        <a href="{{ route('productos.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i>Volver
                        </a>
                    </div>
                </div>

                <div class="card-body">

                    {{-- ALERTAS --}}

                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger">
                            <strong>Corrige los siguientes errores:</strong>
                            <ul class="mt-2 mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- FORMULARIO  --}}
                    <form action="{{ route('productos.store') }}" method="POST" enctype="multipart/form-data" id="formProducto">
                        @csrf

                        <div class="row">

                            {{-- COLUMNA IZQUIERDA --}}
                            <div class="col-md-8">

                                <div class="row">
                                    <div class="col-md-6">
                                        <label class="form-label">SKU *</label>
                                        <input type="text" name="SKU" class="form-control" value="{{ old('SKU') }}" required>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Nombre *</label>
                                        <input type="text" name="nombre" class="form-control" value="{{ old('nombre') }}" required>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Descripción</label>
                                    <textarea name="descripcion" class="form-control" rows="3">{{ old('descripcion') }}</textarea>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <label class="form-label">Categoría *</label>
                                        <select name="idCategoria" class="form-select" required>
                                            <option value="">Seleccione</option>
                                            @foreach($categorias as $categoria)
                                                <option value="{{ $categoria->idCategoria }}">{{ $categoria->nombreCategoria }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Variante</label>
                                        <select name="idVariante" class="form-select">
                                            <option value="">Sin variante</option>
                                            @foreach($variantes as $v)
                                                <option value="{{ $v->idVariante }}">{{ $v->nombre }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                {{-- TIPO PRODUCTO  --}}
                                <div class="col-md-6 mt-3">
                                    <label class="form-label">Tipo de Producto *</label>
                                    <select name="tipoProducto" id="tipoProducto" class="form-select" onchange="toggleComponentesPack()" required>
                                        <option value="producto">Producto Individual</option>
                                        <option value="pack">Pack (Conjunto)</option>
                                    </select>
                                </div>

                                {{-- SECCIÓN PACK (OCULTA) --}}
                                <div id="componentesPackSection" class="card mt-4" style="display:none;">
                                    <div class="card-header bg-light">
                                        <strong><i class="fas fa-boxes me-2"></i>Componentes del Pack</strong>
                                    </div>

                                    <div class="card-body">

                                        <div class="row mb-3">
                                            <div class="col-md-10">
                                                <label>Agregar producto al pack:</label>
                                                <select id="productoComponente" class="form-select">
                                                    <option value="">Seleccionar</option>
                                                    @foreach($productosIndividuales as $p)
                                                        <option value="{{ $p->idProducto }}"
                                                            data-nombre="{{ $p->nombre }}"
                                                            data-precio="{{ $p->precioVenta }}"
                                                            data-sku="{{ $p->SKU }}">
                                                            {{ $p->SKU }} - {{ $p->nombre }} (Bs. {{ number_format($p->precioVenta,2) }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-2 d-flex align-items-end">
                                                <button type="button" class="btn btn-primary w-100" onclick="agregarComponente()">
                                                    <i class="fas fa-plus"></i> Agregar
                                                </button>
                                            </div>
                                        </div>

                                        {{-- TABLA PACK --}}
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <thead class="table-dark">
                                                    <tr>
                                                        <th>SKU</th>
                                                        <th>Producto</th>
                                                        <th>Precio Unit.</th>
                                                        <th>Acciones</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="tbodyComponentes"></tbody>

                                                <tfoot>
                                                    <tr class="table-active">
                                                        <td colspan="2" class="text-end"><strong>Total Pack:</strong></td>
                                                        <td id="totalPack" class="fw-bold text-success">0.00 Bs</td>
                                                        <td></td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>

                                        {{-- Hidden final --}}
                                        <input type="hidden" name="componentesPack" id="componentesPack">
                                    </div>
                                </div>

                                {{-- PRECIO --}}
                                <div class="row mt-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Precio de Venta *</label>
                                        <div class="input-group">
                                            <span class="input-group-text">Bs</span>
                                            <input type="number" min="0" step="0.01" name="precioVenta" id="precioVenta" class="form-control" required>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Precio Producción</label>
                                        <div class="input-group">
                                            <span class="input-group-text">Bs</span>
                                            <input type="number" min="0" step="0.01" name="precioProduccion" class="form-control">
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-3">
                                    <label class="form-label">Pedido mínimo</label>
                                    <input type="number" name="pedidoMinimo" class="form-control" min="1" value="1">
                                </div>

                                <div class="mt-3">
                                    <label class="form-label">Estado *</label>
                                    <select name="estado" class="form-select" required>
                                        <option value="1">Activo</option>
                                        <option value="0">Inactivo</option>
                                    </select>
                                </div>

                            </div> {{-- FIN IZQUIERDA --}}

                            {{-- COLUMNA DERECHA --}}
                            <div class="col-md-4">

                                <label class="form-label">Imagen del Producto</label>
                                <input type="file" name="foto" id="foto" class="form-control" accept="image/*">

                                <div id="imagePreview" class="mt-3" style="display:none;">
                                    <img id="preview" class="img-thumbnail" style="max-width:200px;">
                                </div>

                                {{-- DISEÑOS --}}
                                <div class="mt-4">
                                    <label class="form-label">Diseños vinculados</label>
                                    <button type="button" class="btn btn-outline-primary btn-sm w-100" data-bs-toggle="modal" data-bs-target="#disenosModal">
                                        <i class="fas fa-plus"></i> Seleccionar Diseños
                                    </button>

                                    <div id="disenosSeleccionados" class="border mt-3 p-3 bg-light">
                                        <p class="text-muted text-center">Sin diseños seleccionados</p>
                                    </div>

                                    <input type="hidden" id="disenosVinculados" name="disenosVinculados">
                                </div>

                            </div>
                        </div>

                        <div class="mt-4 d-flex justify-content-end">
                            <button class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>Guardar Producto
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- @include('productos.partials.disenos-modal') --}}

@endsection


@push('scripts')

<script>
/* ===========================
    VARIABLES GLOBALES
=========================== */
let componentesPack = [];

/* ===========================
    MOSTRAR / OCULTAR PACK
=========================== */
function toggleComponentesPack() {
    const tipo = document.getElementById('tipoProducto').value;
    const section = document.getElementById('componentesPackSection');

    if (tipo === 'pack') {
        section.style.display = 'block';
        document.getElementById('precioVenta').readOnly = true;
    } else {
        section.style.display = 'none';
        document.getElementById('precioVenta').readOnly = false;
        componentesPack = [];
        actualizarTablaComponentes();
    }
}

/* ===========================
    AGREGAR COMPONENTE
=========================== */
function agregarComponente() {
    const select = document.getElementById('productoComponente');
    const idProducto = select.value;

    if (!idProducto) return alert("Seleccione un producto.");

    const nombre = select.options[select.selectedIndex].dataset.nombre;
    const sku = select.options[select.selectedIndex].dataset.sku;
    const precio = parseFloat(select.options[select.selectedIndex].dataset.precio);

    if (componentesPack.some(c => c.idProducto == idProducto)) {
        return alert("Este producto ya está en el pack.");
    }

    componentesPack.push({
        idProducto,
        nombre,
        sku,
        precio
    });

    actualizarTablaComponentes();
    select.value='';
}

/* ===========================
    ACTUALIZAR TABLA PACK
=========================== */
function actualizarTablaComponentes() {
    const tbody = document.getElementById('tbodyComponentes');
    const totalBox = document.getElementById('totalPack');

    tbody.innerHTML = "";
    let total = 0;

    componentesPack.forEach(c => {
        total += c.precio;

        tbody.innerHTML += `
            <tr>
                <td>${c.sku}</td>
                <td>${c.nombre}</td>
                <td>Bs. ${c.precio.toFixed(2)}</td>
                <td class="text-center">
                    <button class="btn btn-danger btn-sm" onclick="eliminarComponente('${c.idProducto}')">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
    });

    totalBox.innerHTML = total.toFixed(2) + " Bs";

    document.getElementById('precioVenta').value = total.toFixed(2);
    document.getElementById('componentesPack').value = JSON.stringify(componentesPack);
}

/* ===========================
    ELIMINAR COMPONENTE
=========================== */
function eliminarComponente(idProducto) {
    componentesPack = componentesPack.filter(c => c.idProducto != idProducto);
    actualizarTablaComponentes();
}

/* ===========================
    PREVIEW IMAGEN
=========================== */
document.getElementById('foto').addEventListener('change', e=>{
    const file = e.target.files[0];
    if (!file) return;

    const img = document.getElementById('preview');
    const box = document.getElementById('imagePreview');

    img.src = URL.createObjectURL(file);
    box.style.display = 'block';
});
</script>

@endpush
