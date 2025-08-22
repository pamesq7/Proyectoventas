<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ClienteNaturalController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\OpcionController;
use App\Http\Controllers\CaracteristicaController;
use App\Http\Controllers\VarianteController;
use App\Http\Controllers\ClienteEstablecimientoController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\DisenoController;
use App\Http\Controllers\ConfiguracionController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Ruta principal
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Dashboard
Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

// Ruta temporal para debug
Route::post('/debug-user-creation', function(\Illuminate\Http\Request $request) {
    \Log::info('DEBUG: Datos recibidos en formulario:', $request->all());
    
    return response()->json([
        'success' => true,
        'message' => 'Datos recibidos correctamente',
        'data' => $request->except(['password', 'password_confirmation'])
    ]);
});

/*
|--------------------------------------------------------------------------
| Rutas de Gestión de Usuarios
|--------------------------------------------------------------------------
*/
Route::resource('users', UserController::class);

/*
|--------------------------------------------------------------------------
| Rutas de Gestión de Clientes
|--------------------------------------------------------------------------
*/
// Clientes Naturales
Route::resource('clienteNatural', ClienteNaturalController::class);
Route::get('clienteNatural/{clienteNatural}/estadisticas', [ClienteNaturalController::class, 'estadisticas'])
    ->name('clienteNatural.estadisticas');
Route::patch('clienteNatural/{clienteNatural}/toggle-estado', [ClienteNaturalController::class, 'toggleEstado'])
    ->name('clienteNatural.toggleEstado');

// Clientes Establecimientos
Route::resource('clienteEstablecimiento', ClienteEstablecimientoController::class);
Route::get('clienteEstablecimiento/{clienteEstablecimiento}/estadisticas', [ClienteEstablecimientoController::class, 'estadisticas'])
    ->name('clienteEstablecimiento.estadisticas');
Route::patch('clienteEstablecimiento/{clienteEstablecimiento}/toggle-estado', [ClienteEstablecimientoController::class, 'toggleEstado'])
    ->name('clienteEstablecimiento.toggleEstado');

/*
|--------------------------------------------------------------------------
| Rutas de Configuración Unificada
|--------------------------------------------------------------------------
*/
Route::get('configuracion', [ConfiguracionController::class, 'index'])->name('configuracion.index');

/*
|--------------------------------------------------------------------------
| Rutas de Configuración del Sistema
|--------------------------------------------------------------------------
*/
// Categorías de productos
Route::resource('categorias', CategoriaController::class);
Route::patch('categorias/{categoria}/toggle', [CategoriaController::class, 'toggleEstado'])->name('categorias.toggleEstado');

// Opciones de productos
Route::resource('opciones', OpcionController::class)->parameters(['opciones' => 'opcion']);
Route::patch('opciones/{opcion}/toggle-estado', [OpcionController::class, 'toggleEstado'])->name('opciones.toggleEstado');

// Características de productos
Route::resource('caracteristicas', CaracteristicaController::class)->parameters(['caracteristicas' => 'caracteristica']);
Route::get('api/opciones/{idOpcion}/caracteristicas', [CaracteristicaController::class, 'getByOpcion'])->name('api.caracteristicas.by-opcion');
Route::get('caracteristicas/por-opcion/{idOpcion}', [CaracteristicaController::class, 'getByOpcion'])->name('caracteristicas.por-opcion');

// Variantes de productos
Route::resource('variantes', VarianteController::class);

/*
|--------------------------------------------------------------------------
| Rutas de Gestión de Productos
|--------------------------------------------------------------------------
*/
Route::resource('productos', ProductoController::class);

// Rutas adicionales para gestión de variantes (Many-to-Many)
Route::post('productos/{producto}/variantes/attach', [ProductoController::class, 'attachVariante'])->name('productos.attachVariante');
Route::delete('productos/{producto}/variantes/{idVariante}/detach', [ProductoController::class, 'detachVariante'])->name('productos.detachVariante');
Route::put('productos/{producto}/variantes/{idVariante}/update-relation', [ProductoController::class, 'updateVarianteRelation'])->name('productos.updateVarianteRelation');
Route::get('productos/{producto}/variantes', [ProductoController::class, 'getProductoVariantes'])->name('productos.getVariantes');

// Rutas adicionales para gestión de variantes (legacy - mantener por compatibilidad)
Route::post('productos/variante', [ProductoController::class, 'storeVariante'])->name('productos.storeVariante');
Route::delete('productos/variante/{variante}', [ProductoController::class, 'deleteVariante'])->name('productos.deleteVariante');
Route::get('productos/caracteristicas/{opcion}', [ProductoController::class, 'getCaracteristicasByOpcion'])->name('productos.caracteristicasByOpcion');
Route::post('productos/{producto}/generar-variantes', [ProductoController::class, 'generarVariantesAutomaticas'])->name('productos.generarVariantesAutomaticas');

// Rutas para diseños
Route::resource('disenos', DisenoController::class);

/*
|--------------------------------------------------------------------------
| Rutas de Gestión de Pedidos
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\PedidoController;

// Rutas públicas del catálogo
Route::get('catalogo', [PedidoController::class, 'catalogo'])->name('pedidos.catalogo');
Route::get('producto/{idProducto}/configurar', [PedidoController::class, 'configurarProducto'])->name('pedidos.configurar');
Route::get('personalizar', [PedidoController::class, 'personalizarDiseno'])->name('pedidos.personalizar');
Route::post('personalizar/iniciar', [PedidoController::class, 'iniciarPedidoConDiseno'])->name('pedidos.personalizar.iniciar');

// Nuevo pedido en un solo formulario (usa diseño ya subido en sesión)
Route::get('pedidos/nuevo', [PedidoController::class, 'nuevoPedido'])->name('pedidos.nuevo');
Route::post('pedidos/guardar-nuevo', [PedidoController::class, 'guardarNuevoPedido'])->name('pedidos.guardar-nuevo');

// API para UI dinámica de variantes y características
Route::get('api/producto/{idProducto}/variantes', [PedidoController::class, 'apiVariantesPorProducto'])->name('api.variantes.producto');
Route::get('api/variante/{idVariante}/caracteristicas', [PedidoController::class, 'apiCaracteristicasDeVariante'])->name('api.variante.caracteristicas');
Route::get('api/variantes', [PedidoController::class, 'apiVariantesActivas'])->name('api.variantes.activas');
Route::get('api/variante/{idVariante}/productos', [PedidoController::class, 'apiProductosPorVariante'])->name('api.variante.productos');

// Rutas del carrito de compras
Route::post('carrito/agregar', [PedidoController::class, 'agregarAlCarrito'])->name('pedidos.agregar-carrito');
Route::get('carrito', [PedidoController::class, 'carrito'])->name('pedidos.carrito');
Route::delete('carrito/{itemId}', [PedidoController::class, 'eliminarDelCarrito'])->name('pedidos.eliminar-carrito');

// Rutas de checkout y procesamiento
Route::get('checkout', [PedidoController::class, 'checkout'])->name('pedidos.checkout');
Route::post('procesar-pedido', [PedidoController::class, 'procesarPedido'])->name('pedidos.procesar');
Route::get('pedido/{idVenta}/confirmacion', [PedidoController::class, 'confirmacion'])->name('pedidos.confirmacion');

// Rutas de administración de pedidos
Route::get('pedidos', [PedidoController::class, 'index'])->name('pedidos.index');
Route::get('pedidos/{idVenta}', [PedidoController::class, 'show'])->name('pedidos.show');
Route::patch('pedidos/{idVenta}/estado', [PedidoController::class, 'actualizarEstado'])->name('pedidos.actualizar-estado');
