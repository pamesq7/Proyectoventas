@extends('layouts.app')

@section('title', 'Nuevo Pedido - ' . $producto->nombre)

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Nuevo Pedido</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('pedidos.catalogo') }}">Catálogo</a></li>
        <li class="breadcrumb-item active">Nuevo Pedido</li>
    </ol>

    {{-- Alertas --}}
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
    @if(session('info'))
    <div class="alert alert-info alert-dismissible fade show" role="alert">
        <i class="fas fa-info-circle me-2"></i>
        {{ session('info') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <i class="fas fa-plus me-1"></i>
                Crear Nuevo Pedido
            </div>
            <a href="{{ route('pedidos.catalogo') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Volver al Catálogo
            </a>
        </div>
        <div class="card-body">
            <form id="formNuevoPedido" action="{{ route('pedidos.guardar-desde-catalogo') }}" method="POST">
                @csrf
                <input type="hidden" name="tipoCliente" id="tipoCliente" value="">
                <input type="hidden" name="idCliente" id="idCliente" value="">
                <input type="hidden" name="idEstablecimiento" id="idEstablecimiento" value="">
                <input type="hidden" name="idProducto" id="idProducto" value="{{ $producto->idProducto }}" data-nombre="{{ strtolower($producto->nombre) }}" data-precio="{{ $producto->precioVenta ?? 0 }}">

                <div class="row">
                    <!-- Columna izquierda - Información del pedido -->
                    <div class="col-lg-8">
                        <!-- Información básica del pedido -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-info-circle me-2"></i>Información del Pedido
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Fecha de Entrega *</label>
                                        <input type="date" name="fechaEntrega" class="form-control"
                                            value="{{ old('fechaEntrega') }}"
                                            min="{{ date('Y-m-d', strtotime('+1 day')) }}" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Lugar de Entrega *</label>
                                        <input type="text" name="lugarEntrega" class="form-control"
                                            value="{{ old('lugarEntrega', 'Recojo en tienda') }}" required>
                                    </div>

                                </div>
                            </div>
                        </div>

                        <!-- Información del producto -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-cube me-2"></i>Producto Seleccionado
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4 text-center">
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
                                            class="img-fluid rounded shadow"
                                            style="max-height: 200px;"
                                            alt="{{ $producto->nombre }}">
                                        @else
                                        <div class="bg-light d-flex align-items-center justify-content-center rounded"
                                            style="height: 150px;">
                                            <i class="fas fa-image fa-3x text-muted"></i>
                                        </div>
                                        @endif
                                    </div>
                                    <div class="col-md-8">
                                        <h5>{{ $producto->nombre }}</h5>
                                        <p class="text-muted">{{ $producto->descripcion }}</p>
                                        <div class="row">
                                            <div class="col-6">
                                                <strong>Precio Base:</strong><br>
                                                <span class="h5 text-success">Bs {{ number_format($producto->precioVenta, 2) }}</span>
                                            </div>
                                            <div class="col-6">
                                                <strong>Estado:</strong><br>
                                                <span class="badge bg-success">Disponible</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <!-- Configuración de prendas -->
                        <div class="card mb-4">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">
                                    <i class="fas fa-tshirt me-2"></i>Configuración de Prendas por talla
                                </h5>
                                <button type="button" id="btnAddRow" class="btn btn-primary btn-sm">
                                    <i class="fas fa-plus me-1"></i> Agregar Fila(s)
                                </button>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped" id="tablaPrendas">
                                        <thead class="table-dark">
                                            <tr>
                                                <th width="20%">Talla *</th>
                                                <th width="15%">Cantidad *</th>
                                                <th width="25%">Nombre Personalizado</th>
                                                <th width="20%">Número</th>
                                                <th width="30%">Observaciones</th>
                                                <th width="5%">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tbodyItems">
                                            <tr class="item-row">
                                                <td>
                                                    <select name="items[1][idTalla]" class="form-select form-select-sm sel-talla" required>
                                                        <option value="">Seleccionar talla</option>
                                                        @foreach($tallas as $talla)
                                                        <option value="{{ $talla->idTalla }}">{{ $talla->nombre }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="number" name="items[1][cantidad]"
                                                        class="form-control form-control-sm inp-cantidad"
                                                        min="1" value="1" required>
                                                </td>
                                                <td>
                                                    <input type="text" name="items[1][nombre]"
                                                        class="form-control form-control-sm inp-nombre"
                                                        placeholder="Nombre para la prenda">
                                                </td>
                                                <td>
                                                    <input type="number" name="items[1][numero]"
                                                        class="form-control form-control-sm inp-numero"
                                                        placeholder="Número" min="0" max="999">
                                                </td>
                                                <td>
                                                    <textarea name="items[1][observaciones]"
                                                        class="form-control form-control-sm inp-obs"
                                                        placeholder="Observaciones opcionales"
                                                        rows="1"></textarea>
                                                </td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-sm btn-outline-danger btnRemoveRow"
                                                        title="Quitar fila" disabled>
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Columna derecha - Cliente y pago -->
                    <div class="col-lg-4">
                        <!-- Selección de cliente -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-user me-2"></i>Información del Cliente
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label">Cliente *</label>
                                    <div class="input-group mb-2">
                                        <a href="{{ url('users/create') }}" class="btn btn-success"
                                            title="Agregar nuevo usuario" target="_blank" rel="noopener">
                                            <i class="fas fa-plus"></i>
                                        </a>
                                        <input type="text" id="clienteFilter" class="form-control"
                                            placeholder="Buscar por CI, nombre o teléfono...">
                                    </div>
                                    <select class="form-select" name="clienteSeleccionado" id="clienteSelect" required>
                                        <option value="">Seleccione un cliente</option>
                                        <optgroup label="Clientes naturales">
                                            @foreach($clientesNaturales as $cliente)
                                            @php($valor = 'natural:' . $cliente->idCliente)
                                            @php($documento = $cliente->user->ci ?? ($cliente->nit ?? ''))
                                            @php($telefono = $cliente->user->telefono ?? '')
                                            @php($nombre = $cliente->user->name ?? 'Cliente')
                                            @php($etiqueta = trim(($documento ? 'CI: '.$documento.' - ' : '') . $nombre . ($telefono ? ' - Tel: '.$telefono : '')))
                                            <option value="{{ $valor }}"
                                                data-ci="{{ $documento }}"
                                                data-telefono="{{ $telefono }}"
                                                {{ old('clienteSeleccionado') === $valor ? 'selected' : '' }}>
                                                {{ $etiqueta }}
                                            </option>
                                            @endforeach
                                        </optgroup>
                                        <optgroup label="Establecimientos">
                                            @foreach($clientesEstablecimientos as $establecimiento)
                                            @php($valor = 'establecimiento:' . $establecimiento->idEstablecimiento)
                                            @php($documento = $establecimiento->nit ?? '')
                                            @php($telefono = $establecimiento->representante->telefono ?? '')
                                            @php($nombre = $establecimiento->razonSocial ?? 'Establecimiento')
                                            @php($etiqueta = trim(($documento ? 'NIT: '.$documento.' - ' : '') . $nombre . ($telefono ? ' - Tel: '.$telefono : '')))
                                            <option value="{{ $valor }}"
                                                data-ci="{{ $documento }}"
                                                data-telefono="{{ $telefono }}"
                                                {{ old('clienteSeleccionado') === $valor ? 'selected' : '' }}>
                                                {{ $etiqueta }}
                                            </option>
                                            @endforeach
                                        </optgroup>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Información de pago -->
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-credit-card me-2"></i>Información de Pago
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label">Tipo de Pago *</label>
                                    <select class="form-select" id="tipoPago" name="tipoTransaccion" required>
                                        <option value="efectivo" selected>Efectivo</option>
                                        <option value="qr">QR</option>
                                        <option value="cheque">Cheque</option>
                                        <option value="transferencia">Transferencia bancaria</option>
                                    </select>
                                </div>

                                <div class="mb-3" id="efectivoGroup">
                                    <label class="form-label">Efectivo Recibido</label>
                                    <input type="number" class="form-control" id="efectivoRecibido"
                                        placeholder="Cantidad recibida" step="0.01" min="0">
                                </div>

                                <div class="form-check mb-3" id="efectivoExactoGroup">
                                    <input class="form-check-input" type="checkbox" id="efectivoExacto">
                                    <label class="form-check-label" for="efectivoExacto">Efectivo Exacto</label>
                                </div>

                                <hr>

                                <!-- Resumen de compra actualizado -->
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <div class="d-flex justify-content-between">
                                                    <small>Cantidad total</small>
                                                    <small id="uiCantTotal" class="ms-3">0</small>
                                                </div>
                                                <div id="uiBreakdownTallas" class="small text-muted">Ninguna talla seleccionada</div>
                                                <div class="d-flex justify-content-between">
                                                    <small>Precio unitario</small>
                                                    <small id="uiPrecioUnit">Bs 0.00</small>
                                                </div>
                                            </div>
                                            <div class="text-end">
                                                <h5 class="mb-0">TOTAL</h5>
                                                <h3 id="uiTotal" class="mb-0">Bs 0.00</h3>
                                            </div>
                                        </div>
                                        
                                        <div class="mt-3" id="uiTablaTallas">
                                            <!-- Aquí se mostrará la tabla de tallas -->
                                        </div>
                                        
                                        <div class="mt-3">
                                            <div class="d-flex justify-content-between">
                                                <small>Adelanto</small>
                                                <small id="uiAdelanto">Bs 0.00</small>
                                            </div>
                                            <div class="d-flex justify-content-between">
                                                <small><strong>Saldo</strong></small>
                                                <small id="uiSaldo"><strong>Bs 0.00</strong></small>
                                            </div>
                                            <hr>
                                            <div class="d-flex justify-content-between text-success">
                                                <small>Monto Efectivo</small>
                                                <small id="uiEfectivo">Bs 0.00</small>
                                            </div>
                                            <div class="d-flex justify-content-between text-danger">
                                                <small>Vuelto</small>
                                                <small id="uiVuelto">Bs 0.00</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Adelanto</label>
                                    <input type="number" class="form-control" id="montoAdelanto"
                                        name="montoAdelanto" placeholder="Monto de adelanto"
                                        step="0.01" min="0">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Botones de acción -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('pedidos.catalogo') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i> Volver al Catálogo
                            </a>
                            <div>
                                <button type="submit" class="btn btn-success btn-lg">
                                    <i class="fas fa-save me-2"></i> Guardar Pedido
                                </button>
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
    // ========================================
    // VARIABLES GLOBALES Y CONFIGURACIÓN
    // ========================================
    let contadorFilas = 1;
    let tallaPriceMap = new Map();
    const precioBase = parseFloat(document.getElementById('idProducto').dataset.precio) || 0;

    // ========================================
    // FUNCIONES PARA CARACTERÍSTICAS
    // ========================================

    // Función de prueba para depurar
    function testCargarCaracteristicas() {
        const productoId = document.getElementById('idProducto')?.value;
        const debugDiv = document.getElementById('debugInfo');

        if (debugDiv) {
            debugDiv.innerHTML = `
            <strong>Depuración:</strong><br>
            - Producto ID: ${productoId || 'NO ENCONTRADO'}<br>
            - Recargando características...
        `;
        }

        if (productoId) {
            onProductoChange(productoId);
        } else {
            alert('No se encontró el ID del producto');
        }
    }

    // Test directo de la API
    async function testAPIDirecta() {
        const productoId = document.getElementById('idProducto')?.value;
        const debugDiv = document.getElementById('debugInfo');

        if (!productoId) {
            alert('No hay producto ID');
            return;
        }

        const url = `{{ url('api/producto') }}/${productoId}/opciones`;

        if (debugDiv) {
            debugDiv.innerHTML = `<strong>🔍 Probando API...</strong><br>URL: ${url}`;
            debugDiv.className = 'alert alert-info mb-3';
        }

        try {
            const response = await fetch(url, {
                headers: {
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();

            if (debugDiv) {
                debugDiv.innerHTML = `
                <strong>✅ Respuesta de la API:</strong><br>
                <pre style="max-height: 300px; overflow: auto;">${JSON.stringify(data, null, 2)}</pre>
            `;
                debugDiv.className = 'alert alert-success mb-3';
            }

            console.log('Respuesta completa de la API:', data);
        } catch (error) {
            if (debugDiv) {
                debugDiv.innerHTML = `
                <strong>❌ Error al llamar API:</strong><br>
                ${error.message}
            `;
                debugDiv.className = 'alert alert-danger mb-3';
            }
            console.error('Error:', error);
        }
    }

    // Cargar características del producto desde la API
    async function onProductoChange(idProducto) {
        console.log('🔍 onProductoChange llamado con idProducto:', idProducto);

        const cont = document.getElementById('caracteristicasContainer');
        const wrap = document.getElementById('opcionesContainer');

        console.log('📦 Contenedores encontrados:', {
            caracteristicasContainer: cont ? 'SÍ' : 'NO',
            opcionesContainer: wrap ? 'SÍ' : 'NO'
        });

        if (cont) cont.innerHTML = '';
        if (wrap) wrap.style.display = 'none';

        if (!idProducto) {
            console.warn('⚠️ No hay idProducto');
            return;
        }

        try {
            const urlOpts = `{{ url('api/producto') }}/${idProducto}/opciones`;
            console.log('🌐 Llamando a API:', urlOpts);

            const res = await fetch(urlOpts, {
                headers: {
                    'Accept': 'application/json'
                }
            });

            console.log('📡 Respuesta API status:', res.status);

            if (!res.ok) {
                const errorText = await res.text();
                console.error('❌ Error opciones por producto:', errorText);
                return;
            }

            const data = await res.json();
            console.log('📊 Datos recibidos:', data);

            const opciones = data.opciones || [];
            console.log('✅ Total de opciones:', opciones.length);

            if (cont) {
                opciones.forEach((op, index) => {
                    console.log(`🎨 Procesando opción ${index + 1}:`, op.nombreOpcion, '- Características:', op.caracteristicas?.length || 0);

                    const col = document.createElement('div');
                    col.className = 'col-md-3';

                    const label = document.createElement('label');
                    label.className = 'form-label';
                    label.innerHTML = `<strong>${op.nombreOpcion} *</strong>`;

                    const select = document.createElement('select');
                    select.className = 'form-select';
                    select.name = `caracteristicas[${op.idOpcion || 'otros'}]`;
                    select.required = true;

                    const placeholder = document.createElement('option');
                    placeholder.value = '';
                    placeholder.textContent = 'Seleccionar';
                    select.appendChild(placeholder);

                    (op.caracteristicas || []).forEach(c => {
                        const o = document.createElement('option');
                        o.value = c.idCaracteristica;
                        o.textContent = c.nombre;
                        select.appendChild(o);
                    });

                    col.appendChild(label);
                    col.appendChild(select);
                    cont.appendChild(col);
                });
            }

            // Actualizar info de depuración
            const debugDiv = document.getElementById('debugInfo');
            if (debugDiv) {
                debugDiv.innerHTML = `
                <strong>✅ Carga exitosa:</strong><br>
                - Total de opciones: ${opciones.length}<br>
                - Características renderizadas: ${opciones.reduce((sum, op) => sum + (op.caracteristicas?.length || 0), 0)}
            `;
                debugDiv.className = 'alert alert-success mb-3';
            }

            if (wrap && opciones.length) {
                console.log('✅ Mostrando contenedor de opciones');
                wrap.style.display = '';
            } else {
                console.warn('⚠️ No hay opciones para mostrar');
                if (debugDiv) {
                    debugDiv.innerHTML = '<strong>⚠️ No hay opciones configuradas para este producto</strong>';
                    debugDiv.className = 'alert alert-warning mb-3';
                }
            }
        } catch (e) {
            console.error('❌ Error al cargar características:', e);
            const debugDiv = document.getElementById('debugInfo');
            if (debugDiv) {
                debugDiv.innerHTML = `
                <strong>❌ Error:</strong><br>
                ${e.message}
            `;
                debugDiv.className = 'alert alert-danger mb-3';
            }
        }
    }

    // Cargar precios por talla
    async function loadTallaPrecios(idProducto) {
        tallaPriceMap = new Map();
        if (!idProducto) return;

        try {
            const url = `{{ url('api/producto') }}/${idProducto}/tallas-precios`;
            const res = await fetch(url, {
                headers: {
                    'Accept': 'application/json'
                }
            });

            if (!res.ok) {
                console.error('Error tallas-precios', await res.text());
                return;
            }

            const data = await res.json();
            (data.precios || []).forEach(p => {
                tallaPriceMap.set(String(p.idTalla), Number(p.precioUnitario || 0));
            });
        } catch (e) {
            console.error('Error al cargar precios por talla:', e);
        }
    }

    // Mostrar/ocultar campo Número según producto
    function toggleNumeroByProducto() {
        const sel = document.getElementById('idProducto');
        const opt = sel ? sel.options[sel.selectedIndex] : null;
        const nombre = (opt && opt.dataset && opt.dataset.nombre) ? opt.dataset.nombre : '';

        const esChamarra = nombre.includes('chamarra');
        const esPolera = nombre.includes('polera');
        const mostrarNumero = esPolera && !esChamarra;

        // Ocultar/mostrar columna de número en todas las filas
        const filas = document.querySelectorAll('#tbodyItems tr.item-row');
        filas.forEach(tr => {
            const inputNumero = tr.querySelector('.inp-numero');
            if (inputNumero) {
                inputNumero.closest('td').style.display = mostrarNumero ? '' : 'none';
                if (!mostrarNumero) inputNumero.value = '';
            }
        });

        // Ocultar/mostrar header de número
        const headers = document.querySelectorAll('#tablaPrendas thead th');
        if (headers.length >= 4) {
            headers[3].style.display = mostrarNumero ? '' : 'none';
        }
    }

    // ========================================
    // FUNCIONES PARA GESTIÓN DE FILAS
    // ========================================

    // Función para formatear números con separadores de miles
    function formatear(numero) {
        return 'Bs ' + parseFloat(numero).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
    }

    // Agregar nueva fila
    function agregarFila() {
        contadorFilas++;
        const tbody = document.getElementById('tbodyItems');
        const nuevaFila = document.createElement('tr');
        nuevaFila.className = 'item-row';
        
        nuevaFila.innerHTML = `
            <td>
                <select name="items[${contadorFilas}][idTalla]" class="form-select form-select-sm sel-talla" required>
                    <option value="">Seleccionar talla</option>
                    @foreach($tallas as $talla)
                    <option value="{{ $talla->idTalla }}">{{ $talla->nombre }}</option>
                    @endforeach
                </select>
            </td>
            <td>
                <input type="number" name="items[${contadorFilas}][cantidad]" 
                    class="form-control form-control-sm inp-cantidad" 
                    min="1" value="1" required>
            </td>
            <td>
                <input type="text" name="items[${contadorFilas}][nombre]" 
                    class="form-control form-control-sm inp-nombre" 
                    placeholder="Nombre para la prenda">
            </td>
            <td>
                <input type="number" name="items[${contadorFilas}][numero]" 
                    class="form-control form-control-sm inp-numero" 
                    placeholder="Número" min="0" max="999">
            </td>
            <td>
                <textarea name="items[${contadorFilas}][observaciones]" 
                    class="form-control form-control-sm inp-obs" 
                    placeholder="Observaciones opcionales"
                    rows="1"></textarea>
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-outline-danger btnRemoveRow" 
                    title="Quitar fila">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;
        
        tbody.appendChild(nuevaFila);

        // Actualizar visibilidad del campo número
        toggleNumeroByProducto();
        
        // Habilitar botones de eliminar si hay más de una fila
        actualizarBotonesEliminar();
        recalcTotales();
        
        return false;
    }

    // Eliminar fila
    function eliminarFila(fila) {
        const filas = document.querySelectorAll('.item-row');
        if (filas.length > 1) {
            fila.remove();
            actualizarBotonesEliminar();
            recalcTotales();
        }
    }

    // Actualizar botones de eliminar
    function actualizarBotonesEliminar() {
        const filas = document.querySelectorAll('.item-row');
        const botones = document.querySelectorAll('.btnRemoveRow');

        if (filas.length === 1) {
            botones.forEach(btn => btn.disabled = true);
        } else {
            botones.forEach(btn => btn.disabled = false);
        }
    }

    // ========================================
    // FUNCIONES DE CÁLCULO
    // ========================================

    function recalcTotales() {
        const filas = document.querySelectorAll('.item-row');
        let totalPrendas = 0;
        let totalPrecio = 0;
        const desgloseTallas = {};

        filas.forEach(fila => {
            const cantidad = parseInt(fila.querySelector('.inp-cantidad')?.value) || 0;
            const tallaSelect = fila.querySelector('.sel-talla');
            const tallaTexto = tallaSelect?.options[tallaSelect.selectedIndex]?.text || 'Sin talla';
            const precioUnitario = tallaSelect?.value ? (tallaPriceMap.get(tallaSelect.value) || 0) : 0;
            const subtotal = cantidad * precioUnitario;

            totalPrendas += cantidad;
            totalPrecio += subtotal;

            // Actualizar desglose de tallas
            if (tallaTexto) {
                if (!desgloseTallas[tallaTexto]) {
                    desgloseTallas[tallaTexto] = {
                        cantidad: 0,
                        subtotal: 0
                    };
                }
                desgloseTallas[tallaTexto].cantidad += cantidad;
                desgloseTallas[tallaTexto].subtotal += subtotal;
            }
        });

        // Actualizar UI
        document.getElementById('uiCantTotal').textContent = totalPrendas;
        
        // Mostrar desglose de tallas
        let desgloseHTML = '';
        for (const [talla, datos] of Object.entries(desgloseTallas)) {
            desgloseHTML += `${talla}: ${datos.cantidad} (${formatear(datos.subtotal)})<br>`;
        }
        document.getElementById('uiBreakdownTallas').innerHTML = desgloseHTML || 'Ninguna talla seleccionada';
        document.getElementById('uiPrecioUnit').textContent = formatear(precioBase);
        document.getElementById('uiTotal').textContent = formatear(totalPrecio);

        // Actualizar tabla de tallas
        let tablaHTML = `
            <table class="table table-sm small">
                <thead>
                    <tr>
                        <th>Talla</th>
                        <th class="text-end">Cantidad</th>
                        <th class="text-end">P. Unit</th>
                        <th class="text-end">Subtotal</th>
                    </tr>
                </thead>
                <tbody>`;

        for (const [talla, datos] of Object.entries(desgloseTallas)) {
            const precioUnit = datos.cantidad > 0 ? (datos.subtotal / datos.cantidad) : 0;
            tablaHTML += `
                <tr>
                    <td>${talla}</td>
                    <td class="text-end">${datos.cantidad}</td>
                    <td class="text-end">${formatear(precioUnit)}</td>
                    <td class="text-end">${formatear(datos.subtotal)}</td>
                </tr>`;
        }

        tablaHTML += `
                <tr class="table-active">
                    <th colspan="3" class="text-end">Total:</th>
                    <th class="text-end">${formatear(totalPrecio)}</th>
                </tr>
            </tbody></table>`;

        document.getElementById('uiTablaTallas').innerHTML = tablaHTML;

        // Calcular adelanto y saldo
        const adelanto = parseFloat(document.getElementById('montoAdelanto')?.value || 0);
        const saldo = Math.max(totalPrecio - adelanto, 0);
        document.getElementById('uiAdelanto').textContent = formatear(adelanto);
        document.getElementById('uiSaldo').innerHTML = `<strong>${formatear(saldo)}</strong>`;

        // Calcular efectivo y vuelto
        const efectivoInput = document.getElementById('efectivoRecibido');
        const efectivoExacto = document.getElementById('efectivoExacto').checked;
        let efectivo = 0;

        if (efectivoExacto) {
            efectivo = totalPrecio;
            if (efectivoInput) {
                efectivoInput.value = totalPrecio.toFixed(2);
                efectivoInput.readOnly = true;
            }
        } else {
            if (efectivoInput) {
                efectivoInput.readOnly = false;
                efectivo = parseFloat(efectivoInput.value) || 0;
            }
        }

        document.getElementById('uiEfectivo').textContent = formatear(efectivo);
        const vuelto = Math.max(efectivo - totalPrecio, 0);
        document.getElementById('uiVuelto').textContent = formatear(vuelto);
    }

    // ========================================
    // INICIALIZACIÓN
    // ========================================

    document.addEventListener('DOMContentLoaded', function() {
        // TEST INMEDIATO - Verificar elementos
        console.log('\n========== TEST DE INICIALIZACIÓN ==========');
        const elementos = {
            idProducto: document.getElementById('idProducto'),
            opcionesContainer: document.getElementById('opcionesContainer'),
            caracteristicasContainer: document.getElementById('caracteristicasContainer'),
            debugInfo: document.getElementById('debugInfo'),
            btnAddRow: document.getElementById('btnAddRow'),
            tbodyItems: document.getElementById('tbodyItems')
        };

        console.log('Elementos encontrados:', {
            idProducto: elementos.idProducto ? `SÍ (valor: ${elementos.idProducto.value})` : 'NO',
            opcionesContainer: elementos.opcionesContainer ? 'SÍ' : 'NO',
            caracteristicasContainer: elementos.caracteristicasContainer ? 'SÍ' : 'NO',
            debugInfo: elementos.debugInfo ? 'SÍ' : 'NO',
            btnAddRow: elementos.btnAddRow ? 'SÍ' : 'NO',
            tbodyItems: elementos.tbodyItems ? 'SÍ' : 'NO'
        });

        // Actualizar debugInfo inmediatamente
        if (elementos.debugInfo) {
            elementos.debugInfo.innerHTML = `
            <strong>🔍 Verificando elementos...</strong><br>
            - idProducto: ${elementos.idProducto ? '✅ Encontrado' : '❌ No encontrado'}<br>
            - Valor: ${elementos.idProducto?.value || 'N/A'}<br>
            - Contenedores: ${elementos.opcionesContainer && elementos.caracteristicasContainer ? '✅ OK' : '❌ Falta alguno'}
        `;
        }

        console.log('===========================================\n');

        // Inicializar producto preseleccionado
        const productoId = elementos.idProducto?.value;
        console.log('🚀 Inicializando con producto ID:', productoId);

        if (productoId) {
            console.log('▶️ Cargando características, precios y configuración...');

            // Forzar carga con un pequeño delay para asegurar que el DOM esté listo
            setTimeout(() => {
                console.log('🔄 Ejecutando carga de características...');
                onProductoChange(productoId);
                loadTallaPrecios(productoId);
                toggleNumeroByProducto();
            }, 100);
        } else {
            console.error('❌ No se encontró el ID del producto');
            if (elementos.debugInfo) {
                elementos.debugInfo.innerHTML = '<strong>❌ ERROR: No se encontró el ID del producto</strong>';
                elementos.debugInfo.className = 'alert alert-danger mb-3';
            }
        }

        // Event Listeners para gestión de filas
        if (elementos.btnAddRow) {
            elementos.btnAddRow.addEventListener('click', agregarFila);
        }

        if (elementos.tbodyItems) {
            elementos.tbodyItems.addEventListener('click', function(e) {
                if (e.target.closest('.btnRemoveRow')) {
                    eliminarFila(e.target.closest('.item-row'));
                }
            });
        }

        // Event listeners para actualizar totales
        document.getElementById('montoAdelanto')?.addEventListener('input', recalcTotales);
        document.getElementById('efectivoRecibido')?.addEventListener('input', recalcTotales);
        document.getElementById('efectivoExacto')?.addEventListener('change', recalcTotales);

        // Para actualizar cuando cambie la talla o cantidad (delegación de eventos)
        document.addEventListener('change', function(e) {
            if (e.target.classList.contains('sel-talla') || 
                e.target.classList.contains('inp-cantidad')) {
                recalcTotales();
            }
        });

        // Manejar tipo de pago
        document.getElementById('tipoPago')?.addEventListener('change', function() {
            const tipo = this.value;
            const efectivoGroup = document.getElementById('efectivoGroup');
            const efectivoExactoGroup = document.getElementById('efectivoExactoGroup');

            if (tipo === 'efectivo') {
                efectivoGroup.style.display = 'block';
                efectivoExactoGroup.style.display = 'block';
            } else {
                efectivoGroup.style.display = 'none';
                efectivoExactoGroup.style.display = 'none';
                document.getElementById('efectivoExacto').checked = false;
                document.getElementById('efectivoRecibido').value = '';
            }
            recalcTotales();
        });

        // Manejar selección de cliente
        document.getElementById('clienteSelect')?.addEventListener('change', function() {
            const valor = this.value;
            const tipoCliente = document.getElementById('tipoCliente');
            const idCliente = document.getElementById('idCliente');
            const idEstablecimiento = document.getElementById('idEstablecimiento');

            if (valor.startsWith('natural:')) {
                tipoCliente.value = 'natural';
                idCliente.value = valor.split(':')[1];
                idEstablecimiento.value = '';
            } else if (valor.startsWith('establecimiento:')) {
                tipoCliente.value = 'establecimiento';
                idEstablecimiento.value = valor.split(':')[1];
                idCliente.value = '';
            } else {
                tipoCliente.value = '';
                idCliente.value = '';
                idEstablecimiento.value = '';
            }
        });

        // Búsqueda de clientes
        document.getElementById('clienteFilter')?.addEventListener('input', function() {
            const filtro = this.value.toLowerCase();
            const opciones = document.querySelectorAll('#clienteSelect option');

            opciones.forEach(opcion => {
                const texto = opcion.textContent.toLowerCase();
                if (texto.includes(filtro) || filtro === '') {
                    opcion.style.display = '';
                } else {
                    opcion.style.display = 'none';
                }
            });
        });

        // Validación del formulario
        document.getElementById('formNuevoPedido')?.addEventListener('submit', function(e) {
            const clienteSelect = document.getElementById('clienteSelect');
            const filas = document.querySelectorAll('.item-row');

            if (!clienteSelect?.value) {
                e.preventDefault();
                alert('Por favor selecciona un cliente.');
                clienteSelect?.focus();
                return;
            }

            if (filas.length === 0) {
                e.preventDefault();
                alert('Por favor agrega al menos una prenda al pedido.');
                return;
            }

            // Validar que todas las tallas estén seleccionadas
            let tallasValidas = true;
            filas.forEach(fila => {
                const tallaSelect = fila.querySelector('.sel-talla');
                if (!tallaSelect?.value) {
                    tallasValidas = false;
                }
            });

            if (!tallasValidas) {
                e.preventDefault();
                alert('Por favor selecciona una talla para todas las prendas.');
                return;
            }
        });

        // Inicializar cálculos y botones
        recalcTotales();
        actualizarBotonesEliminar();
    });
</script>
@endpush        
