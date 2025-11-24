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
                                placeholder="Buscar por CI, nombre o teléfono..."
                                aria-label="Buscar cliente">
                            <button class="btn btn-outline-secondary" type="button" id="btnClearSearch" title="Limpiar búsqueda">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <!-- Contador de resultados -->
                        <div id="searchResultsCount" class="small text-muted mb-2" style="display: none;">
                            <span id="resultsCount">0</span> resultados encontrados
                        </div>
                        <select class="form-select" name="clienteSeleccionado" id="clienteSelect" size="8" required>
                            <option value="">Seleccione un cliente</option>
                            <optgroup label="Clientes naturales" id="naturalGroup">
                                @foreach($clientesNaturales as $cliente)
                                @php($valor = 'natural:' . $cliente->idCliente)
                                @php($documento = $cliente->user->ci ?? ($cliente->nit ?? ''))
                                @php($telefono = $cliente->user->telefono ?? '')
                                @php($nombre = $cliente->user->name ?? 'Cliente')

                                @php($primerApellido = $cliente->user->primerApellido ?? '')
                                @php($segundoApellido = $cliente->user->segundoApellido ?? '')
                                @php($nombreCompleto = trim("$nombre $primerApellido $segundoApellido"))
                                

                                @php($etiqueta = trim(($documento ? 'CI: '.$documento.' - ' : '') . $nombre . ($telefono ? ' - Tel: '.$telefono : '')))
                                <option value="{{ $valor }}"
                                    data-ci="{{ $documento }}"
                                    data-telefono="{{ $telefono }}"
                                    data-nombre="{{ $nombreCompleto }}"
                                    data-tipo="natural"
                                    {{ old('clienteSeleccionado') === $valor ? 'selected' : '' }}>
                                    {{ $etiqueta }}
                                </option>
                                @endforeach
                            </optgroup>
                            <optgroup label="Establecimientos" id="establecimientoGroup">
                                @foreach($clientesEstablecimientos as $establecimiento)
                                @php($valor = 'establecimiento:' . $establecimiento->idEstablecimiento)
                                @php($documento = $establecimiento->nit ?? '')
                                @php($telefono = $establecimiento->representante->telefono ?? '')
                                @php($nombre = $establecimiento->razonSocial ?? 'Establecimiento')
                                @php($etiqueta = trim(($documento ? 'NIT: '.$documento.' - ' : '') . $nombre . ($telefono ? ' - Tel: '.$telefono : '')))
                                <option value="{{ $valor }}"
                                    data-ci="{{ $documento }}"
                                    data-telefono="{{ $telefono }}"
                                    data-nombre="{{ $nombre }}"
                                    data-tipo="establecimiento"
                                    {{ old('clienteSeleccionado') === $valor ? 'selected' : '' }}>
                                    {{ $etiqueta }}
                                </option>
                                @endforeach
                            </optgroup>
                        </select>
                        <div class="form-text">
                            <i class="fas fa-info-circle me-1"></i>
                            Puedes buscar por cédula de identidad, NIT, nombre, apellido, razón social o teléfono
                        </div>
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



@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // =====
        // FUNCIONES DE BÚSQUEDA DE CLIENTES
        // =====
        const clienteFilter = document.getElementById('clienteFilter');
        const clienteSelect = document.getElementById('clienteSelect');
        const btnClearSearch = document.getElementById('btnClearSearch');
        const searchResultsCount = document.getElementById('searchResultsCount');
        const resultsCount = document.getElementById('resultsCount');
        const naturalGroup = document.getElementById('naturalGroup');
        const establecimientoGroup = document.getElementById('establecimientoGroup');

        function filtrarClientes() {
            const filtro = clienteFilter.value.toLowerCase().trim();
            let totalResultados = 0;

            // Filtrar clientes naturales
            const opcionesNaturales = naturalGroup.querySelectorAll('option');
            let naturalesVisibles = 0;
            opcionesNaturales.forEach(option => {
                const texto = option.textContent.toLowerCase();
                const ci = option.getAttribute('data-ci')?.toLowerCase() || '';
                const telefono = option.getAttribute('data-telefono')?.toLowerCase() || '';
                const nombre = option.getAttribute('data-nombre')?.toLowerCase() || '';
                
                const coincide = texto.includes(filtro) || 
                                ci.includes(filtro) || 
                                telefono.includes(filtro) || 
                                nombre.includes(filtro);
                
                option.style.display = coincide ? '' : 'none';
                if (coincide) naturalesVisibles++;
            });

            // Filtrar establecimientos
            const opcionesEstablecimientos = establecimientoGroup.querySelectorAll('option');
            let establecimientosVisibles = 0;
            opcionesEstablecimientos.forEach(option => {
                const texto = option.textContent.toLowerCase();
                const nit = option.getAttribute('data-ci')?.toLowerCase() || '';
                const telefono = option.getAttribute('data-telefono')?.toLowerCase() || '';
                const nombre = option.getAttribute('data-nombre')?.toLowerCase() || '';
                
                const coincide = texto.includes(filtro) || 
                                nit.includes(filtro) || 
                                telefono.includes(filtro) || 
                                nombre.includes(filtro);
                
                option.style.display = coincide ? '' : 'none';
                if (coincide) establecimientosVisibles++;
            });

            // Mostrar/ocultar grupos según tengan resultados
            naturalGroup.style.display = naturalesVisibles > 0 ? '' : 'none';
            establecimientoGroup.style.display = establecimientosVisibles > 0 ? '' : 'none';

            // Actualizar contador
            totalResultados = naturalesVisibles + establecimientosVisibles;
            if (filtro) {
                searchResultsCount.style.display = 'block';
                resultsCount.textContent = totalResultados;
                
                // Auto-seleccionar si hay un solo resultado y el filtro tiene al menos 3 caracteres
                if (totalResultados === 1 && filtro.length >= 3) {
                    const opcionVisible = clienteSelect.querySelector('option[style=""]:not([style*="display: none"])');
                    if (opcionVisible && opcionVisible.value) {
                        clienteSelect.value = opcionVisible.value;
                        clienteSelect.dispatchEvent(new Event('change'));
                    }
                }
            } else {
                searchResultsCount.style.display = 'none';
            }
        }

        function limpiarBusqueda() {
            clienteFilter.value = '';
            filtrarClientes();
            clienteSelect.value = '';
        }

        // Eventos de búsqueda
        clienteFilter?.addEventListener('input', filtrarClientes);
        btnClearSearch?.addEventListener('click', limpiarBusqueda);

        // =====
        // FUNCIONES DE CÁLCULO
        // =====
        let tallaPriceMap = new Map();
        let precioBase = 0;

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

        function formatear(numero) {
            return 'Bs ' + new Intl.NumberFormat('es-BO', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }).format(numero);
        }

        // Eventos para cálculo automático
        document.getElementById('montoAdelanto')?.addEventListener('input', recalcTotales);
        document.getElementById('efectivoRecibido')?.addEventListener('input', recalcTotales);
        document.getElementById('efectivoExacto')?.addEventListener('change', recalcTotales);
        document.getElementById('tipoPago')?.addEventListener('change', function() {
            const efectivoGroup = document.getElementById('efectivoGroup');
            const efectivoExactoGroup = document.getElementById('efectivoExactoGroup');
            
            if (this.value === 'efectivo') {
                efectivoGroup.style.display = 'block';
                efectivoExactoGroup.style.display = 'block';
            } else {
                efectivoGroup.style.display = 'none';
                efectivoExactoGroup.style.display = 'none';
            }
        });

        // Inicializar cálculos
        recalcTotales();
    });
</script>