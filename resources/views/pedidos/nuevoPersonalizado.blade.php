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
            <a href="{{ route('pedidos.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Volver a Pedidos
            </a>
        </div>

        <div class="card-body">
            <form id="formNuevoPedido" action="{{ route('pedidos.guardar-nuevo') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="tipoCliente" id="tipoCliente" value="">
                <input type="hidden" name="idCliente" id="idCliente" value="">
                <input type="hidden" name="idEstablecimiento" id="idEstablecimiento" value="">
                <input type="hidden" name="ruta_diseno" id="ruta_diseno" value="">

                <div class="row g-3">
                    <!-- ====== Columna izquierda principal ====== -->
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

                            {{-- 🔥 NUEVO CAMPO - ASIGNAR DISEÑADOR --}}
                            <div class="col-md-6">
                                <label class="form-label">Asignar Diseñador *</label>
                                <select name="idEmpleado" class="form-select" required>
                                    <option value="">Seleccionar diseñador</option>
                                    @foreach($diseñadores as $diseñador)
                                        <option value="{{ $diseñador->idEmpleado }}" {{ old('idEmpleado') == $diseñador->idEmpleado ? 'selected' : '' }}>
                                            {{ $diseñador->user->name }} {{ $diseñador->user->primerApellido }} @if($diseñador->user->segundApellido) {{ $diseñador->user->segundApellido }} @endif
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Diseñador responsable de este pedido</small>
                            </div>

                            <hr class="mt-3" />

                            <!-- ====== Tabla de items ====== -->
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
                                                    <select name="idTallas[]" class="form-select form-select-sm sel-talla" required>
                                                        <option value="">Seleccionar talla</option>
                                                        @foreach($tallas as $t)
                                                            <option value="{{ $t->idTallas }}">{{ $t->nombre }}</option>
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

                            <!-- ====== Carga de diseño ====== -->
                            <div class="col-12 mt-4">
                                <div class="card shadow-sm border-0">
                                    <div class="card-header bg-white">
                                        <i class="fas fa-cloud-upload-alt me-2 text-primary"></i>
                                        <strong>Sube tu diseño (Opcional)</strong>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="disenoPersonalizado" class="form-label fw-semibold">Archivo de diseño</label>

                                            <div id="dzArea" class="border rounded-3 p-4 d-flex align-items-center justify-content-center text-center bg-light-subtle" style="cursor:pointer;">
                                                <div>
                                                    <i class="fas fa-cloud-upload-alt fa-2x mb-2"></i>
                                                    <div class="small text-muted">
                                                        Arrastrá y soltá tu archivo aquí o
                                                        <span class="text-primary text-decoration-underline">haz clic para seleccionarlo</span>.
                                                    </div>
                                                    <div id="dzFilename" class="mt-1 small text-muted"></div>
                                                </div>
                                            </div>

                                            <input type="file" name="disenoPersonalizado" id="disenoPersonalizado" class="form-control mt-2" accept=".jpg,.jpeg,.png,.pdf" style="display:none;" />
                                        </div>

                                        <!-- Vista previa -->
                                        <div id="previewContainer" class="row g-3" style="display:none;">
                                            <div class="col-12">
                                                <div class="border rounded-3 p-2 bg-white">
                                                    <img id="previewImagen" class="img-fluid rounded d-none" alt="Vista previa" style="max-height:300px;">
                                                    <iframe id="previewPdf" class="d-none" style="width:100%; height:420px; border:0; border-radius:.5rem; background:#fff;"></iframe>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="border rounded-3 p-3 bg-white">
                                                    <strong class="d-block mb-2">Archivo seleccionado</strong>
                                                    <div class="small text-muted mb-3" id="metaInfo">—</div>

                                                    <div class="d-flex gap-2">
                                                        <button type="button" id="btnClear" class="btn btn-outline-secondary flex-grow-1">
                                                            <i class="fas fa-eraser me-1"></i> Limpiar selección
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="alert alert-info mt-3">
                                            <i class="fas fa-info-circle me-2"></i>
                                            <strong>Consejos:</strong>
                                            <ul class="mb-0 mt-2">
                                                <li>Sube imágenes nítidas y en alta resolución.</li>
                                                <li>Si es PDF, asegúrate que incluya curvas y fuentes incrustadas.</li>
                                                <li>Indica nombre/número si deseas personalización individual.</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Acciones -->
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

                    {{-- ===== Panel derecho: Detalles de producto (Polera / Corto) ===== --}}
                    <div class="col-lg-4">
                        <div class="card border-0 shadow-sm mb-3" id="cardDetallesPolera" style="display:none;">
                            <div class="card-header bg-light fw-bold">DETALLES POLERA</div>
                            <div class="card-body">
                                <div class="mb-2">
                                    <div class="small text-muted">Tipo Cuello</div>
                                    <div id="polera_cuello" class="btn-group flex-wrap gap-2" role="group"></div>
                                </div>
                                <div class="mb-2">
                                    <div class="small text-muted">Tipo de Manga</div>
                                    <div id="polera_manga" class="btn-group flex-wrap gap-2" role="group"></div>
                                </div>
                                <div class="mb-2">
                                    <div class="small text-muted">Tipo de Tela</div>
                                    <div id="polera_tela" class="btn-group flex-wrap gap-2" role="group"></div>
                                </div>
                            </div>
                        </div>

                        <div class="card border-0 shadow-sm mb-3" id="cardDetallesCorto" style="display:none;">
                            <div class="card-header bg-light fw-bold">DETALLES CORTO</div>
                            <div class="card-body">
                                <div class="mb-2">
                                    <div class="small text-muted">Tipo de Sublimado</div>
                                    <div id="corto_sublimado" class="btn-group flex-wrap gap-2" role="group"></div>
                                </div>
                            </div>
                        </div>

                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-light fw-bold">Datos del producto</div>
                            <div class="card-body">
                                {{-- Toggle Producto --}}
                                <div class="btn-group w-100 mb-3" role="group" id="grpProducto">
                                    <input type="radio" class="btn-check" name="prodTipo" id="prod_pyc" autocomplete="off" value="pyc" checked>
                                    <label class="btn btn-outline-primary" for="prod_pyc">POLERA Y CORTO</label>

                                    <input type="radio" class="btn-check" name="prodTipo" id="prod_polera" autocomplete="off" value="polera">
                                    <label class="btn btn-outline-primary" for="prod_polera">SOLO POLERA</label>

                                    <input type="radio" class="btn-check" name="prodTipo" id="prod_corto" autocomplete="off" value="corto">
                                    <label class="btn btn-outline-primary" for="prod_corto">SOLO CORTO</label>
                                </div>

                                {{-- Tallas (pills) --}}
                                <div class="mb-3">
                                    <div class="small text-muted mb-1">Tallas</div>
                                    <div class="d-flex flex-wrap gap-2" id="pillsTallas">
                                        @foreach($tallas as $t)
                                            <button type="button" class="btn btn-outline-secondary pill-talla" data-id="{{ $t->idTallas }}" data-nombre="{{ $t->nombre }}">{{ $t->nombre }}</button>
                                        @endforeach
                                    </div>
                                    <input type="hidden" id="tallaSeleccionadaNombre">
                                    <input type="hidden" id="tallaSeleccionadaId">
                                </div>

                                {{-- Cantidad --}}
                                <div class="mb-3 d-flex align-items-center gap-2">
                                    <div class="small text-muted">Cantidad</div>
                                    <div class="input-group" style="width:140px;">
                                        <button class="btn btn-outline-secondary" type="button" id="qtyLess">-</button>
                                        <input type="number" class="form-control text-center" id="qty" value="1" min="1">
                                        <button class="btn btn-outline-secondary" type="button" id="qtyMore">+</button>
                                    </div>
                                </div>

                                {{-- Personalizar --}}
                                <div class="row g-2 mb-3" id="boxPersonalizar">
                                    <div class="col-6">
                                        <label class="form-label small">NÚMERO</label>
                                        <input type="text" class="form-control" id="uiNumero" placeholder="SIN NUMERO">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small">NOMBRE</label>
                                        <input type="text" class="form-control" id="uiNombre" placeholder="SIN NOMBRE">
                                    </div>
                                </div>

                                <button type="button" class="btn btn-primary w-100" id="btnAgregarProducto">
                                    + Agregar Producto
                                </button>

                                {{-- Hidden para selecciones --}}
                                <input type="hidden" id="sel_polera_cuello">
                                <input type="hidden" id="sel_polera_manga">
                                <input type="hidden" id="sel_polera_tela">
                                <input type="hidden" id="sel_corto_sublimado">
                            </div>
                        </div>
                    </div>

                    <!-- ====== Panel de cliente (columna separada para layout) ====== -->
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

                                                <optgroup label="Clientes Naturales">
                                                    @foreach($clientesNaturales as $cliente)
                                                        @php($val = 'natural:' . $cliente->idClienteNatural)
                                                        @php($doc = $cliente->user->ci ?? '')
                                                        @php($tel = $cliente->user->telefono ?? '')
                                                        @php($nombre = $cliente->user->name ?? 'Cliente')
                                                        @php($apellido = $cliente->user->primerApellido ?? '')
                                                        @php($label = trim(($doc ? 'CI: '.$doc.' - ' : '') . $nombre . ' ' . $apellido . ($tel ? ' - Tel: '.$tel : '')))

                                                        <option value="{{ $val }}" data-ci="{{ $doc }}" data-telefono="{{ $tel }}" {{ $oldClienteSel === $val ? 'selected' : '' }}>
                                                            {{ $label }}
                                                        </option>
                                                    @endforeach
                                                </optgroup>

                                                <optgroup label="Establecimientos">
                                                    @foreach($establecimientos as $establecimiento)
                                                        @php($val = 'establecimiento:' . $establecimiento->idClienteEstablecimiento)
                                                        @php($doc = $establecimiento->nit ?? '')
                                                        @php($tel = $establecimiento->telefono ?? '')
                                                        @php($nombre = $establecimiento->razonSocial ?? 'Establecimiento')
                                                        @php($label = trim(($doc ? 'NIT: '.$doc.' - ' : '') . $nombre . ($tel ? ' - Tel: '.$tel : '')))

                                                        <option value="{{ $val }}" data-ci="{{ $doc }}" data-telefono="{{ $tel }}" {{ $oldClienteSel === $val ? 'selected' : '' }}>
                                                            {{ $label }}
                                                        </option>
                                                    @endforeach
                                                </optgroup>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                {{-- (Opcional) Resumen de totales UI si lo usas en esta tarjeta
                                <hr>
                                <div class="small text-muted">Resumen</div>
                                <div class="d-flex justify-content-between"><span>Cant. total</span><strong id="uiCantTotal">0</strong></div>
                                <div class="d-flex justify-content-between"><span>Precio unit.</span><strong id="uiPrecioUnit">—</strong></div>
                                <div id="uiTablaTallas" class="mt-2"></div>
                                <div class="d-flex justify-content-between mt-2"><span>Total</span><strong id="uiTotal">Bs 0.00</strong></div>
                                --}}
                            </div>
                        </div>
                    </div>
                    <!-- ====== /Panel cliente ====== -->
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .pill-talla.active, .btn-group .btn.btn-primary {
        box-shadow: 0 0 0 .2rem rgba(13, 110, 253, .15);
    }
    .pill-talla { min-width: 48px; }
    #cardDetallesPolera .btn, #cardDetallesCorto .btn { margin-bottom: .25rem; }
