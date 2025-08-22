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
            <form action="{{ route('pedidos.guardar-nuevo') }}" method="POST">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Tipo de Cliente *</label>
                        <select name="tipoCliente" id="tipoCliente" class="form-select" required>
                            <option value="natural" {{ old('tipoCliente')==='natural' ? 'selected' : '' }}>Natural</option>
                            <option value="establecimiento" {{ old('tipoCliente')==='establecimiento' ? 'selected' : '' }}>Establecimiento</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Fecha de Entrega *</label>
                        <input type="date" name="fechaEntrega" class="form-control" value="{{ old('fechaEntrega') }}" required>
                    </div>
                    <div class="col-md-6" id="wrapClienteNatural">
                        <label class="form-label">Cliente *</label>
                        <select name="idCliente" class="form-select">
                            <option value="">Seleccionar cliente</option>
                            @foreach($clientesNaturales as $c)
                                <option value="{{ $c->idCliente }}" {{ old('idCliente')==$c->idCliente ? 'selected' : '' }}>{{ $c->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6" id="wrapClienteEstablecimiento" style="display:none;">
                        <label class="form-label">Establecimiento *</label>
                        <select name="idEstablecimiento" class="form-select">
                            <option value="">Seleccionar establecimiento</option>
                            @foreach($clientesEstablecimientos as $e)
                                <option value="{{ $e->idEstablecimiento }}" {{ old('idEstablecimiento')==$e->idEstablecimiento ? 'selected' : '' }}>{{ $e->nombreEstablecimiento }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Lugar de Entrega *</label>
                        <input type="text" name="lugarEntrega" class="form-control" value="{{ old('lugarEntrega','Recojo en tienda') }}" required>
                    </div>

                    <hr class="mt-3"/>

                    <div class="col-md-6">
                        <label class="form-label">Producto (solo IDs 1–4) *</label>
                        <select name="idProducto" id="idProducto" class="form-select" required>
                            <option value="">Seleccionar producto</option>
                            @foreach($productos as $p)
                                <option value="{{ $p->idProducto }}" {{ old('idProducto')==$p->idProducto ? 'selected' : '' }}>
                                    [#{{ $p->idProducto }}] {{ $p->nombre }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Solo se muestran productos permitidos (IDs 1 a 4).</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Variante</label>
                        <select id="idVariante" class="form-select" required>
                            <option value="">Seleccionar variante</option>
                        </select>
                        <small class="text-muted">Selecciona la variante. El producto base se asignará automáticamente.</small>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Talla *</label>
                        <select name="idTalla" id="idTalla" class="form-select" required>
                            <option value="">Seleccionar talla</option>
                            @foreach($tallas as $t)
                                <option value="{{ $t->idTalla }}" {{ old('idTalla')==$t->idTalla ? 'selected' : '' }}>{{ $t->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12" id="opcionesContainer" style="display:none;">
                        <div class="card border-0">
                            <div class="card-body p-2">
                                <div class="row g-3" id="caracteristicasContainer">
                                    <!-- Aquí se renderizan opciones y características -->
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Cantidad *</label>
                        <input type="number" name="cantidad" class="form-control" min="1" value="{{ old('cantidad',1) }}" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Nombre</label>
                        <input type="text" name="nombrePersonalizado" class="form-control" value="{{ old('nombrePersonalizado') }}" placeholder="Nombre">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Número</label>
                        <input type="text" name="numeroPersonalizado" class="form-control" value="{{ old('numeroPersonalizado') }}" placeholder="Número">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Texto Adicional</label>
                        <input type="text" name="textoAdicional" class="form-control" value="{{ old('textoAdicional') }}" placeholder="Texto">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Observaciones</label>
                        <textarea name="observaciones" class="form-control" rows="2" placeholder="Detalles adicionales...">{{ old('observaciones') }}</textarea>
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
    async function cargarVariantesActivas() {
        const selVar = document.getElementById('idVariante');
        selVar.innerHTML = '<option value="">Cargando...</option>';
        try {
            const endpoint = `{{ route('api.variantes.activas') }}`;
            const res = await fetch(endpoint, { headers: { 'Accept': 'application/json' } });
            if (!res.ok) {
                const text = await res.text();
                console.error('Error HTTP cargando variantes:', res.status, text);
                throw new Error('HTTP '+res.status);
            }
            const data = await res.json();
            selVar.innerHTML = '<option value="">Seleccionar variante</option>';
            (data.variantes || []).forEach(v => {
                const opt = document.createElement('option');
                opt.value = v.idVariante; opt.textContent = v.nombre;
                selVar.appendChild(opt);
            });
        } catch (e) {
            selVar.innerHTML = '<option value="">Error cargando variantes</option>';
            console.error(e);
        }
    }

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

    async function setProductoPorVariante(idVariante) {
        const selProd = document.getElementById('idProducto');
        const opciones = Array.from(selProd.options).map(o => parseInt(o.value)).filter(v => !isNaN(v));
        selProd.value = '';
        try {
            const endpoint = `{{ route('api.variante.productos', ['idVariante' => 'VAR_ID']) }}`.replace('VAR_ID', encodeURIComponent(idVariante));
            const res = await fetch(endpoint, { headers: { 'Accept': 'application/json' } });
            if (!res.ok) {
                const text = await res.text();
                console.error('Error HTTP productos por variante:', res.status, text);
                return;
            }
            const data = await res.json();
            const productos = data.productos || [];
            if (productos.length > 0) {
                // Seleccionar el primer producto que exista en el select permitido (1-4)
                const candidato = productos.find(p => opciones.includes(parseInt(p.idProducto)));
                if (candidato) {
                    selProd.value = String(candidato.idProducto);
                }
            }
        } catch (e) {
            console.error(e);
        }
    }

    document.getElementById('idVariante').addEventListener('change', async (e) => {
        const idVar = e.target.value;
        document.getElementById('caracteristicasContainer').innerHTML = '';
        document.getElementById('opcionesContainer').style.display = 'none';
        if (!idVar) { document.getElementById('idProducto').value = ''; return; }
        await setProductoPorVariante(idVar);
        cargarCaracteristicas(idVar);
    });

    // Al cargar, traer todas las variantes
    (function initOnLoad(){
        cargarVariantesActivas();
    })();
    function toggleClientes() {
        const tipo = document.getElementById('tipoCliente').value;
        document.getElementById('wrapClienteNatural').style.display = (tipo === 'natural') ? '' : 'none';
        document.getElementById('wrapClienteEstablecimiento').style.display = (tipo === 'establecimiento') ? '' : 'none';
    }
    document.getElementById('tipoCliente').addEventListener('change', toggleClientes);
    toggleClientes();
</script>
@endpush
