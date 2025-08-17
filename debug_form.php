<?php
// Archivo temporal para debuggear el formulario
if ($_POST) {
    echo "<h2>Datos recibidos del formulario:</h2>";
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";
    
    echo "<h2>Validaciones que fallarían:</h2>";
    
    // Simular validaciones
    $errors = [];
    
    if (empty($_POST['tipo_usuario'])) {
        $errors[] = "tipo_usuario es requerido";
    }
    
    if ($_POST['tipo_usuario'] === 'cliente_establecimiento') {
        if (empty($_POST['nit_establecimiento'])) $errors[] = "nit_establecimiento es requerido";
        if (empty($_POST['razonSocial'])) $errors[] = "razonSocial es requerido";
        if (empty($_POST['tipoEstablecimiento'])) $errors[] = "tipoEstablecimiento es requerido";
        if (empty($_POST['domicilioFiscal'])) $errors[] = "domicilioFiscal es requerido";
    }
    
    if ($_POST['tipo_usuario'] === 'empleado') {
        if (empty($_POST['cargo'])) $errors[] = "cargo es requerido";
        if (empty($_POST['rol'])) $errors[] = "rol es requerido";
    }
    
    if (empty($errors)) {
        echo "<p style='color: green;'>✓ Todas las validaciones pasarían</p>";
    } else {
        echo "<ul style='color: red;'>";
        foreach ($errors as $error) {
            echo "<li>✗ $error</li>";
        }
        echo "</ul>";
    }
    
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Debug Form</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input, select, textarea { width: 300px; padding: 8px; }
        button { padding: 10px 20px; background: #007bff; color: white; border: none; cursor: pointer; }
        .campos { display: none; border: 1px solid #ccc; padding: 15px; margin: 10px 0; }
    </style>
</head>
<body>
    <h1>Formulario de Debug - Creación de Usuario</h1>
    
    <form method="POST">
        <div class="form-group">
            <label>Tipo de Usuario:</label>
            <input type="radio" name="tipo_usuario" value="cliente_natural" onchange="toggleCampos()"> Cliente Natural
            <input type="radio" name="tipo_usuario" value="cliente_establecimiento" onchange="toggleCampos()"> Cliente Establecimiento
            <input type="radio" name="tipo_usuario" value="empleado" onchange="toggleCampos()"> Empleado
        </div>
        
        <div class="form-group">
            <label>CI:</label>
            <input type="text" name="ci" required>
        </div>
        
        <div class="form-group">
            <label>Nombre:</label>
            <input type="text" name="name" required>
        </div>
        
        <div class="form-group">
            <label>Primer Apellido:</label>
            <input type="text" name="primerApellido" required>
        </div>
        
        <div class="form-group">
            <label>Email:</label>
            <input type="email" name="email" required>
        </div>
        
        <div class="form-group">
            <label>Contraseña:</label>
            <input type="password" name="password" required>
        </div>
        
        <div class="form-group">
            <label>Confirmar Contraseña:</label>
            <input type="password" name="password_confirmation" required>
        </div>
        
        <!-- Campos Cliente Establecimiento -->
        <div id="campos_cliente_establecimiento" class="campos">
            <h3>Datos del Establecimiento</h3>
            <div class="form-group">
                <label>NIT Establecimiento:</label>
                <input type="text" name="nit_establecimiento">
            </div>
            <div class="form-group">
                <label>Razón Social:</label>
                <input type="text" name="razonSocial">
            </div>
            <div class="form-group">
                <label>Tipo Establecimiento:</label>
                <select name="tipoEstablecimiento">
                    <option value="">Seleccione</option>
                    <option value="Empresa Privada">Empresa Privada</option>
                    <option value="Institución Pública">Institución Pública</option>
                    <option value="ONG">ONG</option>
                    <option value="Cooperativa">Cooperativa</option>
                    <option value="Otro">Otro</option>
                </select>
            </div>
            <div class="form-group">
                <label>Domicilio Fiscal:</label>
                <textarea name="domicilioFiscal"></textarea>
            </div>
        </div>
        
        <!-- Campos Empleado -->
        <div id="campos_empleado" class="campos">
            <h3>Datos del Empleado</h3>
            <div class="form-group">
                <label>Cargo:</label>
                <input type="text" name="cargo">
            </div>
            <div class="form-group">
                <label>Rol:</label>
                <select name="rol">
                    <option value="">Seleccione</option>
                    <option value="administrador">Administrador</option>
                    <option value="diseñador">Diseñador</option>
                    <option value="operador">Operador</option>
                    <option value="cliente">Cliente</option>
                    <option value="vendedor">Vendedor</option>
                </select>
            </div>
        </div>
        
        <!-- Campos Cliente Natural -->
        <div id="campos_cliente_natural" class="campos">
            <h3>Datos del Cliente Natural</h3>
            <div class="form-group">
                <label>NIT Cliente:</label>
                <input type="text" name="nit_cliente">
            </div>
        </div>
        
        <button type="submit">Enviar y Debuggear</button>
    </form>
    
    <script>
        function toggleCampos() {
            // Ocultar todos los campos
            document.getElementById('campos_cliente_natural').style.display = 'none';
            document.getElementById('campos_cliente_establecimiento').style.display = 'none';
            document.getElementById('campos_empleado').style.display = 'none';
            
            // Mostrar campos según selección
            const tipo = document.querySelector('input[name="tipo_usuario"]:checked');
            if (tipo) {
                document.getElementById('campos_' + tipo.value).style.display = 'block';
            }
        }
    </script>
</body>
</html>