</style>
@endpush

@push('scripts')
<script>
(() => {
  // ===== Helpers globales =====
  window.addEventListener('error', (ev) => {
    console.error('[GLOBAL onerror]', {
      message: ev?.message, filename: ev?.filename, lineno: ev?.lineno, colno: ev?.colno, error: ev?.error
    });
  });
  window.addEventListener('unhandledrejection', (ev) => {
    console.error('[GLOBAL unhandledrejection]', ev?.reason);
  });

  const EL = (id) => document.getElementById(id);
  const tbody = EL('tbodyItems');

  // Opciones de tallas (para addRow)
  const TALLA_OPTIONS = `{!! collect($tallas)->map(fn($t) => '<option value="'.$t->idTallas.'">'.e($t->nombre).'</option>')->implode('') !!}`;

  // ===== API helper =====
  async function fetchJSON(url) {
    const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
    if (!res.ok) throw new Error(`HTTP ${res.status} ${res.statusText} :: ${await res.text()}`);
    return res.json();
  }

  // ===== Estado de precios por talla =====
  let tallaPriceMap = new Map();

  // ===== Cargar opciones/características por producto =====
  async function onProductoChange(idProducto) {
    const cont = EL('caracteristicasContainer');
    const wrap = EL('opcionesContainer');
    if (!cont || !wrap) return;

    cont.innerHTML = '';
    wrap.style.display = 'none';
    if (!idProducto) return;
    try {
      const data = await fetchJSON(`{{ url('api/producto') }}/${idProducto}/opciones`);
      const opciones = data.opciones || [];
      for (const op of opciones) {
        const col = document.createElement('div');
        col.className = 'col-md-3';
        const label = document.createElement('label');
        label.className = 'form-label';
        label.textContent = op.nombreOpcion;
        const select = document.createElement('select');
        select.className = 'form-select';
        select.name = `caracteristicas[${op.idOpcion ?? 'otros'}]`;
        select.innerHTML = `<option value="">Seleccionar</option>` + (op.caracteristicas || [])
          .map(c => `<option value="${c.idCaracteristica}">${c.nombre}</option>`).join('');
        col.append(label, select);
        cont.appendChild(col);
      }
      if (opciones.length) wrap.style.display = '';
    } catch (e) {
      console.error('[onProductoChange]', e);
    }
  }

  // ===== Cargar precios por talla =====
  async function loadTallaPrecios(idProducto) {
    tallaPriceMap = new Map();
    if (!idProducto) return;
    try {
      const data = await fetchJSON(`{{ url('api/producto') }}/${idProducto}/tallas-precios`);
      (data.precios || []).forEach(p => {
        tallaPriceMap.set(String(p.idTallas), Number(p.precioUnitario || 0));
      });
    } catch (e) {
      console.error('[loadTallaPrecios]', e);
    }
  }

  // ===== Upload diseño (drag & drop + preview) =====
  (function initUpload() {
    const dz = EL('dzArea');
    const fileInput = EL('disenoPersonalizado');
    const filenameEl = EL('dzFilename');
    const previewWrap = EL('previewContainer');
    const imgEl = EL('previewImagen');
    const pdfEl = EL('previewPdf');
    const metaInfo = EL('metaInfo');
    const btnClear = EL('btnClear');
    const rutaDiseno = EL('ruta_diseno');
    if (!dz || !fileInput) return;

    let currentURL = null;
    const MAX_MB = 5, MAX_BYTES = MAX_MB * 1024 * 1024;
    const ALLOWED = ['image/jpeg','image/png','application/pdf'];

    dz.addEventListener('click', () => fileInput.click());
    dz.addEventListener('dragover', (e) => { e.preventDefault(); dz.classList.add('border-primary'); });
    dz.addEventListener('dragleave', () => dz.classList.remove('border-primary'));
    dz.addEventListener('drop', (e) => {
      e.preventDefault(); dz.classList.remove('border-primary');
      if (e.dataTransfer.files?.[0]) {
        fileInput.files = e.dataTransfer.files;
        handleFile(fileInput.files[0]);
      }
    });

    fileInput.addEventListener('change', () => {
      const f = fileInput.files?.[0];
      if (!f) { clearPreview(); return; }
      handleFile(f);
    });

    function handleFile(file) {
      if (!ALLOWED.includes(file.type)) { alert('Formato no permitido. Usa JPG, PNG o PDF.'); clearPreview(); return; }
      if (file.size > MAX_BYTES) { alert(`El archivo supera ${MAX_MB} MB.`); clearPreview(); return; }
      filenameEl.textContent = `${file.name} • ${(file.size / 1024 / 1024).toFixed(2)} MB`;

      if (currentURL) URL.revokeObjectURL(currentURL);
      currentURL = URL.createObjectURL(file);

      if (file.type.startsWith('image/')) {
        imgEl.src = currentURL; imgEl.classList.remove('d-none');
        pdfEl.classList.add('d-none'); pdfEl.src = '';
      } else {
        pdfEl.src = currentURL + '#toolbar=0&navpanes=0&scrollbar=0';
        pdfEl.classList.remove('d-none'); imgEl.classList.add('d-none'); imgEl.src = '';
      }

      metaInfo.innerHTML = `
        <div><strong>Nombre:</strong> ${file.name}</div>
        <div><strong>Tamaño:</strong> ${(file.size / 1024 / 1024).toFixed(2)} MB</div>
        <div><strong>Tipo:</strong> ${file.type}</div>
      `;

      if (rutaDiseno) rutaDiseno.value = file.name;
      previewWrap.style.display = 'flex';
    }

    function clearPreview() {
      if (currentURL) URL.revokeObjectURL(currentURL);
      currentURL = null;
      fileInput.value = '';
      filenameEl.textContent = '';
      imgEl.src = ''; pdfEl.src = '';
      imgEl.classList.add('d-none'); pdfEl.classList.add('d-none');
      previewWrap.style.display = 'none';
      metaInfo.textContent = '—';
      if (rutaDiseno) rutaDiseno.value = '';
    }

    btnClear?.addEventListener('click', clearPreview);
  })();

  // ===== Mostrar/ocultar columna "Número" según producto =====
  function toggleNumeroByProducto() {
    const sel = EL('idProducto'); // si existe
    const thNum = EL('thNumero');
    const filas = document.querySelectorAll('#tbodyItems tr.item-row');
    if (!sel || !thNum) return;

    const opt = sel.options[sel.selectedIndex];
    const nombre = (opt?.dataset?.nombre || '').toLowerCase();
    const esChamarra = nombre.includes('chamarra');
    const esPolera   = nombre.includes('polera');
    const mostrarNumero = esPolera && !esChamarra;

    thNum.style.display = mostrarNumero ? '' : 'none';
    filas.forEach(tr => {
      const colNum = tr.querySelector('.col-numero');
      const inp = tr.querySelector('.inp-numero');
      if (colNum) colNum.style.display = mostrarNumero ? '' : 'none';
      if (!mostrarNumero && inp) inp.value = '';
    });
  }

  // ===== Agregar / quitar filas =====
  EL('btnAddRow')?.addEventListener('click', addRow);
  tbody?.addEventListener('click', (e) => {
    if (e.target.closest('.btnRemoveRow')) {
      const rows = tbody.querySelectorAll('tr.item-row');
      if (rows.length > 1) {
        e.target.closest('tr.item-row').remove();
        toggleNumeroByProducto();
        recalcTotales();
      }
    }
  });
  tbody?.addEventListener('change', (e) => {
    if (e.target.classList.contains('sel-talla')) recalcTotales();
  });

  function addRow() {
    const tr = document.createElement('tr');
    tr.className = 'item-row';
    tr.innerHTML = `
      <td>
        <select name="idTallas[]" class="form-select form-select-sm sel-talla" required>
          <option value="">Seleccionar talla</option>
          ${TALLA_OPTIONS}
        </select>
      </td>
      <td><input type="number" name="cantidad[]" class="form-control form-control-sm inp-cantidad" min="1" value="1" required></td>
      <td><input type="text" name="nombrePersonalizado[]" class="form-control form-control-sm inp-nombre" placeholder="Nombre"></td>
      <td class="col-numero"><input type="text" name="numeroPersonalizado[]" class="form-control form-control-sm inp-numero" placeholder="Número"></td>
      <td><input type="text" name="observaciones[]" class="form-control form-control-sm inp-obs" placeholder="Detalles adicionales..."></td>
      <td class="text-center">
        <button type="button" class="btn btn-sm btn-outline-danger btnRemoveRow" title="Quitar fila"><i class="fas fa-trash"></i></button>
      </td>`;
    tbody.appendChild(tr);
    toggleNumeroByProducto();
    recalcTotales();
  }

  // ===== Mapeo hidden fields de cliente =====
  EL('clienteSelect')?.addEventListener('change', function() {
    const value = String(this.value || '');
    const tipoCliente = EL('tipoCliente');
    const idCliente = EL('idCliente');
    const idEstablecimiento = EL('idEstablecimiento');
    if (!tipoCliente || !idCliente || !idEstablecimiento) return;

    if (value.startsWith('natural:')) {
      tipoCliente.value = 'natural';
      idCliente.value = value.split(':')[1] || '';
      idEstablecimiento.value = '';
    } else if (value.startsWith('establecimiento:')) {
      tipoCliente.value = 'establecimiento';
      idEstablecimiento.value = value.split(':')[1] || '';
      idCliente.value = '';
    } else {
      tipoCliente.value = '';
      idCliente.value = '';
      idEstablecimiento.value = '';
    }
  });

  // ===== Filtro local por texto =====
  EL('clienteFilter')?.addEventListener('input', function() {
    const search = this.value.toLowerCase();
    const options = document.querySelectorAll('#clienteSelect option');
    options.forEach(option => {
      if (option.value === '') return;
      const text = option.textContent.toLowerCase();
      option.style.display = text.includes(search) ? '' : 'none';
    });
  });

  // ===== Búsqueda AJAX de clientes (debounce) =====
  (function initClienteSearchAJAX() {
    const input = EL('clienteFilter');
    const select = EL('clienteSelect');
    if (!input || !select) return;

    const renderResults = (results) => {
      select.innerHTML = '';
      const ph = document.createElement('option');
      ph.value = ''; ph.textContent = 'Seleccione un cliente';
      select.appendChild(ph);
      const grpNat = document.createElement('optgroup'); grpNat.label = 'Clientes Naturales';
      const grpEst = document.createElement('optgroup'); grpEst.label = 'Establecimientos';
      (results || []).forEach(r => {
        const o = document.createElement('option');
        o.value = r.value; o.textContent = r.label;
        (r.type === 'natural' ? grpNat : grpEst).appendChild(o);
      });
      select.append(grpNat, grpEst);
    };

    let t = null;
    input.addEventListener('input', () => {
      clearTimeout(t);
      t = setTimeout(async () => {
        const q = input.value.trim();
        if (!q) { renderResults([]); return; }
        try {
          const url = `{{ url('api/clientes/search') }}?q=${encodeURIComponent(q)}`;
          const data = await fetchJSON(url);
          renderResults(data.results || []);
        } catch (e) {
          console.error('buscar clientes', e);
        }
      }, 300);
    });
  })();

  // ===== Validación form submit =====
  EL('formNuevoPedido')?.addEventListener('submit', (e) => {
    const selCli = EL('clienteSelect');
    const tipo = EL('tipoCliente');
    if (!selCli?.value) {
      e.preventDefault();
      alert('Selecciona un cliente.');
      selCli?.focus();
      return;
    }
    const v = String(selCli.value);
    if (v.startsWith('natural:') && !tipo.value) tipo.value = 'natural';
    if (v.startsWith('establecimiento:') && !tipo.value) tipo.value = 'establecimiento';
  });

  // ===== Totales / pagos (solo si existen los elementos UI) =====
  function getPrecioUnitarioBase() {
    const sel = EL('idProducto');
    if (!sel || !sel.value) return 0;
    const p = parseFloat(sel.options[sel.selectedIndex]?.dataset?.precio || '0');
    return isNaN(p) ? 0 : p;
  }
  function precioUnitarioPorTalla(idTallas) {
    const key = String(idTallas || '');
    if (key && tallaPriceMap.has(key)) {
      const v = Number(tallaPriceMap.get(key));
      if (!isNaN(v) && v > 0) return v;
    }
    return getPrecioUnitarioBase();
  }
  function getDetallePorTalla() {
    const rows = document.querySelectorAll('#tbodyItems tr.item-row');
    const map = new Map();
    rows.forEach(tr => {
      const sel = tr.querySelector('.sel-talla');
      const inp = tr.querySelector('.inp-cantidad');
      if (!sel || !inp) return;
      const opt = sel.options[sel.selectedIndex];
      const nombre = (opt?.text || '').trim();
      const id = opt?.value || '';
      const cant = parseInt(inp.value || '0');
      if (!nombre || !id || isNaN(cant) || cant <= 0) return;
      const unit = precioUnitarioPorTalla(id);
      if (!map.has(nombre)) map.set(nombre, { nombre, cant: 0, unit });
      const agg = map.get(nombre);
      agg.cant += cant;
      agg.unit = unit;
    });
    return Array.from(map.values()).map(it => ({ ...it, subtotal: it.unit * it.cant }));
  }
  function formatear(n) { return 'Bs ' + Number(n).toFixed(2); }

  function recalcTotales() {
    const detalle = getDetallePorTalla();
    const total = detalle.reduce((acc, d) => acc + d.subtotal, 0);
    const cantTotal = detalle.reduce((acc, d) => acc + d.cant, 0);

    EL('uiCantTotal') && (EL('uiCantTotal').textContent = String(cantTotal));
    const units = Array.from(new Set(detalle.map(d => d.unit)));
    EL('uiPrecioUnit') && (EL('uiPrecioUnit').textContent = units.length === 1 ? formatear(units[0]) : 'Mixto');

    const uiTabla = EL('uiTablaTallas');
    if (uiTabla) {
      if (!detalle.length) {
        uiTabla.innerHTML = '<span class="text-muted">—</span>';
      } else {
        const rowsHtml = detalle.map(d => `
          <tr>
            <td>${d.nombre}</td>
            <td class="text-end">${d.cant}</td>
            <td class="text-end">${formatear(d.unit)}</td>
            <td class="text-end">${formatear(d.subtotal)}</td>
          </tr>`).join('');
        const totalTabla = formatear(total);
        uiTabla.innerHTML = `
          <div class="table-responsive">
            <table class="table table-sm table-bordered mb-0">
              <thead class="table-light">
                <tr>
                  <th>Talla</th><th class="text-end">Cantidad</th><th class="text-end">P.Unit</th><th class="text-end">Subtotal</th>
                </tr>
              </thead>
              <tbody>${rowsHtml}</tbody>
              <tfoot><tr><th colspan="3" class="text-end">Total de todo lo pedido</th><th class="text-end">${totalTabla}</th></tr></tfoot>
            </table>
          </div>`;
      }
    }

    EL('uiTotal') && (EL('uiTotal').textContent = formatear(total));

    const inpAd = EL('montoAdelanto');
    let adelanto = parseFloat(inpAd?.value || '0');
    if (isNaN(adelanto) || adelanto < 0) adelanto = 0;
    EL('uiAdelanto') && (EL('uiAdelanto').textContent = formatear(adelanto));
    const saldo = Math.max(total - adelanto, 0);
    EL('uiSaldo') && (EL('uiSaldo').textContent = formatear(saldo));

    const chkExacto = EL('efectivoExacto');
    const inpEfec = EL('efectivoRecibido');
    const tipo = (EL('tipoPago')?.value || '').toLowerCase();
    const isEfec = (tipo === 'efectivo');

    if (isEfec && chkExacto && inpEfec) {
      if (chkExacto.checked) {
        inpEfec.value = total.toFixed(2);
        inpEfec.readOnly = true;
      } else {
        inpEfec.readOnly = false;
      }
    }
    let efectivo = 0;
    if (isEfec && inpEfec) {
      efectivo = parseFloat(inpEfec.value || '0');
      if (isNaN(efectivo)) efectivo = 0;
    }
    EL('uiEfectivo') && (EL('uiEfectivo').textContent = formatear(efectivo));
    const vuelto = Math.max(efectivo - total, 0);
    EL('uiVuelto') && (EL('uiVuelto').textContent = formatear(vuelto));
  }

  EL('tbodyItems')?.addEventListener('input', (e) => {
    if (e.target.classList.contains('inp-cantidad')) recalcTotales();
  });
  EL('efectivoRecibido')?.addEventListener('input', recalcTotales);
  EL('efectivoExacto')?.addEventListener('change', recalcTotales);
  ['montoAdelanto'].forEach(id => EL(id)?.addEventListener('input', recalcTotales));

  function updatePagoUI() {
    const tipo = (EL('tipoPago')?.value || '').toLowerCase();
    const isEfec = tipo === 'efectivo';
    const grpEfec = EL('efectivoGroup');
    const grpExacto = EL('efectivoExactoGroup');
    const inpEfec = EL('efectivoRecibido');
    const chkExacto = EL('efectivoExacto');
    if (grpEfec) grpEfec.style.display = isEfec ? '' : 'none';
    if (grpExacto) grpExacto.style.display = isEfec ? '' : 'none';
    if (!isEfec) {
      if (chkExacto) chkExacto.checked = false;
      if (inpEfec) { inpEfec.value = ''; inpEfec.readOnly = true; }
    } else {
      if (inpEfec) inpEfec.readOnly = false;
    }
  }
  EL('tipoPago')?.addEventListener('change', () => { updatePagoUI(); recalcTotales(); });

  // ===== Init =====
  document.addEventListener('DOMContentLoaded', async () => {
    const selProd = EL('idProducto'); // este select puede estar en otro partial
    if (selProd) {
      toggleNumeroByProducto();
      if (selProd.value) {
        await onProductoChange(selProd.value);
        await loadTallaPrecios(selProd.value);
      }
      selProd.addEventListener('change', async (e) => {
        const idProd = e.target.value;
        toggleNumeroByProducto();
        await onProductoChange(idProd);
        await loadTallaPrecios(idProd);
        recalcTotales();
      });
    }
    updatePagoUI();
    recalcTotales();
  });
})();
</script>
@endpush
