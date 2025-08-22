@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Nuevo Pedido</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('pedidos.index') }}">Pedidos</a></li>
        <li class="breadcrumb-item active">Nuevo</li>
    </ol>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
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
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <i class="fas fa-plus me-1"></i>
                Crear Nuevo Pedido
            </div>
            <a href="{{ route('pedidos.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Volver a Pedidos
            </a>
        </div>
        <div class="card-body">
            <form id="formNuevoPedido" action="{{ route('pedidos.guardar-nuevo') }}" method="POST">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Cliente (buscable) *</label>
                        @php($oldClienteSel = old('clienteSeleccionado'))
                        <input type="text" id="clienteSearch" class="form-control" list="clientesList" placeholder="Escribe para buscar..." autocomplete="off" required>
                        <datalist id="clientesList">
                            <optgroup label="Clientes naturales"></optgroup>
                            @foreach($clientesNaturales as $c)
                                @php($val = 'natural:' . $c->idCliente)
                                <option value="{{ $c->nombre }}" data-value="{{ $val }}">{{ $c->nombre }}</option>
                            @endforeach
                            <optgroup label="Establecimientos"></optgroup>
                            @foreach($clientesEstablecimientos as $e)
                                @php($val = 'establecimiento:' . $e->idEstablecimiento)
                                <option value="{{ $e->nombreEstablecimiento }}" data-value="{{ $val }}">{{ $e->nombreEstablecimiento }}</option>
                            @endforeach
                        </datalist>
                        <input type="hidden" name="clienteSeleccionado" id="clienteSeleccionado" value="{{ $oldClienteSel }}">
                        <small class="text-muted">Empieza a escribir el nombre y elige de la lista.</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Fecha de Entrega *</label>
                        <input type="date" name="fechaEntrega" class="form-control" value="{{ old('fechaEntrega') }}" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Lugar de Entrega *</label>
                        <input type="text" name="lugarEntrega" class="form-control" value="{{ old('lugarEntrega','Recojo en tienda') }}" required>
                    </div>

                    <hr class="mt-3"/>

                    <div class="col-12">
                        <div class="card shadow-sm border-0">
                            <div class="card-header py-2 bg-light">
                                <strong>Producto y personalización</strong>
                            </div>
                            <div class="card-body">
                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <label class="form-label">Producto (solo IDs 1–4) *</label>
                                        <select name="idProducto" id="idProducto" class="form-select" required>
                                            <option value="">Seleccionar producto</option>
                                            @foreach($productos as $p)
                                                <option value="{{ $p->idProducto }}" data-nombre="{{ strtolower($p->nombre) }}" {{ old('idProducto')==$p->idProducto ? 'selected' : '' }}>
                                                    [#{{ $p->idProducto }}] {{ $p->nombre }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <small class="text-muted">Selecciona el producto. La variante se resolverá automáticamente.</small>
                                    </div>
                                </div>

                                <div class="mt-3" id="opcionesContainer" style="display:none;">
                                    <div class="row g-3" id="caracteristicasContainer">
                                        <!-- Aquí se renderizan opciones y características -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 mt-2">
                        <div class="table-responsive">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <strong class="mb-0">Datos del producto</strong>
                                <button type="button" id="btnAddRow" class="btn btn-sm btn-primary">
                                    <i class="fas fa-plus"></i> Agregar fila
                                </button>
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

                    
                    

                    <div class="col-12 mt-3">
                        <div class="alert alert-info d-flex align-items-center" role="alert">
                            <i class="fas fa-image me-2"></i>
                            <div>
                                Tu diseño ya fue subido y será aplicado a este pedido.
                                @if(session('disenoTemporal'))
                                    <div class="mt-2">
                                        <img src="{{ asset('storage/' . session('disenoTemporal')) }}" alt="Diseño" class="img-thumbnail" style="max-height:140px;object-fit:contain;">
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="col-12 d-flex gap-2 mt-2">
                        <a href="{{ route('pedidos.personalizar') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-1"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save me-1"></i> Guardar Pedido
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Eliminado: cargarVariantesActivas (ya no se muestra selector de variante)

    async function cargarCaracteristicas(idVariante) {
        const cont = document.getElementById('caracteristicasContainer');
        const wrap = document.getElementById('opcionesContainer');
        cont.innerHTML = '';
        wrap.style.display = 'none';
        if (!idVariante) return;
        try {
            const res = await fetch(`{{ url('api/variante') }}/${idVariante}/caracteristicas`);
            const data = await res.json();
            const opciones = data.opciones || [];
            opciones.forEach(op => {
                const col = document.createElement('div');
                col.className = 'col-md-3';
                const label = document.createElement('label');
                label.className = 'form-label';
                label.textContent = op.nombreOpcion;
                const select = document.createElement('select');
                select.className = 'form-select';
                select.name = `caracteristicas[${op.idOpcion || 'otros'}]`;
                const placeholder = document.createElement('option');
                placeholder.value = '';
                placeholder.textContent = 'Seleccionar';
                select.appendChild(placeholder);
                (op.caracteristicas || []).forEach(c => {
                    const o = document.createElement('option');
                    o.value = c.idCaracteristica; o.textContent = c.nombre;
                    select.appendChild(o);
                });
                col.appendChild(label);
                col.appendChild(select);
                cont.appendChild(col);
            });
            if (opciones.length) wrap.style.display = '';
        } catch (e) {
            console.error(e);
        }
    }

    // Al cambiar producto, cargar sus opciones/características desde producto_opcions
    async function onProductoChange(idProducto) {
        const cont = document.getElementById('caracteristicasContainer');
        const wrap = document.getElementById('opcionesContainer');
        cont.innerHTML = '';
        wrap.style.display = 'none';
        if (!idProducto) return;
        try {
            const urlOpts = `{{ url('api/producto') }}/${idProducto}/opciones`;
            const res = await fetch(urlOpts, { headers: { 'Accept': 'application/json' } });
            if (!res.ok) { console.error('Error opciones por producto', await res.text()); return; }
            const data = await res.json();
            const opciones = data.opciones || [];
            opciones.forEach(op => {
                const col = document.createElement('div');
                col.className = 'col-md-3';
                const label = document.createElement('label');
                label.className = 'form-label';
                label.textContent = op.nombreOpcion;
                const select = document.createElement('select');
                select.className = 'form-select';
                select.name = `caracteristicas[${op.idOpcion || 'otros'}]`;
                const placeholder = document.createElement('option');
                placeholder.value = '';
                placeholder.textContent = 'Seleccionar';
                select.appendChild(placeholder);
                (op.caracteristicas || []).forEach(c => {
                    const o = document.createElement('option');
                    o.value = c.idCaracteristica; o.textContent = c.nombre;
                    select.appendChild(o);
                });
                col.appendChild(label);
                col.appendChild(select);
                cont.appendChild(col);
            });
            if (opciones.length) wrap.style.display = '';
        } catch (e) {
            console.error(e);
        }
    }

    document.getElementById('idProducto').addEventListener('change', async (e) => {
        const idProd = e.target.value;
        toggleNumeroByProducto();
        await onProductoChange(idProd);
    });

    // Al cargar, si viene producto seleccionado, cargar sus características
    (function initOnLoad(){
        const sel = document.getElementById('idProducto');
        toggleNumeroByProducto();
        if (sel && sel.value) { onProductoChange(sel.value); }
        // Restaurar cliente seleccionado (si viene de old())
        const hiddenCli = document.getElementById('clienteSeleccionado');
        const inputCli = document.getElementById('clienteSearch');
        if (hiddenCli && hiddenCli.value) {
            const opt = findOptionByDataValue(hiddenCli.value);
            if (opt) inputCli.value = opt.value;
        }
    })();

    // Mostrar/ocultar campo Número según producto seleccionado
    function toggleNumeroByProducto() {
        const sel = document.getElementById('idProducto');
        const opt = sel ? sel.options[sel.selectedIndex] : null;
        const nombre = (opt && opt.dataset && opt.dataset.nombre) ? opt.dataset.nombre : '';
        const thNum = document.getElementById('thNumero');
        const filas = document.querySelectorAll('#tbodyItems tr.item-row');
        // Regla: si contiene 'chamarra' => ocultar número; si contiene 'polera' => mostrar número
        const esChamarra = nombre.includes('chamarra');
        const esPolera = nombre.includes('polera');
        const mostrarNumero = esPolera && !esChamarra; // prioridad a polera si ambos
        if (thNum) thNum.style.display = mostrarNumero ? '' : 'none';
        filas.forEach(tr => {
            const colNum = tr.querySelector('.col-numero');
            const inp = tr.querySelector('.inp-numero');
            if (colNum) colNum.style.display = mostrarNumero ? '' : 'none';
            if (!mostrarNumero && inp) inp.value = '';
        });
    }

    // Agregar y quitar filas
    const tbody = document.getElementById('tbodyItems');
    document.getElementById('btnAddRow').addEventListener('click', () => addRow());
    tbody.addEventListener('click', (e) => {
        if (e.target.closest('.btnRemoveRow')) {
            const rows = tbody.querySelectorAll('tr.item-row');
            if (rows.length > 1) {
                e.target.closest('tr.item-row').remove();
                toggleNumeroByProducto();
            }
        }
    });

    function addRow() {
        const tr = document.createElement('tr');
        tr.className = 'item-row';
        tr.innerHTML = `
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
        `;
        tbody.appendChild(tr);
        toggleNumeroByProducto();
    }
    // --- Búsqueda cliente con datalist ---
    function findOptionByDataValue(dataVal) {
        const opts = document.querySelectorAll('#clientesList option');
        for (const o of opts) { if (o.dataset.value === dataVal) return o; }
        return null;
    }
    function findOptionByLabel(label) {
        const opts = document.querySelectorAll('#clientesList option');
        for (const o of opts) { if (o.value === label) return o; }
        return null;
    }
    const inputCli = document.getElementById('clienteSearch');
    const hiddenCli = document.getElementById('clienteSeleccionado');
    inputCli.addEventListener('input', () => {
        const match = findOptionByLabel(inputCli.value);
        hiddenCli.value = match ? (match.dataset.value || '') : '';
    });
    // Validar antes de enviar: debe haber coincidencia
    document.getElementById('formNuevoPedido').addEventListener('submit', (e) => {
        const match = findOptionByLabel(inputCli.value);
        if (!match) {
            e.preventDefault();
            alert('Por favor, selecciona un cliente válido de la lista.');
            inputCli.focus();
        }
    });
    // Eliminado: toggleClientes, ahora hay un solo selector combinado de clientes
</script>
@endpush
