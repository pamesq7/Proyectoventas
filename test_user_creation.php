<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\ClienteNatural;
use App\Models\ClienteEstablecimiento;
use App\Models\Empleado;

// Configurar Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== PRUEBA DE CREACIÓN DE USUARIOS ===\n\n";

try {
    // Probar creación de Cliente Natural
    echo "1. Probando Cliente Natural...\n";
    DB::beginTransaction();
    
    $userData = [
        'ci' => '12345678',
        'name' => 'Juan',
        'primerApellido' => 'Pérez',
        'segundApellido' => 'García',
        'email' => 'juan.test@example.com',
        'telefono' => '70123456',
        'password' => Hash::make('123456'),
        'estado' => 1,
    ];
    
    $user = User::create($userData);
    echo "   ✓ Usuario base creado con ID: {$user->idUser}\n";
    
    $clienteNatural = ClienteNatural::create([
        'idCliente' => $user->idUser,
        'nit' => '1234567890',
        'estado' => 1,
    ]);
    echo "   ✓ Cliente Natural creado\n";
    
    DB::commit();
    echo "   ✓ Cliente Natural: ÉXITO\n\n";
    
} catch (Exception $e) {
    DB::rollBack();
    echo "   ✗ Cliente Natural: ERROR - " . $e->getMessage() . "\n\n";
}

try {
    // Probar creación de Cliente Establecimiento
    echo "2. Probando Cliente Establecimiento...\n";
    DB::beginTransaction();
    
    $userData2 = [
        'ci' => '87654321',
        'name' => 'María',
        'primerApellido' => 'López',
        'segundApellido' => 'Martínez',
        'email' => 'maria.test@example.com',
        'telefono' => '70654321',
        'password' => Hash::make('123456'),
        'estado' => 1,
    ];
    
    $user2 = User::create($userData2);
    echo "   ✓ Usuario base creado con ID: {$user2->idUser}\n";
    
    $clienteEstablecimiento = ClienteEstablecimiento::create([
        'nit' => '9876543210',
        'razonSocial' => 'Empresa Test S.A.',
        'tipoEstablecimiento' => 'Empresa Privada',
        'domicilioFiscal' => 'Av. Test 123',
        'idRepresentante' => $user2->idUser,
        'estado' => 1,
    ]);
    echo "   ✓ Cliente Establecimiento creado\n";
    
    DB::commit();
    echo "   ✓ Cliente Establecimiento: ÉXITO\n\n";
    
} catch (Exception $e) {
    DB::rollBack();
    echo "   ✗ Cliente Establecimiento: ERROR - " . $e->getMessage() . "\n\n";
}

try {
    // Probar creación de Empleado
    echo "3. Probando Empleado...\n";
    DB::beginTransaction();
    
    $userData3 = [
        'ci' => '11223344',
        'name' => 'Carlos',
        'primerApellido' => 'Rodríguez',
        'segundApellido' => 'Silva',
        'email' => 'carlos.test@example.com',
        'telefono' => '70112233',
        'password' => Hash::make('123456'),
        'estado' => 1,
    ];
    
    $user3 = User::create($userData3);
    echo "   ✓ Usuario base creado con ID: {$user3->idUser}\n";
    
    $empleado = Empleado::create([
        'idEmpleado' => $user3->idUser,
        'cargo' => 'Vendedor',
        'rol' => 'vendedor',
        'estado' => 1,
    ]);
    echo "   ✓ Empleado creado\n";
    
    DB::commit();
    echo "   ✓ Empleado: ÉXITO\n\n";
    
} catch (Exception $e) {
    DB::rollBack();
    echo "   ✗ Empleado: ERROR - " . $e->getMessage() . "\n\n";
}

echo "=== FIN DE PRUEBAS ===\n";
