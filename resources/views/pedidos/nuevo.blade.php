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
                <input type="hidden" name="tipoCliente" id="tipoCliente" value="">
                <input type="hidden" name="idCliente" id="idCliente" value="">
                <input type="hidden" name="idEstablecimiento" id="idEstablecimiento" value="">
                <div class="row g-3">
                    <div class="col-lg-8">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Fecha de Entrega *</label>
                                <input type="date" name="fechaEntrega" class="form-control" value="{{ old('fechaEntrega') }}" required>
                            </div>
                            <div class="col-md-6">
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
                                                        <option value="{{ $p->idProducto }}" data-nombre="{{ strtolower($p->nombre) }}" data-precio="{{ $p->precioVenta ?? 0 }}" {{ old('idProducto')==$p->idProducto ? 'selected' : '' }}>
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
                    </div>
                    <div class="col-lg-4">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="mb-2">
                                    <label class="form-label">Cliente *</label>
                                    @php($oldClienteSel = old('clienteSeleccionado'))
                                    <div class="input-group">
                                        <a href="{{ url('users/create') }}" class="btn btn-success" title="Agregar nuevo usuario" target="_blank" rel="noopener">
                                            <i class="fas fa-plus"></i>
                                        </a>
                                        <div class="flex-grow-1">
                                            <input type="text" id="clienteFilter" class="form-control mb-1" placeholder="Buscar por CI, nombre o teléfono...">
                                            <select class="form-select" name="clienteSeleccionado" id="clienteSelect" required>
                                                <option value="">Seleccione un cliente</option>
                                                <optgroup label="Clientes naturales"></optgroup>
                                                @foreach($clientesNaturales as $c)
                                                    @php($val = 'natural:' . $c->idCliente)
                                                    @php($doc = $c->user->ci ?? ($c->nit ?? ''))
                                                    @php($tel = $c->user->telefono ?? '')
                                                    @php($nom = $c->user->name ?? 'Cliente')
                                                    @php($label = trim(($doc ? 'CI: '.$doc.' - ' : '') . $nom . ($tel ? ' - Tel: '.$tel : '')))
                                                    <option value="{{ $val }}" data-ci="{{ $doc }}" data-telefono="{{ $tel }}" {{ $oldClienteSel === $val ? 'selected' : '' }}>{{ $label }}</option>
                                                @endforeach
                                                <optgroup label="Establecimientos"></optgroup>
                                                @foreach($clientesEstablecimientos as $e)
                                                    @php($val = 'establecimiento:' . $e->idEstablecimiento)
                                                    @php($doc = $e->nit ?? '')
                                                    @php($tel = $e->representante->telefono ?? '')
                                                    @php($nom = $e->nombreEstablecimiento ?? ($e->razonSocial ?? 'Establecimiento'))
                                                    @php($label = trim(($doc ? 'NIT: '.$doc.' - ' : '') . $nom . ($tel ? ' - Tel: '.$tel : '')))
                                                    <option value="{{ $val }}" data-ci="{{ $doc }}" data-telefono="{{ $tel }}" {{ $oldClienteSel === $val ? 'selected' : '' }}>{{ $label }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label">Tipo Pago *</label>
                                    <select class="form-select" id="tipoPago" name="tipoTransaccion">
                                        <option value="efectivo" selected>Efectivo</option>
                                        <option value="qr">QR</option>
                                        <option value="cheque">Cheque</option>
                                        <option value="transferencia">Transferencia bancaria</option>
                                    </select>
                                </div>
                                <div class="mb-1" id="efectivoGroup">
                                    <label class="form-label">Monto entregado (Bs)</label>
                                    <input type="number" class="form-control" id="montoEntregado" placeholder="Monto entregado por el cliente" step="0.01" min="0">
                                </div>
                                <hr/>
                                <div class="d-flex justify-content-between"><small>Cantidad total</small><small id="uiCantTotal">0</small></div>
                                <div class="mt-1">
                                    <small class="text-muted d-block">Desglose por talla</small>
                                    <div id="uiBreakdownTallas" class="small"></div>
                                </div>
                                <div class="d-flex justify-content-between"><small>Precio unitario</small><small id="uiPrecioUnit">Bs 0.00</small></div>
                                <div class="d-flex justify-content-between"><small>SUBTOTAL</small><small id="uiSubtotal">Bs 0.00</small></div>
                                <div class="d-flex justify-content-between"><small>IGV (18%)</small><small id="uiIgv">Bs 0.00</small></div>
                                <div class="d-flex justify-content-between"><strong>TOTAL</strong><strong id="uiTotal">Bs 0.00</strong></div>
                                <div class="mt-3 p-2 border rounded">
                                    <div id="uiTablaTallas"></div>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label">Pago inicial (Bs)</label>
                                    <input type="number" class="form-control" id="pagoInicial" name="pagoInicial" placeholder="Monto del pago inicial" step="0.01" min="0">
                                </div>
                                <div class="d-flex justify-content-between"><small>Pago inicial</small><small id="uiAdelanto">Bs 0.00</small></div>
                                <div class="d-flex justify-content-between"><small><strong>Saldo</strong></small><small id="uiSaldo"><strong>Bs 0.00</strong></small></div>
                                <hr/>
                                <div class="d-flex justify-content-between text-success"><small>Monto Efectivo</small><small id="uiEfectivo">Bs 0.00</small></div>
                                <div class="d-flex justify-content-between text-danger"><small>Vuelto</small><small id="uiVuelto">Bs 0.00</small></div>
                            </div>
                        </div>
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

    // Mapa de precios por talla del producto actual: { idTalla: precioUnitario }
    let tallaPriceMap = new Map();

    async function loadTallaPrecios(idProducto) {
        tallaPriceMap = new Map();
        if (!idProducto) return;
        try {
            const url = `{{ url('api/producto') }}/${idProducto}/tallas-precios`;
            const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
            if (!res.ok) { console.error('Error tallas-precios', await res.text()); return; }
            const data = await res.json();
            (data.precios || []).forEach(p => {
                tallaPriceMap.set(String(p.idTalla), Number(p.precioUnitario || 0));
            });
        } catch (e) {
            console.error('fetch tallas-precios', e);
        }
    }

    document.getElementById('idProducto').addEventListener('change', async (e) => {
        const idProd = e.target.value;
        toggleNumeroByProducto();
        await onProductoChange(idProd);
        await loadTallaPrecios(idProd);
        recalcTotales();
    });

    // Al cargar, si viene producto seleccionado, cargar sus características
    (function initOnLoad(){
        const sel = document.getElementById('idProducto');
        toggleNumeroByProducto();
        if (sel && sel.value) { onProductoChange(sel.value); loadTallaPrecios(sel.value); }
        // Inicializar UI de pago
        updatePagoUI();
        recalcTotales();
    })();

    // --- Búsqueda AJAX de clientes por CI / nombre / teléfono ---
    function debounce(fn, delay) {
        let t; return (...args) => { clearTimeout(t); t = setTimeout(() => fn.apply(null, args), delay); };
    }
    (function initClienteFilter(){
        const input = document.getElementById('clienteFilter');
        const select = document.getElementById('clienteSelect');
        if (!input || !select) return;
        const renderResults = (results) => {
            // Reconstruir opciones: placeholder + optgroups
            select.innerHTML = '';
            const ph = document.createElement('option'); ph.value=''; ph.textContent='Seleccione un cliente';
            select.appendChild(ph);
            const grpNat = document.createElement('optgroup'); grpNat.label = 'Clientes naturales';
            const grpEst = document.createElement('optgroup'); grpEst.label = 'Establecimientos';
            (results || []).forEach(r => {
                const o = document.createElement('option');
                o.value = r.value; o.textContent = r.label;
                if (r.type === 'natural') grpNat.appendChild(o); else grpEst.appendChild(o);
            });
            select.appendChild(grpNat); select.appendChild(grpEst);
        };
        const search = debounce(async () => {
            const q = input.value.trim();
            if (!q) { renderResults([]); return; }
            try {
                const url = `{{ url('api/clients') }}`.replace('clients','clientes') + `/search?q=${encodeURIComponent(q)}`;
                const res = await fetch(url, { headers: { 'Accept':'application/json' } });
                if (!res.ok) { console.error('buscar clientes', await res.text()); return; }
                const data = await res.json();
                renderResults(data.results || []);
            } catch (e) {
                console.error('fetch clientes/search', e);
            }
        }, 300);
        input.addEventListener('input', search);
    })();

    // Mapear selección de cliente al backend (tipoCliente, idCliente/idEstablecimiento)
    (function initClienteSelection(){
        const select = document.getElementById('clienteSelect');
        const tipo = document.getElementById('tipoCliente');
        const idCli = document.getElementById('idCliente');
        const idEst = document.getElementById('idEstablecimiento');
        if (!select || !tipo || !idCli || !idEst) return;
        const apply = () => {
            const v = String(select.value || '');
            tipo.value = ''; idCli.value = ''; idEst.value = '';
            if (v.startsWith('natural:')) {
                tipo.value = 'natural';
                idCli.value = v.split(':')[1] || '';
            } else if (v.startsWith('establecimiento:')) {
                tipo.value = 'establecimiento';
                idEst.value = v.split(':')[1] || '';
            }
        };
        select.addEventListener('change', apply);
        // Por si viene precargado por old()
        apply();
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
                recalcTotales();
            }
        }
    });
    // Recalcular al cambiar tallas en cualquier fila
    tbody.addEventListener('change', (e) => {
        if (e.target.classList.contains('sel-talla')) { recalcTotales(); }
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
        recalcTotales();
    }
    // Validación con select de cliente
    document.getElementById('formNuevoPedido').addEventListener('submit', (e) => {
        const selCli = document.getElementById('clienteSelect');
        const tipo = document.getElementById('tipoCliente');
        if (!selCli || !selCli.value) {
            e.preventDefault();
            alert('Selecciona un cliente.');
            if (selCli) selCli.focus();
        }
        // Validación extra de mapeo
        if (selCli && selCli.value) {
            const v = selCli.value;
            if (v.startsWith('natural:') && !tipo.value) { tipo.value = 'natural'; }
            if (v.startsWith('establecimiento:') && !tipo.value) { tipo.value = 'establecimiento'; }
        }
    });

    // --- Cálculo de totales y vuelto (sin afectar backend) ---
    function getPrecioUnitarioBase() {
        const sel = document.getElementById('idProducto');
        if (!sel || !sel.value) return 0;
        const opt = sel.options[sel.selectedIndex];
        const p = parseFloat(opt?.dataset?.precio || '0');
        return isNaN(p) ? 0 : p;
    }

    function normalizarTalla(nombre) {
        return String(nombre || '').trim().toLowerCase();
    }

    function grupoDeTalla(nombre) {
        const t = normalizarTalla(nombre);
        // Númericas primero
        if (/(^|\D)(14|12)($|\D)/.test(t)) return '14_12';
        if (/(^|\D)(10|8)($|\D)/.test(t)) return '10_8';
        if (/(^|\D)(6|4)($|\D)/.test(t)) return '6_4';
        // 2XL o más: 2xl, xxl, 3xl, xxxl
        if (/(^|\W)(2xl|xxl|3xl|xxxl)($|\W)/.test(t)) return '2xl_plus';
        // Letras estándar
        if (/(^|\W)(xs|s|m|l|xl)($|\W)/.test(t)) return 'letras';
        // Desconocido -> letras por defecto
        return 'letras';
    }

    // Precios por grupo removidos; se usa precio por talla (BD) o precio base del producto.

    // Precio unitario efectivo para una fila según la talla elegida.
    // Prioridad: precio específico por talla (BD) -> precio base del producto.
    function precioUnitarioPorTalla(idTalla, nombreTalla) {
        const key = String(idTalla || '');
        if (key && tallaPriceMap.has(key)) {
            const v = Number(tallaPriceMap.get(key));
            if (!isNaN(v) && v > 0) return v;
        }
        return getPrecioUnitarioBase();
    }
    function getTotalCantidad() {
        const inputs = document.querySelectorAll('#tbodyItems .inp-cantidad');
        let total = 0;
        inputs.forEach(i => { const v = parseInt(i.value || '0'); if (!isNaN(v)) total += v; });
        return total;
    }
    function getBreakdownPorTalla() {
        const rows = document.querySelectorAll('#tbodyItems tr.item-row');
        const map = {};
        rows.forEach(tr => {
            const sel = tr.querySelector('.sel-talla');
            const cantInp = tr.querySelector('.inp-cantidad');
            if (!sel || !cantInp) return;
            const nombreTalla = (sel.options[sel.selectedIndex]?.text || '').trim();
            if (!nombreTalla) return;
            const cant = parseInt(cantInp.value || '0');
            if (isNaN(cant) || cant <= 0) return;
            map[nombreTalla] = (map[nombreTalla] || 0) + cant;
        });
        return map;
    }

    // Construye detalle por talla con cantidad, precio unitario y subtotal a partir de filas.
    function getDetallePorTalla() {
        const rows = document.querySelectorAll('#tbodyItems tr.item-row');
        const map = new Map(); // key: nombreTalla, val: { nombre, cant, unit }
        rows.forEach(tr => {
            const sel = tr.querySelector('.sel-talla');
            const cantInp = tr.querySelector('.inp-cantidad');
            if (!sel || !cantInp) return;
            const optSel = sel.options[sel.selectedIndex];
            const nombreTalla = (optSel?.text || '').trim();
            const idTalla = optSel?.value || '';
            if (!nombreTalla || !idTalla) return;
            const cant = parseInt(cantInp.value || '0');
            if (isNaN(cant) || cant <= 0) return;
            const unit = precioUnitarioPorTalla(idTalla, nombreTalla);
            if (!map.has(nombreTalla)) {
                map.set(nombreTalla, { nombre: nombreTalla, cant: 0, unit });
            }
            const agg = map.get(nombreTalla);
            agg.cant += cant;
            // En caso de múltiples filas de la misma talla con distinto unit (no debería), usar el último calculado
            agg.unit = unit;
        });
        // Orden por talla (letras más comunes primero, luego alfabético)
        const pref = ['XXXL','XXL','XL','L','M','S','XS'];
        const arr = Array.from(map.values());
        arr.sort((a,b)=>{
            const ia = pref.indexOf(a.nombre.toUpperCase());
            const ib = pref.indexOf(b.nombre.toUpperCase());
            if (ia !== -1 || ib !== -1) return (ia === -1 ? 999 : ia) - (ib === -1 ? 999 : ib);
            return a.nombre.localeCompare(b.nombre);
        });
        return arr.map(it => ({ ...it, subtotal: it.unit * it.cant }));
    }
    function renderBreakdown(map) {
        const cont = document.getElementById('uiBreakdownTallas');
        if (!cont) return;
        const nombres = Object.keys(map);
        if (nombres.length === 0) { cont.innerHTML = '<span class="text-muted">—</span>'; return; }
        // Orden opcional: XS,S,M,L,XL,XXL,XXXL al inicio si existen, luego el resto alfabético
        const pref = ['XXXL','XXL','XL','L','M','S','XS'];
        nombres.sort((a,b)=>{
            const ia = pref.indexOf(a.toUpperCase());
            const ib = pref.indexOf(b.toUpperCase());
            if (ia !== -1 || ib !== -1) {
                return (ia === -1 ? 999 : ia) - (ib === -1 ? 999 : ib);
            }
            return a.localeCompare(b);
        });
        cont.innerHTML = nombres.map(n => `
            <div class="d-flex justify-content-between">
                <span>${n}</span><span>${map[n]}</span>
            </div>
        `).join('');
    }
    function formatear(n) { return 'Bs ' + (Number(n).toFixed(2)); }
    function recalcTotales() {
        // Recorrer filas para calcular por grupo
        const rows = document.querySelectorAll('#tbodyItems tr.item-row');
        const grupos = { letras: {cant:0, unit:0, total:0}, '2xl_plus': {cant:0, unit:0, total:0}, '14_12': {cant:0, unit:0, total:0}, '10_8': {cant:0, unit:0, total:0}, '6_4': {cant:0, unit:0, total:0} };
        let cantTotal = 0;
        rows.forEach(tr => {
            const sel = tr.querySelector('.sel-talla');
            const cantInp = tr.querySelector('.inp-cantidad');
            if (!sel || !cantInp) return;
            const optSel = sel.options[sel.selectedIndex];
            const nombreTalla = (optSel?.text || '').trim();
            const idTalla = optSel?.value || '';
            let c = parseInt(cantInp.value || '0');
            if (isNaN(c) || c <= 0) return;
            const g = grupoDeTalla(nombreTalla);
            const unit = precioUnitarioPorTalla(idTalla, nombreTalla);
            grupos[g].cant += c;
            grupos[g].unit = unit; // mostrar en desglose
            grupos[g].total += unit * c;
            cantTotal += c;
        });

        const subtotal = Object.values(grupos).reduce((acc, g) => acc + g.total, 0);
        const igv = 0; // Mantener 0 (frontend)
        const total = subtotal + igv;

        // UI: cantidad total y desglose por talla específica
        const uiCant = document.getElementById('uiCantTotal');
        if (uiCant) uiCant.textContent = String(cantTotal);
        renderBreakdown(getBreakdownPorTalla());

        // Precio unitario: si hay un solo grupo con cantidad, mostrar su unit. Si hay varios, "Mixto".
        const gruposConCant = Object.values(grupos).filter(g => g.cant > 0);
        const uiPrecio = document.getElementById('uiPrecioUnit');
        if (gruposConCant.length === 1) {
            if (uiPrecio) uiPrecio.textContent = formatear(gruposConCant[0].unit);
        } else {
            if (uiPrecio) uiPrecio.textContent = 'Mixto';
        }

        // Tabla por talla (Talla, Cantidad, P. Unit, Subtotal) y total general
        const uiTabla = document.getElementById('uiTablaTallas');
        if (uiTabla) {
            const detalle = getDetallePorTalla();
            const totalTabla = detalle.reduce((acc, d) => acc + d.subtotal, 0);
            if (!detalle.length) {
                uiTabla.innerHTML = '<span class="text-muted">—</span>';
            } else {
                const rowsHtml = detalle.map(d => `
                    <tr>
                        <td>${d.nombre}</td>
                        <td class="text-end">${d.cant}</td>
                        <td class="text-end">${formatear(d.unit)}</td>
                        <td class="text-end">${formatear(d.subtotal)}</td>
                    </tr>
                `).join('');
                uiTabla.innerHTML = `
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Talla</th>
                                    <th class="text-end">Cantidad</th>
                                    <th class="text-end">P. Unit</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${rowsHtml}
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="3" class="text-end">Total de todo lo pedido</th>
                                    <th class="text-end">${formatear(totalTabla)}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                `;
            }
        }

        // Subtotales y totales
        document.getElementById('uiSubtotal').textContent = formatear(subtotal);
        document.getElementById('uiIgv').textContent = formatear(igv);
        document.getElementById('uiTotal').textContent = formatear(total);

        // Pago inicial y saldo
        const inpAd = document.getElementById('pagoInicial');
        let adelanto = parseFloat(inpAd?.value || '0');
        if (isNaN(adelanto) || adelanto < 0) adelanto = 0;
        document.getElementById('uiAdelanto').textContent = formatear(adelanto);
        const saldo = Math.max(total - adelanto, 0);
        document.getElementById('uiSaldo').textContent = formatear(saldo);

        // Efectivo y vuelto (Opción B): contra pago inicial
        const inpEfec = document.getElementById('montoEntregado');
        const tipoPago = (document.getElementById('tipoPago')?.value || '').toLowerCase();
        let efectivo = parseFloat(inpEfec?.value || '0');
        if (isNaN(efectivo) || tipoPago !== 'efectivo') efectivo = 0;
        document.getElementById('uiEfectivo').textContent = formatear(efectivo);
        const vuelto = Math.max(efectivo - adelanto, 0);
        document.getElementById('uiVuelto').textContent = formatear(vuelto);
    }
    // Delegar cambios en cantidades
    document.getElementById('tbodyItems').addEventListener('input', (e) => {
        if (e.target.classList.contains('inp-cantidad')) { recalcTotales(); }
    });
    const inpMontoEntregado = document.getElementById('montoEntregado');
    if (inpMontoEntregado) inpMontoEntregado.addEventListener('input', recalcTotales);
    document.getElementById('tipoPago').addEventListener('change', () => {
        updatePagoUI();
        recalcTotales();
    });
    // Cambios de adelanto
    ['pagoInicial']
        .forEach(id => { const el = document.getElementById(id); if (el) el.addEventListener('input', recalcTotales); });

    function updatePagoUI() {
        const tipo = (document.getElementById('tipoPago')?.value || '').toLowerCase();
        const grpEfec = document.getElementById('efectivoGroup');
        const inpEfec = document.getElementById('montoEntregado');
        const isEfec = tipo === 'efectivo';
        if (grpEfec) grpEfec.style.display = isEfec ? '' : 'none';
        if (!isEfec && inpEfec) {
            inpEfec.value = '';
        }
    }

    // Eliminado: dropdown personalizado de clientes (ahora se usa <select>)
</script>
@endpush
