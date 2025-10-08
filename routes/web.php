<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
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
use App\Http\Controllers\VentaController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\EmpleadoController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\PagoController;

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

// Ruta de inicio
Route::get('/', [HomeController::class, 'index'])->name('home');

// Rutas de autenticación
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
// Rutas de autenticación (FUERA del middleware auth)
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Rutas protegidas por autenticación
// Rutas protegidas
Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboards específicos - SIN middleware role
    Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');
    Route::get('/admin/dashboard', [HomeController::class, 'adminDashboard'])
        ->name('dashboard.admin')
        ->middleware('auth');
    Route::get('/disenador/dashboard', [HomeController::class, 'disenadorDashboard'])
        ->name('dashboard.disenador')
        ->middleware('auth');
    Route::get('/vendedor/dashboard', [HomeController::class, 'vendedorDashboard'])
        ->name('dashboard.vendedor')
        ->middleware('auth');
    Route::get('/operador/dashboard', [HomeController::class, 'operadorDashboard'])
        ->name('dashboard.operador')
        ->middleware('auth');
    Route::get('/cliente/dashboard', [HomeController::class, 'clienteDashboard'])
        ->name('dashboard.cliente')
        ->middleware('auth');
/*
   |--------------------------------------------------------------------------
   | Rutas de Gestión de Ventas
   |--------------------------------------------------------------------------
   */
// Gestión principal de ventas
Route::get('ventas', [VentaController::class, 'index'])->name('ventas.index');
Route::get('ventas/create', [VentaController::class, 'create'])->name('ventas.create');
Route::get('ventas/morosos', [VentaController::class, 'clientesMorosos'])->name('ventas.morosos');
Route::get('ventas/dashboard', [VentaController::class, 'dashboard'])->name('ventas.dashboard');
Route::post('ventas', [VentaController::class, 'store'])->name('ventas.store');
Route::get('ventas/{id}', [VentaController::class, 'show'])->name('ventas.show');

// Gestión de pagos individuales
Route::get('pagos/{id}/edit', [PagoController::class, 'editPago'])->name('pagos.edit');
Route::put('pagos/{id}', [PagoController::class, 'updatePago'])->name('pagos.update');
Route::get('pagos', [PagoController::class, 'index'])->name('pagos.index');

// Actualizar estado de pedido
Route::post('ventas/{id}/estado', [VentaController::class, 'actualizarEstado'])->name('ventas.actualizar-estado');

/*
   |--------------------------------------------------------------------------
   | Rutas de Reportes
   |--------------------------------------------------------------------------
   */
Route::get('reportes', [ReporteController::class, 'index'])->name('reportes.index');

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
    

// Exportación de Diseños
Route::get('/export/disenos/pdf', [ExportController::class, 'exportarDisenos'])->name('export.disenos.pdf');

// Exportación de Pedidos
Route::get('/export/pedidos/pdf', [ExportController::class, 'exportarPedidos'])->name('export.pedidos.pdf');

/*
   |--------------------------------------------------------------------------
   | Rutas de Gestión de Pedidos
   |--------------------------------------------------------------------------
   */
// Rutas públicas del catálogo
Route::get('catalogo', [PedidoController::class, 'catalogo'])->name('pedidos.catalogo');
    Route::get('catalogo/consulta', [PedidoController::class, 'catalogo'])->name('catalogo.consulta');
Route::get('producto/{idProducto}/configurar', [PedidoController::class, 'configurarProducto'])->name('pedidos.configurar');
Route::get('personalizar', [PedidoController::class, 'personalizarDiseno'])->name('pedidos.personalizar');
Route::post('personalizar/iniciar', [PedidoController::class, 'iniciarPedidoConDiseno'])->name('pedidos.personalizar.iniciar');

// Nuevo pedido en un solo formulario (usa diseño ya subido en sesión)
Route::get('pedidos/nuevo', [PedidoController::class, 'nuevoPedido'])->name('pedidos.nuevo');
Route::post('pedidos/guardar-nuevo', [PedidoController::class, 'guardarNuevoPedido'])->name('pedidos.guardar-nuevo');

