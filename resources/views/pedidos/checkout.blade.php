@extends('layouts.app')

@section('title', 'Checkout - Finalizar Compra')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">
        <i class="fas fa-shopping-cart me-2"></i>Finalizar Compra
    </h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('pedidos.catalogo') }}">Catálogo</a></li>
        <li class="breadcrumb-item"><a href="{{ route('pedidos.carrito') }}">Carrito</a></li>
        <li class="breadcrumb-item active">Checkout</li>
    </ol>

    {{-- Mensajes --}}
    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form action="{{ route('pedidos.checkout.procesar') }}" method="POST">
        @csrf

        <div class="row">
            {{-- Columna izquierda: resumen --}}
            <div class="col-lg-8">
                {{-- Resumen del pedido --}}
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-shopping-cart me-2"></i>Resumen del Pedido
                        </h5>
                    </div>
                    <div class="card-body">
                        @php
                            // Agrupar productos como en la vista lateral
                            $productosAgrupados = [];
                            foreach($carrito as $item) {
                                $idProducto = $item['idProducto'];
                                if (!isset($productosAgrupados[$idProducto])) {
                                    $productosAgrupados[$idProducto] = [
                                        'idProducto' => $item['idProducto'],
                                        'producto' => $item['producto'],
                                        'imagen' => $item['imagen'] ?? null,
                                        'precioUnitario' => $item['precioUnitario'] ?? 0,
                                        'cantidad_total' => 0,
                                        'subtotal' => 0,
                                        'items' => []
                                    ];
                                }
                                $productosAgrupados[$idProducto]['cantidad_total'] += $item['cantidad'] ?? 0;
                                $productosAgrupados[$idProducto]['subtotal'] += $item['subtotal'] ?? 0;
                                $productosAgrupados[$idProducto]['items'][] = $item;
                            }
                        @endphp

                        @if(empty($productosAgrupados))
                            <div class="p-4 text-center text-muted">
                                <i class="fas fa-shopping-cart fa-2x mb-2"></i>
                                <p>Tu carrito está vacío</p>
                                <a href="{{ route('pedidos.catalogo') }}" class="btn btn-primary mt-2">
                                    <i class="fas fa-store me-1"></i> Ver catálogo
                                </a>
                            </div>
                        @else
                            @foreach($productosAgrupados as $index => $producto)
                            <div class="card mb-3">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        {{-- IMAGEN --}}
                                        <div class="col-md-2">
                                            @php
                                            $imagenSrc = null;
                                            if (!empty($producto['imagen'])) {
                                                $imagenSrc = asset('storage/' . $producto['imagen']);
                                            }
                                            @endphp

                                            @if($imagenSrc)
                                            <img src="{{ $imagenSrc }}"
                                                alt="{{ $producto['producto'] }}"
                                                class="img-fluid rounded"
                                                style="width: 80px; height: 80px; object-fit: cover;">
                                            @else
                                            <div class="bg-light d-flex align-items-center justify-content-center rounded"
                                                style="width: 80px; height: 80px;">
                                                <i class="fas fa-image text-muted fa-2x"></i>
                                            </div>
                                            @endif
                                        </div>

                                        {{-- DETALLES --}}
                                        <div class="col-md-6">
                                            <h5 class="mb-2">{{ $producto['producto'] }}</h5>
                                            <p class="mb-1">
                                                <strong>Cantidad:</strong> {{ $producto['cantidad_total'] }} unidad(es)
                                            </p>
                                            <p class="mb-1">
                                                <strong>Precio unitario:</strong> Bs {{ number_format($producto['precioUnitario'], 2) }}
                                            </p>
                                            @if($producto['cantidad_total'] > 1)
                                                <small class="text-muted">
                                                    @foreach($producto['items'] as $idx => $item)
                                                        @if($idx > 0) - @endif
                                                        Talla {{ $item['talla'] ?? 'N/A' }}: {{ $item['cantidad'] ?? 0 }}
                                                    @endforeach
                                                </small>
                                            @endif
                                        </div>

                                        {{-- SUBTOTAL --}}
                                        <div class="col-md-4 text-end">
                                            <h5 class="text-primary mb-0">
                                                Bs {{ number_format($producto['subtotal'], 2) }}
                                            </h5>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach

                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <h5>TOTAL</h5>
                                        </div>
                                        <div class="col-md-4 text-end">
                                            <h3 class="text-primary mb-0">
                                                Bs {{ number_format($total, 2) }}
                                            </h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Columna derecha: Cliente + Entrega + Pago --}}
            <div class="col-lg-4">
                {{-- Cliente --}}
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-user me-2"></i>Información del Cliente
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Buscar Cliente</label>
                            <div class="position-relative">
                                <input type="text" class="form-control" id="clienteFilter" 
                                       placeholder="Buscar por CI, nombre o teléfono..." autocomplete="off">
                                <button type="button" class="btn btn-outline-secondary position-absolute" 
                                        style="right: 10px; top: 50%; transform: translateY(-50%); z-index: 10;"
                                        id="btnClearSearch">
                                    <i class="fas fa-times"></i>
                                </button>
                                <!-- Dropdown de sugerencias -->
                                <div id="clienteDropdown" class="position-absolute w-100 bg-white border rounded shadow-sm mt-1" 
                                     style="max-height: 300px; overflow-y: auto; z-index: 1000; display: none;">
                                </div>
                            </div>
                            <small class="text-muted" id="searchResultsCount" style="display: none;">
                                <span id="resultsCount">0</span> resultados encontrados
                            </small>
                            
                            <!-- Campo oculto para guardar el cliente seleccionado -->
                            <input type="hidden" name="clienteSeleccionado" id="clienteSeleccionado" value="">
                            
                            <!-- Cliente seleccionado visible -->
                            <div id="clienteSeleccionadoInfo" class="mt-2 p-2 bg-light rounded" style="display: none;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong id="clienteNombre"></strong>
                                        <br>
                                        <small class="text-muted" id="clienteDetalles"></small>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-danger" id="btnDeseleccionar">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Entrega --}}
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-truck me-2"></i>Información de Entrega
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Fecha de Entrega *</label>
                            <input type="date" name="fechaEntrega" class="form-control"
                                value="{{ old('fechaEntrega') }}"
                                min="{{ date('Y-m-d', strtotime('+1 day')) }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Departamento *</label>
                            <select name="idDepartamento" id="departamento" class="form-select" required>
                                <option value="">Seleccione departamento</option>
                                @foreach($departamentos as $depto)
                                <option value="{{ $depto->idDepartamento }}"
                                    {{ old('idDepartamento') == $depto->idDepartamento ? 'selected' : '' }}>
                                    {{ $depto->nombreDepartamento }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Provincia *</label>
                            <select name="idProvincia" id="provincia" class="form-select" required>
                                <option value="">Seleccione provincia</option>
                                @if(!empty($provincias))
                                    @foreach($provincias as $prov)
                                    <option value="{{ $prov->idProvincia }}"
                                        {{ old('idProvincia') == $prov->idProvincia ? 'selected' : '' }}>
                                        {{ $prov->nombreProvincia }}
                                    </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Municipio *</label>
                            <select name="idMunicipio" id="municipio" class="form-select" required>
                                <option value="">Seleccione municipio</option>
                                @if(!empty($municipios))
                                    @foreach($municipios as $munic)
                                    <option value="{{ $munic->idMunicipio }}"
                                        {{ old('idMunicipio') == $munic->idMunicipio ? 'selected' : '' }}>
                                        {{ $munic->nombreMunicipio }}
                                    </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Dirección (calle, nro, referencia) *</label>
                            <input type="text" name="nombreDireccion" class="form-control"
                                value="{{ old('nombreDireccion') }}"
                                placeholder="Ej: Av. Siempre Viva #123, frente al mercado X" required>
                        </div>
                    </div>
                </div>

                {{-- Pago --}}
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

                        <div class="card mb-3">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <div class="d-flex justify-content-between">
                                            <small>Cantidad total</small>
                                            <small id="uiCantTotal" class="ms-3">0</small>
                                        </div>
                                        <div id="uiBreakdownTallas" class="small text-muted">
                                            Ninguna talla seleccionada
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <small>Precio unitario</small>
                                            <small id="uiPrecioUnit">Bs 0.00</small>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <h5 class="mb-0">TOTAL</h5>
                                        <h3 id="uiTotal" class="mb-0">
                                            Bs {{ number_format($total, 2) }}
                                        </h3>
                                    </div>
                                </div>

                                <div class="mt-3" id="uiTablaTallas"></div>

                                <div class="mt-3">
                                    <div class="d-flex justify-content-between">
                                        <small>Adelanto</small>
                                        <small id="uiAdelanto">Bs 0.00</small>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <small><strong>Saldo</strong></small>
                                        <small id="uiSaldo"><strong>Bs {{ number_format($total, 2) }}</strong></small>
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

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success btn-lg" id="btnProcesarPedido">
                                <i class="fas fa-check me-1"></i> Procesar Pedido
                            </button>
                            <a href="{{ route('pedidos.carrito') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Volver al Carrito
                            </a>
                        </div>
                    </div>
                </div>

            </div> {{-- /col-lg-4 --}}
        </div> {{-- /row --}}
    </form>
</div>
@endsection

@push('scripts')
<script>
    // ===== AJAX ubicación =====
    document.addEventListener('DOMContentLoaded', function() {
        const departamento = document.getElementById('departamento');
        const provincia = document.getElementById('provincia');
        const municipio = document.getElementById('municipio');

        // Cargar municipios si ya hay una provincia seleccionada al cargar la página
        if (provincia && provincia.value) {
            const idProv = provincia.value;
            municipio.innerHTML = '<option value="">Seleccione municipio</option>';
            
            fetch("{{ url('ubicacion/municipios') }}/" + idProv)
                .then(r => r.json())
                .then(data => {
                    data.forEach(m => {
                        const opt = document.createElement('option');
                        opt.value = m.idMunicipio;
                        opt.textContent = m.nombreMunicipio;
                        municipio.appendChild(opt);
                    });
                    // Seleccionar el municipio guardado si existe
                    const oldMunicipio = "{{ old('idMunicipio') }}";
                    if (oldMunicipio) {
                        const selectedMunicipio = municipio.querySelector('option[value="' + oldMunicipio + '"]');
                        if (selectedMunicipio) {
                            selectedMunicipio.selected = true;
                        }
                    }
                });
        }

        departamento?.addEventListener('change', function() {
            const idDepto = this.value;
            provincia.innerHTML = '<option value="">Seleccione provincia</option>';
            municipio.innerHTML = '<option value="">Seleccione municipio</option>';

            if (!idDepto) return;

            fetch("{{ url('ubicacion/provincias') }}/" + idDepto)
                .then(r => r.json())
                .then(data => {
                    data.forEach(p => {
                        const opt = document.createElement('option');
                        opt.value = p.idProvincia;
                        opt.textContent = p.nombreProvincia;
                        provincia.appendChild(opt);
                    });
                });
        });

        provincia?.addEventListener('change', function() {
            const idProv = this.value;
            municipio.innerHTML = '<option value="">Seleccione municipio</option>';

            if (!idProv) return;

            fetch("{{ url('ubicacion/municipios') }}/" + idProv)
                .then(r => r.json())
                .then(data => {
                    data.forEach(m => {
                        const opt = document.createElement('option');
                        opt.value = m.idMunicipio;
                        opt.textContent = m.nombreMunicipio;
                        municipio.appendChild(opt);
                    });
                });
        });
    });
</script>

<script>
    // ===== Resumen de pago =====
    document.addEventListener('DOMContentLoaded', function() {
        const total = parseFloat('{{ $total }}') || 0;
        const tipoPago = document.getElementById('tipoPago');
        const efectivoGroup = document.getElementById('efectivoGroup');
        const efectivoExactoGroup = document.getElementById('efectivoExactoGroup');
        const efectivoRecibido = document.getElementById('efectivoRecibido');
        const efectivoExacto = document.getElementById('efectivoExacto');
        const montoAdelanto = document.getElementById('montoAdelanto');

        function actualizarResumenPago() {
            const adelanto = parseFloat(montoAdelanto?.value) || 0;
            const esEfectivoExacto = efectivoExacto?.checked;
            let efectivo = parseFloat(efectivoRecibido?.value) || 0;

            let saldo = total - adelanto;
            if (saldo < 0) saldo = 0;

            let vuelto = 0;

            if (esEfectivoExacto) {
                efectivo = total;
                if (efectivoRecibido) {
                    efectivoRecibido.value = total.toFixed(2);
                    efectivoRecibido.readOnly = true;
                }
            } else {
                if (efectivoRecibido) {
                    efectivoRecibido.readOnly = false;
                }
                if (efectivo > 0) {
                    vuelto = efectivo - total;
                }
            }

            document.getElementById('uiTotal').textContent = 'Bs ' + total.toFixed(2);
            document.getElementById('uiAdelanto').textContent = 'Bs ' + adelanto.toFixed(2);
            document.getElementById('uiSaldo').textContent = 'Bs ' + saldo.toFixed(2);
            document.getElementById('uiEfectivo').textContent = 'Bs ' + efectivo.toFixed(2);
            document.getElementById('uiVuelto').textContent = 'Bs ' + vuelto.toFixed(2);
        }

        tipoPago?.addEventListener('change', function() {
            const isEfectivo = this.value === 'efectivo';
            efectivoGroup.style.display = isEfectivo ? 'block' : 'none';
            efectivoExactoGroup.style.display = isEfectivo ? 'block' : 'none';
            actualizarResumenPago();
        });

        efectivoRecibido?.addEventListener('input', actualizarResumenPago);
        efectivoExacto?.addEventListener('change', actualizarResumenPago);
        montoAdelanto?.addEventListener('input', actualizarResumenPago);

        actualizarResumenPago();
    });
</script>

<script>
    // ===== Buscador de clientes (Autocompletado moderno) =====
    document.addEventListener('DOMContentLoaded', function () {
        const input = document.getElementById('clienteFilter');
        const dropdown = document.getElementById('clienteDropdown');
        const countWrap = document.getElementById('searchResultsCount');
        const countSpan = document.getElementById('resultsCount');
        const btnClear = document.getElementById('btnClearSearch');
        const hiddenInput = document.getElementById('clienteSeleccionado');
        const selectedInfo = document.getElementById('clienteSeleccionadoInfo');
        const selectedNombre = document.getElementById('clienteNombre');
        const selectedDetalles = document.getElementById('clienteDetalles');
        const btnDeseleccionar = document.getElementById('btnDeseleccionar');

        let lastController = null;
        let debounceTimer = null;
        let currentResults = [];

        function renderDropdown(results) {
            dropdown.innerHTML = '';
            
            if (!results || results.length === 0) {
                dropdown.style.display = 'none';
                countWrap.style.display = 'none';
                return;
            }

            // Agrupar resultados por tipo
            const naturales = results.filter(r => r.type === 'natural');
            const establecimientos = results.filter(r => r.type === 'establecimiento');

            // Renderizar clientes naturales
            if (naturales.length > 0) {
                const groupHeader = document.createElement('div');
                groupHeader.className = 'dropdown-header text-muted small px-3 py-1';
                groupHeader.textContent = 'Clientes Naturales';
                dropdown.appendChild(groupHeader);

                naturales.forEach(cliente => {
                    const item = document.createElement('div');
                    item.className = 'dropdown-item cursor-pointer py-2';
                    item.innerHTML = `
                        <div class="d-flex align-items-center">
                            <i class="fas fa-user me-2 text-primary"></i>
                            <div>
                                <div class="fw-bold">${cliente.label}</div>
                            </div>
                        </div>
                    `;
                    item.addEventListener('click', () => selectCliente(cliente));
                    dropdown.appendChild(item);
                });
            }

            // Renderizar establecimientos
            if (establecimientos.length > 0) {
                const groupHeader = document.createElement('div');
                groupHeader.className = 'dropdown-header text-muted small px-3 py-1';
                groupHeader.textContent = 'Establecimientos';
                dropdown.appendChild(groupHeader);

                establecimientos.forEach(cliente => {
                    const item = document.createElement('div');
                    item.className = 'dropdown-item cursor-pointer py-2';
                    item.innerHTML = `
                        <div class="d-flex align-items-center">
                            <i class="fas fa-building me-2 text-success"></i>
                            <div>
                                <div class="fw-bold">${cliente.label}</div>
                            </div>
                        </div>
                    `;
                    item.addEventListener('click', () => selectCliente(cliente));
                    dropdown.appendChild(item);
                });
            }

            dropdown.style.display = 'block';
            countSpan.textContent = results.length;
            countWrap.style.display = 'block';
        }

        function selectCliente(cliente) {
            // Guardar en campo oculto
            hiddenInput.value = cliente.value;
            
            // Mostrar información del cliente seleccionado
            selectedNombre.textContent = cliente.label;
            selectedDetalles.textContent = cliente.type === 'natural' ? 'Cliente Natural' : 'Establecimiento';
            selectedInfo.style.display = 'block';
            
            // Limpiar y ocultar dropdown
            input.value = '';
            dropdown.style.display = 'none';
            countWrap.style.display = 'none';
            
            // Enfocar siguiente campo
            document.getElementById('fechaEntrega')?.focus();
        }

        async function buscarClientes(q) {
            if (!q || q.trim().length < 2) {
                dropdown.style.display = 'none';
                countWrap.style.display = 'none';
                return;
            }

            // Cancelar petición anterior si existe
            if (lastController) {
                lastController.abort();
            }

            try {
                const controller = new AbortController();
                lastController = controller;

                const resp = await fetch(`{{ url('api/clientes/search') }}?q=${encodeURIComponent(q)}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    signal: controller.signal
                });
                
                if (!resp.ok) return;

                const json = await resp.json();
                currentResults = json.results || [];
                renderDropdown(currentResults);
            } catch (e) {
                if (e.name !== 'AbortError') {
                    console.error('Error buscando clientes', e);
                }
            }
        }

        // Escuchar cambios en el input
        input.addEventListener('keyup', function () {
            const value = this.value;
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                buscarClientes(value);
            }, 250);
        });

        // Cerrar dropdown al hacer clic fuera
        document.addEventListener('click', function(e) {
            if (!dropdown.contains(e.target) && e.target !== input) {
                dropdown.style.display = 'none';
            }
        });

        // Botón para limpiar búsqueda
        btnClear?.addEventListener('click', function () {
            input.value = '';
            dropdown.style.display = 'none';
            countWrap.style.display = 'none';
            input.focus();
        });

        // Botón para deseleccionar cliente
        btnDeseleccionar?.addEventListener('click', function () {
            hiddenInput.value = '';
            selectedInfo.style.display = 'none';
            input.focus();
        });
    });
</script>
@endpush