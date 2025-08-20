@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex align-items-center justify-content-between mt-4">
        <h1 class="mb-0">
            <i class="fas fa-magic me-2"></i>Personalizar mi diseño
        </h1>
        <a href="{{ route('pedidos.catalogo') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Volver al catálogo
        </a>
    </div>

    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('pedidos.catalogo') }}">Catálogo</a></li>
        <li class="breadcrumb-item active">Personalizar</li>
    </ol>

    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-file-upload me-2"></i>Datos de personalización
                </div>
                <div class="card-body">
                    <form action="{{ route('pedidos.agregar-carrito') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="idProducto" class="form-label">Producto base</label>
                                <select name="idProducto" id="idProducto" class="form-select" required>
                                    <option value="" selected disabled>Seleccione un tipo de prenda</option>
                                    @foreach($productosBase as $p)
                                        <option value="{{ $p->idProducto }}">{{ $p->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label for="idTalla" class="form-label">Talla</label>
                                <select name="idTalla" id="idTalla" class="form-select" required>
                                    <option value="" selected disabled>Seleccione</option>
                                    @foreach($tallas as $t)
                                        <option value="{{ $t->idTalla }}">{{ $t->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label for="cantidad" class="form-label">Cantidad</label>
                                <input type="number" min="1" value="1" name="cantidad" id="cantidad" class="form-control" required>
                            </div>

                            <div class="col-md-6">
                                <label for="nombrePersonalizado" class="form-label">Nombre (opcional)</label>
                                <input type="text" maxlength="50" name="nombrePersonalizado" id="nombrePersonalizado" class="form-control" placeholder="Ej: P. Gómez">
                            </div>

                            <div class="col-md-6">
                                <label for="numeroPersonalizado" class="form-label">Número (opcional)</label>
                                <input type="text" maxlength="10" name="numeroPersonalizado" id="numeroPersonalizado" class="form-control" placeholder="Ej: 10">
                            </div>

                            <div class="col-12">
                                <label for="textoAdicional" class="form-label">Texto adicional (opcional)</label>
                                <textarea name="textoAdicional" id="textoAdicional" rows="2" class="form-control" placeholder="Frase o mensaje adicional (máx. 200 caracteres)"></textarea>
                            </div>

                            <div class="col-12">
                                <label for="disenoPersonalizado" class="form-label">Archivo de diseño</label>
                                <input type="file" name="disenoPersonalizado" id="disenoPersonalizado" class="form-control" accept=".jpg,.jpeg,.png,.pdf" required>
                                <small class="text-muted">Formatos permitidos: JPG, PNG, PDF. Tamaño máx. 5MB.</small>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-cart-plus me-2"></i>Agregar al carrito
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-info-circle me-2"></i>Consejos
                </div>
                <div class="card-body">
                    <ul class="mb-0">
                        <li>Sube imágenes nítidas y en alta resolución.</li>
                        <li>Si es PDF, asegúrate que incluya curvas y fuentes incrustadas.</li>
                        <li>Indica nombre/número si deseas personalización individual.</li>
                        <li>Podrás revisar todo en el carrito antes de confirmar.</li>
                    </ul>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <i class="fas fa-shopping-cart me-2"></i>Carrito
                </div>
                <div class="card-body">
                    <a href="{{ route('pedidos.carrito') }}" class="btn btn-outline-primary w-100">
                        Ver carrito
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