// API para UI dinámica de variantes y características
Route::get('api/producto/{idProducto}/variantes', [PedidoController::class, 'apiVariantesPorProducto'])->name('api.variantes.producto');
Route::get('api/producto/{idProducto}/opciones', [PedidoController::class, 'apiOpcionesPorProducto'])->name('api.opciones.producto');
Route::get('api/variante/{idVariante}/caracteristicas', [PedidoController::class, 'apiCaracteristicasDeVariante'])->name('api.variante.caracteristicas');
Route::get('api/variantes', [PedidoController::class, 'apiVariantesActivas'])->name('api.variantes.activas');
Route::get('api/variante/{idVariante}/productos', [PedidoController::class, 'apiProductosPorVariante'])->name('api.variante.productos');
Route::get('api/producto/{idProducto}/tallas-precios', [PedidoController::class, 'apiTallasPreciosPorProducto'])->name('api.producto.tallas-precios');
Route::get('api/clientes/search', [PedidoController::class, 'apiBuscarClientes'])->name('api.clientes.search');

// Rutas del carrito de compras
Route::post('carrito/agregar', [PedidoController::class, 'agregarAlCarrito'])->name('pedidos.agregar-carrito');
Route::get('carrito', [PedidoController::class, 'carrito'])->name('pedidos.carrito');
Route::delete('carrito/{itemId}', [PedidoController::class, 'eliminarDelCarrito'])->name('pedidos.eliminar-carrito');

// Rutas de checkout y procesamiento
Route::get('checkout', [PedidoController::class, 'checkout'])->name('pedidos.checkout');
Route::post('procesar-pedido', [PedidoController::class, 'procesarPedido'])->name('pedidos.procesar');
Route::get('pedido/{idVenta}/confirmacion', [PedidoController::class, 'confirmacion'])->name('pedidos.confirmacion');
Route::post('pedidos/{idVenta}/detalle', [PedidoController::class, 'agregarDetalle'])->name('pedidos.detalle.agregar');
// Registro de pagos para una venta (sin modificar base de datos)
Route::post('pedido/{idVenta}/pagos', [PedidoController::class, 'registrarPago'])->name('pedidos.registrar-pago');

// Rutas de administración de pedidos
Route::get('pedidos', [PedidoController::class, 'index'])->name('pedidos.index');
Route::get('pedidos/{idVenta}', [PedidoController::class, 'show'])->name('pedidos.show');
Route::patch('pedidos/{idVenta}/estado', [PedidoController::class, 'actualizarEstado'])->name('pedidos.actualizar-estado');
Route::get('pedidos/{idVenta}/edit', [PedidoController::class, 'edit'])->name('pedidos.edit');
Route::put('pedidos/{idVenta}', [PedidoController::class, 'update'])->name('pedidos.update');
Route::put('pedidos/{idVenta}/detalles', [PedidoController::class, 'updateDetalles'])->name('pedidos.update-detalles');
Route::delete('pedidos/{idVenta}', [PedidoController::class, 'destroy'])->name('pedidos.destroy');

/*
   |--------------------------------------------------------------------------
   | Rutas de Gestión de Empleados
   |--------------------------------------------------------------------------
   */
Route::resource('empleados', EmpleadoController::class);
Route::patch('/empleados/{empleado}/toggle-estado', [EmpleadoController::class, 'toggleEstado'])->name('empleados.toggleEstado');
Route::get('/empleados/{empleado}/estadisticas', [EmpleadoController::class, 'estadisticas'])->name('empleados.estadisticas');

/*
   |--------------------------------------------------------------------------
   | Rutas de Exportación PDF
   |--------------------------------------------------------------------------
   */
// Exportación de Usuarios
Route::get('/export/usuarios/pdf', [ExportController::class, 'exportarUsuarios'])->name('export.usuarios.pdf');

// Exportación de Empleados
Route::get('/export/empleados/pdf', [ExportController::class, 'exportarEmpleados'])->name('export.empleados.pdf');

// Exportación de Clientes
Route::get('/export/clientes-naturales/pdf', [ExportController::class, 'exportarClientesNaturales'])->name('export.clientes-naturales.pdf');
Route::get('/export/clientes-establecimientos/pdf', [ExportController::class, 'exportarClientesEstablecimientos'])->name('export.clientes-establecimientos.pdf');
Route::get('/export/clientes-consolidado/pdf', [ExportController::class, 'exportarClientesConsolidado'])->name('export.clientes-consolidado.pdf');

// Exportación de Productos
Route::get('/export/productos/pdf', [ExportController::class, 'exportarProductos'])->name('export.productos.pdf');
}); 

// Rutas públicas que no requieren autenticación
Route::get('/about', function () {
return view('about');
})->name('about');


// Ruta temporal para prueba
Route::get('/test-disenador', function () {
    return "Ruta de prueba del diseñador funciona";
})->name('test.disenador');
