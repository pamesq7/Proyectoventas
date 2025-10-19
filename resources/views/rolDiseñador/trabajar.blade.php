@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">🎨 Trabajar en Diseño</h1>
        <a href="{{ route('dashboard.disenador') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>

    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Error:</strong>
        <ul class="mb-0">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif

    <div class="row">
        <!-- Información del Pedido -->
        <div class="col-md-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-primary text-white">
                    <h6 class="m-0 font-weight-bold">📋 Información del Pedido</h6>
                </div>
                <div class="card-body">
                    @php
                        $venta = $diseno->detalleVenta->venta;
                        $nombreCliente = 'Cliente no especificado';
                        
                        // Obtener nombre del cliente natural
                        if ($venta->clienteNatural && $venta->clienteNatural->user) {
                            $user = $venta->clienteNatural->user;
                            $nombreCliente = trim($user->name . ' ' . ($user->primerApellido ?? '') . ' ' . ($user->segundApellido ?? ''));
                        }
                        // Si no hay cliente natural, obtener del establecimiento
                        elseif ($venta->clienteEstablecimiento) {
                            $nombreCliente = $venta->clienteEstablecimiento->razonSocial ?? 'Establecimiento';
                        }
                    @endphp
                    <p class="mb-2">
                        <strong>Pedido:</strong> #{{ $venta->idVenta }}
                    </p>
                    <p class="mb-2">
                        <strong>Cliente:</strong> <span class="badge badge-primary">{{ $nombreCliente }}</span>
                    </p>
                    <p class="mb-2">
                        <strong>Estado Actual:</strong>
                        <span class="badge badge-warning">{{ ucfirst($diseno->estadoDiseño) }}</span>
                    </p>
                    <p class="mb-2">
                        <strong>Fecha Creación:</strong> {{ $diseno->created_at->format('d/m/Y H:i') }}
                    </p>
                    <p class="mb-2">
                        <strong>Fecha de Entrega:</strong> {{ $venta->fechaEntrega->format('d/m/Y') }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Área de Trabajo -->
        <div class="col-md-8">
            <!-- Borrador Inicial -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-info text-white">
                    <h6 class="m-0 font-weight-bold">🖼️ Borrador Inicial (Referencia)</h6>
                </div>
                <div class="card-body d-flex justify-content-start align-items-center">
                    @if($diseno->archivo)
                    <div class="image-container" style="max-width: 200px;">
                        <img src="{{ asset('storage/' . $diseno->archivo) }}" alt="Borrador" class="img-fluid" style="max-height: 250px;">
                        <p class="mt-2 text-muted text-center">{{ basename($diseno->archivo) }}</p>
                    </div>
                    @else
                    <div class="alert alert-warning" role="alert">
                        <i class="fas fa-exclamation-triangle"></i> No hay archivo borrador disponible
                    </div>
                    @endif
                    <div class="ml-4">
                        <p><strong>Descripción:</strong> Este es el borrador del diseño que ha sido asignado. Asegúrate de hacer las modificaciones necesarias y subir el diseño terminado.</p>
                    </div>
                </div>
            </div>

            <!-- Formulario de Subida -->
            <div class="card shadow">
                <div class="card-header py-3 bg-success text-white">
                    <h6 class="m-0 font-weight-bold">✅ Subir Diseño Terminado</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('diseñador.subir', $diseno->idDiseno) }}" method="POST" enctype="multipart/form-data" id="formDiseño">
                        @csrf

                        <div class="form-group">
                            <label for="disenoTerminado" class="font-weight-bold">
                                <i class="fas fa-cloud-upload-alt"></i> Selecciona tu Diseño Terminado
                            </label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="disenoTerminado" name="disenoTerminado" accept=".jpg,.jpeg,.png,.pdf" required>
                                <label class="custom-file-label" for="disenoTerminado">Elige archivo...</label>
                            </div>
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle"></i> Formatos permitidos: JPG, PNG, PDF | Tamaño máximo: 5MB
                            </small>
                        </div>

                        <!-- Vista Previa -->
                        <div class="form-group" id="previewContainer" style="display: none;">
                            <label class="font-weight-bold">✨ Vista Previa del Nuevo Diseño</label>
                            <div class="border rounded p-3 text-center" style="background-color: #f8f9fa;">
                                <img id="previewImage" src="" alt="Vista previa" class="img-fluid" style="max-height: 300px;">
                                <p id="previewFileName" class="mt-2 text-muted"></p>
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-success btn-lg btn-block">
                                <i class="fas fa-check-circle"></i> Marcar como Terminado
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Vista previa de imagen
    document.getElementById('disenoTerminado').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(event) {
                document.getElementById('previewImage').src = event.target.result;
                document.getElementById('previewFileName').textContent = file.name;
                document.getElementById('previewContainer').style.display = 'block';
            };
            reader.readAsDataURL(file);

            // Actualizar label del input
            document.querySelector('.custom-file-label').textContent = file.name;
        }
    });

    // Confirmación antes de enviar
    document.getElementById('formDiseño').addEventListener('submit', function(e) {
        if (!confirm('¿Estás seguro de que deseas marcar este diseño como terminado? El pedido pasará automáticamente a producción.')) {
            e.preventDefault();
        }
    });
</script>

<style>
    .custom-file-label::after {
        content: "Examinar";
    }

    .image-container {
        width: 100%;
        max-width: 200px;
        margin-right: 20px;
    }

    @media (max-width: 768px) {
        .d-flex {
            flex-direction: column;
            align-items: center;
        }
        .ml-4 {
            margin-left: 0 !important;
            margin-top: 15px;
        }
    }
</style>
@endsection
