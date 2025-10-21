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
                    <i class="fas fa-file-upload me-2"></i>Sube tu diseño
                </div>
                <div class="card-body">
                    <form action="{{ route('pedidos.personalizar.iniciar') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label for="disenoPersonalizado" class="form-label">Archivo de diseño</label>
                            <input type="file" name="disenoPersonalizado" id="disenoPersonalizado" class="form-control" accept=".jpg,.jpeg,.png,.pdf" required>
                            <small class="text-muted">Formatos permitidos: JPG, PNG, PDF. Tamaño máx. 5MB.</small>
                        </div>

                        <div id="previewContainer" class="mt-3" style="display:none;">
                            <img id="previewImagen" class="img-thumbnail" style="max-height:200px; display:none;"/>
                            <div id="previewPdf" class="alert alert-info" style="display:none;"></div>
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-arrow-right me-2"></i>Hacer mi pedido
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
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const input = document.getElementById('disenoPersonalizado');
    const cont = document.getElementById('previewContainer');
    const img = document.getElementById('previewImagen');
    const pdf = document.getElementById('previewPdf');

    input.addEventListener('change', () => {
        const file = input.files[0];
        if (!file) { cont.style.display = 'none'; return; }
        cont.style.display = 'block';
        if (file.type.startsWith('image/')) {
            img.src = URL.createObjectURL(file);
            img.style.display = 'block';
            pdf.style.display = 'none';
        } else {
            img.style.display = 'none';
            pdf.style.display = 'block';
            pdf.innerHTML = `<i class="fas fa-file-pdf me-2"></i> Archivo PDF seleccionado: ${file.name}`;
        }
    });
});
</script>
@endpush
@endsection
