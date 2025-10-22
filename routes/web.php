<?php

use Illuminate\Support\Facades\Route;
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
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DisenadorController;
use App\Http\Controllers\VendedorController;
use App\Http\Controllers\OperadorController;
use App\Http\Controllers\ClienteDashboardController;


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

// =========================================================================
// RUTAS PÚBLICAS
// =========================================================================

// Ruta de inicio (única definición)
Route::get('/', [HomeController::class, 'index'])->name('home');

// Rutas de autenticación
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);



// =========================================================================
// RUTAS PROTEGIDAS (REQUIEREN AUTENTICACIÓN)
// =========================================================================
Route::middleware('auth')->group(function () {
    // Logout disponible para TODOS los usuarios autenticados
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/admin/dashboard', [HomeController::class, 'adminDashboard'])->name('dashboard.admin');
    Route::get('/vendedor/dashboard', [HomeController::class, 'vendedorDashboard'])->name('dashboard.vendedor');
    Route::get('/disenador/dashboard', [HomeController::class, 'disenadorDashboard'])->name('dashboard.disenador');
    Route::get('/operador/dashboard', [HomeController::class, 'operadorDashboard'])->name('dashboard.operador');
    Route::get('/cliente/dashboard', [HomeController::class, 'clienteDashboard'])->name('dashboard.cliente');
});

Route::middleware(['auth', 'role:administrador,vendedor'])->group(function () {

    // Dashboard principal
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    /*
     |--------------------------------------------------------------------------
     | Gestión de Ventas
     |--------------------------------------------------------------------------
     */
    Route::get('ventas', [VentaController::class, 'index'])->name('ventas.index');
    Route::get('ventas/create', [VentaController::class, 'create'])->name('ventas.create');
    Route::get('ventas/morosos', [VentaController::class, 'clientesMorosos'])->name('ventas.morosos');
    Route::get('ventas/dashboard', [VentaController::class, 'dashboard'])->name('ventas.dashboard');
    Route::post('ventas', [VentaController::class, 'store'])->name('ventas.store');
    Route::get('ventas/{id}', [VentaController::class, 'show'])->name('ventas.show');
    Route::post('ventas/{id}/estado', [VentaController::class, 'actualizarEstado'])->name('ventas.actualizar-estado');

    // Gestión de pagos
    Route::get('pagos', [PagoController::class, 'index'])->name('pagos.index');
    Route::get('pagos/{id}/edit', [PagoController::class, 'editPago'])->name('pagos.edit');
    Route::put('pagos/{id}', [PagoController::class, 'updatePago'])->name('pagos.update');

    /*
     |--------------------------------------------------------------------------
     | Reportes
     |--------------------------------------------------------------------------
     */
    Route::get('reportes', [ReporteController::class, 'index'])->name('reportes.index');
    Route::prefix('reportes')->group(function () {
        Route::get('/ventas-mensuales', [ReporteController::class, 'ventasMensuales'])->name('reportes.ventas-mensuales');
        Route::get('/ventas-rango', [ReporteController::class, 'ventasRangoFechas'])->name('reportes.ventas-rango');
        Route::get('/comparativa-anual', [ReporteController::class, 'comparativaAnual'])->name('reportes.comparativa-anual');
    });



    /*
     |--------------------------------------------------------------------------
     | Gestión de Usuarios
     |--------------------------------------------------------------------------
     */
    Route::resource('users', UserController::class);

    /*
     |--------------------------------------------------------------------------
     | Gestión de Clientes
     |--------------------------------------------------------------------------
     */
    // Clientes Naturales
    Route::resource('clienteNatural', ClienteNaturalController::class);
    Route::get('clienteNatural/{clienteNatural}/estadisticas', [ClienteNaturalController::class, 'estadisticas'])->name('clienteNatural.estadisticas');
    Route::patch('clienteNatural/{clienteNatural}/toggle-estado', [ClienteNaturalController::class, 'toggleEstado'])->name('clienteNatural.toggleEstado');

    // Clientes Establecimientos
    Route::resource('clienteEstablecimiento', ClienteEstablecimientoController::class);
    Route::get('clienteEstablecimiento/{clienteEstablecimiento}/estadisticas', [ClienteEstablecimientoController::class, 'estadisticas'])->name('clienteEstablecimiento.estadisticas');
    Route::patch('clienteEstablecimiento/{clienteEstablecimiento}/toggle-estado', [ClienteEstablecimientoController::class, 'toggleEstado'])->name('clienteEstablecimiento.toggleEstado');

    /*
     |--------------------------------------------------------------------------
     | Configuración
     |--------------------------------------------------------------------------
     */
    Route::get('configuracion', [ConfiguracionController::class, 'index'])->name('configuracion.index');

    /*
     |--------------------------------------------------------------------------
     | Gestión de Productos y Sistema
     |--------------------------------------------------------------------------
     */
    // Categorías
    Route::resource('categorias', CategoriaController::class);
    Route::patch('categorias/{categoria}/toggle', [CategoriaController::class, 'toggleEstado'])->name('categorias.toggleEstado');
    Route::delete('/productos/{producto}/eliminar-imagen', [ProductoController::class, 'eliminarImagen'])->name('productos.eliminar-imagen');


    // Opciones
    Route::resource('opciones', OpcionController::class)->parameters(['opciones' => 'opcion']);
    Route::patch('opciones/{opcion}/toggle-estado', [OpcionController::class, 'toggleEstado'])->name('opciones.toggleEstado');

    // Características
    Route::resource('caracteristicas', CaracteristicaController::class)->parameters(['caracteristicas' => 'caracteristica']);
    Route::get('api/opciones/{idOpcion}/caracteristicas', [CaracteristicaController::class, 'getByOpcion'])->name('api.caracteristicas.by-opcion');
    Route::get('caracteristicas/por-opcion/{idOpcion}', [CaracteristicaController::class, 'getByOpcion'])->name('caracteristicas.por-opcion');

    // Variantes
    Route::resource('variantes', VarianteController::class);

    // Productos
    Route::resource('productos', ProductoController::class);
    Route::post('productos/{producto}/variantes/attach', [ProductoController::class, 'attachVariante'])->name('productos.attachVariante');
    Route::delete('productos/{producto}/variantes/{idVariante}/detach', [ProductoController::class, 'detachVariante'])->name('productos.detachVariante');
    Route::put('productos/{producto}/variantes/{idVariante}/update-relation', [ProductoController::class, 'updateVarianteRelation'])->name('productos.updateVarianteRelation');
    Route::get('productos/{producto}/variantes', [ProductoController::class, 'getProductoVariantes'])->name('productos.getVariantes');
    Route::post('productos/{producto}/generar-variantes', [ProductoController::class, 'generarVariantesAutomaticas'])->name('productos.generarVariantesAutomaticas');
    Route::post('productos/variante', [ProductoController::class, 'storeVariante'])->name('productos.storeVariante');
    Route::delete('productos/variante/{variante}', [ProductoController::class, 'deleteVariante'])->name('productos.deleteVariante');
    Route::get('productos/caracteristicas/{opcion}', [ProductoController::class, 'getCaracteristicasByOpcion'])->name('productos.caracteristicasByOpcion');

    // Diseños
    Route::resource('disenos', DisenoController::class);

    // Exportación de Diseños
    Route::get('/export/disenos/pdf', [ExportController::class, 'exportarDisenos'])->name('export.disenos.pdf');

    // Exportación de Pedidos
    Route::get('/export/pedidos/pdf', [ExportController::class, 'exportarPedidos'])->name('export.pedidos.pdf');

    /*
     |--------------------------------------------------------------------------
     | Gestión de Pedidos
     |--------------------------------------------------------------------------
     */
    // Catálogo y configuración
    Route::get('catalogo', [PedidoController::class, 'catalogo'])->name('pedidos.catalogo');
    Route::get('producto/{idProducto}/configurar', [PedidoController::class, 'configurarProducto'])->name('pedidos.configurar');
    Route::get('personalizar', [PedidoController::class, 'personalizarDiseno'])->name('pedidos.personalizar');
    Route::post('personalizar/iniciar', [PedidoController::class, 'iniciarPedidoConDiseno'])->name('pedidos.personalizar.iniciar');

    // Nuevo pedido con asignación de diseñador
    Route::get('pedidos/create', [PedidoController::class, 'create'])->name('pedidos.create');
    Route::get('pedidos/nuevo', [PedidoController::class, 'nuevoPedido'])->name('pedidos.nuevo');
    Route::post('pedidos/guardar-nuevo', [PedidoController::class, 'guardarNuevoPedido'])->name('pedidos.guardar-nuevo');

    // APIs para UI dinámica
    Route::get('api/producto/{idProducto}/variantes', [PedidoController::class, 'apiVariantesPorProducto'])->name('api.variantes.producto');
    Route::get('api/producto/{idProducto}/opciones', [PedidoController::class, 'apiOpcionesPorProducto'])->name('api.opciones.producto');
    Route::get('api/variante/{idVariante}/caracteristicas', [PedidoController::class, 'apiCaracteristicasDeVariante'])->name('api.variante.caracteristicas');
    Route::get('api/variantes', [PedidoController::class, 'apiVariantesActivas'])->name('api.variantes.activas');
    Route::get('api/variante/{idVariante}/productos', [PedidoController::class, 'apiProductosPorVariante'])->name('api.variante.productos');
    Route::get('api/producto/{idProducto}/tallas-precios', [PedidoController::class, 'apiTallasPreciosPorProducto'])->name('api.producto.tallas-precios');
    Route::get('api/clientes/search', [PedidoController::class, 'apiBuscarClientes'])->name('api.clientes.search');

    // Carrito y checkout
    Route::post('carrito/agregar', [PedidoController::class, 'agregarAlCarrito'])->name('pedidos.agregar-carrito');
    Route::get('carrito', [PedidoController::class, 'carrito'])->name('pedidos.carrito');
    Route::delete('carrito/{itemId}', [PedidoController::class, 'eliminarDelCarrito'])->name('pedidos.eliminar-carrito');
    Route::get('checkout', [PedidoController::class, 'checkout'])->name('pedidos.checkout');
    Route::post('procesar-pedido', [PedidoController::class, 'procesarPedido'])->name('pedidos.procesar');
    Route::get('pedido/{idVenta}/confirmacion', [PedidoController::class, 'confirmacion'])->name('pedidos.confirmacion');
    Route::post('pedidos/{idVenta}/detalle', [PedidoController::class, 'agregarDetalle'])->name('pedidos.detalle.agregar');
    Route::post('pedido/{idVenta}/pagos', [PedidoController::class, 'registrarPago'])->name('pedidos.registrar-pago');

    // Administración de pedidos
    Route::get('pedidos', [PedidoController::class, 'index'])->name('pedidos.index');
    Route::get('pedidos/{idVenta}', [PedidoController::class, 'show'])->name('pedidos.show');
    Route::get('pedidos/{idVenta}/edit', [PedidoController::class, 'edit'])->name('pedidos.edit');
    Route::put('pedidos/{idVenta}', [PedidoController::class, 'update'])->name('pedidos.update');
    Route::patch('pedidos/{idVenta}/estado', [PedidoController::class, 'actualizarEstado'])->name('pedidos.actualizar-estado');
    Route::put('pedidos/{idVenta}/detalles', [PedidoController::class, 'updateDetalles'])->name('pedidos.update-detalles');
    Route::delete('pedidos/{idVenta}', [PedidoController::class, 'destroy'])->name('pedidos.destroy');
    Route::delete('pedidos/{idVenta}/eliminar-imagen', [PedidoController::class, 'eliminarImagen'])->name('pedidos.eliminar-imagen');

    /*
     |--------------------------------------------------------------------------
     | Gestión de Empleados
     |--------------------------------------------------------------------------
     */
    Route::resource('empleados', EmpleadoController::class);
    Route::patch('/empleados/{empleado}/toggle-estado', [EmpleadoController::class, 'toggleEstado'])->name('empleados.toggleEstado');
    Route::get('/empleados/{empleado}/estadisticas', [EmpleadoController::class, 'estadisticas'])->name('empleados.estadisticas');

    /*
     |--------------------------------------------------------------------------
     | Exportación PDF
     |--------------------------------------------------------------------------
     */
    Route::get('/export/usuarios/pdf', [ExportController::class, 'exportarUsuarios'])->name('export.usuarios.pdf');
    Route::get('/export/empleados/pdf', [ExportController::class, 'exportarEmpleados'])->name('export.empleados.pdf');
    Route::get('/export/clientes-naturales/pdf', [ExportController::class, 'exportarClientesNaturales'])->name('export.clientes-naturales.pdf');
    Route::get('/export/clientes-establecimientos/pdf', [ExportController::class, 'exportarClientesEstablecimientos'])->name('export.clientes-establecimientos.pdf');
    Route::get('/export/clientes-consolidado/pdf', [ExportController::class, 'exportarClientesConsolidado'])->name('export.clientes-consolidado.pdf');
    Route::get('/export/productos/pdf', [ExportController::class, 'exportarProductos'])->name('export.productos.pdf');
});

// =========================================================================
// RUTAS PÚBLICAS ADICIONALES
// =========================================================================
Route::get('/about', function () {
    return view('about');
})->name('about');




// =========================================================================
// ROL VENDEDOR - GESTIÓN COMPLETA
// =========================================================================
Route::middleware('auth')->group(function () {
    // Dashboard principal
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    /*
     |--------------------------------------------------------------------------
     | Gestión de Ventas
     |--------------------------------------------------------------------------
     */
    Route::get('ventas', [VentaController::class, 'index'])->name('ventas.index');
    Route::get('ventas/create', [VentaController::class, 'create'])->name('ventas.create');
    Route::get('ventas/morosos', [VentaController::class, 'clientesMorosos'])->name('ventas.morosos');
    Route::get('ventas/dashboard', [VentaController::class, 'dashboard'])->name('ventas.dashboard');
    Route::post('ventas', [VentaController::class, 'store'])->name('ventas.store');
    Route::get('ventas/{id}', [VentaController::class, 'show'])->name('ventas.show');
    Route::post('ventas/{id}/estado', [VentaController::class, 'actualizarEstado'])->name('ventas.actualizar-estado');

    // Gestión de pagos
    Route::get('pagos', [PagoController::class, 'index'])->name('pagos.index');
    Route::get('pagos/{id}/edit', [PagoController::class, 'editPago'])->name('pagos.edit');
    Route::put('pagos/{id}', [PagoController::class, 'updatePago'])->name('pagos.update');

    /*
     |--------------------------------------------------------------------------
     | Reportes
     |--------------------------------------------------------------------------
     */
    Route::get('reportes', [ReporteController::class, 'index'])->name('reportes.index');
    Route::prefix('reportes')->group(function () {
        Route::get('/ventas-mensuales', [ReporteController::class, 'ventasMensuales'])->name('reportes.ventas-mensuales');
        Route::get('/ventas-rango', [ReporteController::class, 'ventasRangoFechas'])->name('reportes.ventas-rango');
        Route::get('/comparativa-anual', [ReporteController::class, 'comparativaAnual'])->name('reportes.comparativa-anual');
    });



    /*
     |--------------------------------------------------------------------------
     | Gestión de Usuarios
     |--------------------------------------------------------------------------
     */
    Route::resource('users', UserController::class);

    /*
     |--------------------------------------------------------------------------
     | Gestión de Clientes
     |--------------------------------------------------------------------------
     */
    // Clientes Naturales
    Route::resource('clienteNatural', ClienteNaturalController::class);
    Route::get('clienteNatural/{clienteNatural}/estadisticas', [ClienteNaturalController::class, 'estadisticas'])->name('clienteNatural.estadisticas');
    Route::patch('clienteNatural/{clienteNatural}/toggle-estado', [ClienteNaturalController::class, 'toggleEstado'])->name('clienteNatural.toggleEstado');

    // Clientes Establecimientos
    Route::resource('clienteEstablecimiento', ClienteEstablecimientoController::class);
    Route::get('clienteEstablecimiento/{clienteEstablecimiento}/estadisticas', [ClienteEstablecimientoController::class, 'estadisticas'])->name('clienteEstablecimiento.estadisticas');
    Route::patch('clienteEstablecimiento/{clienteEstablecimiento}/toggle-estado', [ClienteEstablecimientoController::class, 'toggleEstado'])->name('clienteEstablecimiento.toggleEstado');

    /*
     |--------------------------------------------------------------------------
     | Configuración
     |--------------------------------------------------------------------------
     */
    Route::get('configuracion', [ConfiguracionController::class, 'index'])->name('configuracion.index');

    /*
     |--------------------------------------------------------------------------
     | Gestión de Productos y Sistema
     |--------------------------------------------------------------------------
     */
    // Categorías
    Route::resource('categorias', CategoriaController::class);
    Route::patch('categorias/{categoria}/toggle', [CategoriaController::class, 'toggleEstado'])->name('categorias.toggleEstado');
    Route::delete('/productos/{producto}/eliminar-imagen', [ProductoController::class, 'eliminarImagen'])->name('productos.eliminar-imagen');


    // Opciones
    Route::resource('opciones', OpcionController::class)->parameters(['opciones' => 'opcion']);
    Route::patch('opciones/{opcion}/toggle-estado', [OpcionController::class, 'toggleEstado'])->name('opciones.toggleEstado');

    // Características
    Route::resource('caracteristicas', CaracteristicaController::class)->parameters(['caracteristicas' => 'caracteristica']);
    Route::get('api/opciones/{idOpcion}/caracteristicas', [CaracteristicaController::class, 'getByOpcion'])->name('api.caracteristicas.by-opcion');
    Route::get('caracteristicas/por-opcion/{idOpcion}', [CaracteristicaController::class, 'getByOpcion'])->name('caracteristicas.por-opcion');

    // Variantes
    Route::resource('variantes', VarianteController::class);

    // Productos
    Route::resource('productos', ProductoController::class);
    Route::post('productos/{producto}/variantes/attach', [ProductoController::class, 'attachVariante'])->name('productos.attachVariante');
    Route::delete('productos/{producto}/variantes/{idVariante}/detach', [ProductoController::class, 'detachVariante'])->name('productos.detachVariante');
    Route::put('productos/{producto}/variantes/{idVariante}/update-relation', [ProductoController::class, 'updateVarianteRelation'])->name('productos.updateVarianteRelation');
    Route::get('productos/{producto}/variantes', [ProductoController::class, 'getProductoVariantes'])->name('productos.getVariantes');
    Route::post('productos/{producto}/generar-variantes', [ProductoController::class, 'generarVariantesAutomaticas'])->name('productos.generarVariantesAutomaticas');
    Route::post('productos/variante', [ProductoController::class, 'storeVariante'])->name('productos.storeVariante');
    Route::delete('productos/variante/{variante}', [ProductoController::class, 'deleteVariante'])->name('productos.deleteVariante');
    Route::get('productos/caracteristicas/{opcion}', [ProductoController::class, 'getCaracteristicasByOpcion'])->name('productos.caracteristicasByOpcion');

    // Diseños
    Route::resource('disenos', DisenoController::class);

    // Exportación de Diseños
    Route::get('/export/disenos/pdf', [ExportController::class, 'exportarDisenos'])->name('export.disenos.pdf');

    // Exportación de Pedidos
    Route::get('/export/pedidos/pdf', [ExportController::class, 'exportarPedidos'])->name('export.pedidos.pdf');

    /*
     |--------------------------------------------------------------------------
     | Gestión de Pedidos
     |--------------------------------------------------------------------------
     */
    // Catálogo y configuración
    Route::get('catalogo', [PedidoController::class, 'catalogo'])->name('pedidos.catalogo');
    Route::get('producto/{idProducto}/configurar', [PedidoController::class, 'configurarProducto'])->name('pedidos.configurar');
    Route::get('personalizar', [PedidoController::class, 'personalizarDiseno'])->name('pedidos.personalizar');
    Route::post('personalizar/iniciar', [PedidoController::class, 'iniciarPedidoConDiseno'])->name('pedidos.personalizar.iniciar');

    // Nuevo pedido con asignación de diseñador
    Route::get('pedidos/create', [PedidoController::class, 'create'])->name('pedidos.create');
    Route::get('pedidos/nuevo', [PedidoController::class, 'nuevoPedido'])->name('pedidos.nuevo');
    Route::post('pedidos/guardar-nuevo', [PedidoController::class, 'guardarNuevoPedido'])->name('pedidos.guardar-nuevo');

    // APIs para UI dinámica
    Route::get('api/producto/{idProducto}/variantes', [PedidoController::class, 'apiVariantesPorProducto'])->name('api.variantes.producto');
    Route::get('api/producto/{idProducto}/opciones', [PedidoController::class, 'apiOpcionesPorProducto'])->name('api.opciones.producto');
    Route::get('api/variante/{idVariante}/caracteristicas', [PedidoController::class, 'apiCaracteristicasDeVariante'])->name('api.variante.caracteristicas');
    Route::get('api/variantes', [PedidoController::class, 'apiVariantesActivas'])->name('api.variantes.activas');
    Route::get('api/variante/{idVariante}/productos', [PedidoController::class, 'apiProductosPorVariante'])->name('api.variante.productos');
    Route::get('api/producto/{idProducto}/tallas-precios', [PedidoController::class, 'apiTallasPreciosPorProducto'])->name('api.producto.tallas-precios');
    Route::get('api/clientes/search', [PedidoController::class, 'apiBuscarClientes'])->name('api.clientes.search');

    // Carrito y checkout
    Route::post('carrito/agregar', [PedidoController::class, 'agregarAlCarrito'])->name('pedidos.agregar-carrito');
    Route::get('carrito', [PedidoController::class, 'carrito'])->name('pedidos.carrito');
    Route::delete('carrito/{itemId}', [PedidoController::class, 'eliminarDelCarrito'])->name('pedidos.eliminar-carrito');
    Route::get('checkout', [PedidoController::class, 'checkout'])->name('pedidos.checkout');
    Route::post('procesar-pedido', [PedidoController::class, 'procesarPedido'])->name('pedidos.procesar');
    Route::get('pedido/{idVenta}/confirmacion', [PedidoController::class, 'confirmacion'])->name('pedidos.confirmacion');
    Route::post('pedidos/{idVenta}/detalle', [PedidoController::class, 'agregarDetalle'])->name('pedidos.detalle.agregar');
    Route::post('pedido/{idVenta}/pagos', [PedidoController::class, 'registrarPago'])->name('pedidos.registrar-pago');

    // Administración de pedidos
    Route::get('pedidos', [PedidoController::class, 'index'])->name('pedidos.index');
    Route::get('pedidos/{idVenta}', [PedidoController::class, 'show'])->name('pedidos.show');
    Route::get('pedidos/{idVenta}/edit', [PedidoController::class, 'edit'])->name('pedidos.edit');
    Route::put('pedidos/{idVenta}', [PedidoController::class, 'update'])->name('pedidos.update');
    Route::patch('pedidos/{idVenta}/estado', [PedidoController::class, 'actualizarEstado'])->name('pedidos.actualizar-estado');
    Route::put('pedidos/{idVenta}/detalles', [PedidoController::class, 'updateDetalles'])->name('pedidos.update-detalles');
    Route::delete('pedidos/{idVenta}', [PedidoController::class, 'destroy'])->name('pedidos.destroy');
    Route::delete('pedidos/{idVenta}/eliminar-imagen', [PedidoController::class, 'eliminarImagen'])->name('pedidos.eliminar-imagen');

    /*
     |--------------------------------------------------------------------------
     | Gestión de Empleados
     |--------------------------------------------------------------------------
     */
    Route::resource('empleados', EmpleadoController::class);
    Route::patch('/empleados/{empleado}/toggle-estado', [EmpleadoController::class, 'toggleEstado'])->name('empleados.toggleEstado');
    Route::get('/empleados/{empleado}/estadisticas', [EmpleadoController::class, 'estadisticas'])->name('empleados.estadisticas');

    /*
     |--------------------------------------------------------------------------
     | Exportación PDF
     |--------------------------------------------------------------------------
     */
    Route::get('/export/usuarios/pdf', [ExportController::class, 'exportarUsuarios'])->name('export.usuarios.pdf');
    Route::get('/export/empleados/pdf', [ExportController::class, 'exportarEmpleados'])->name('export.empleados.pdf');
    Route::get('/export/clientes-naturales/pdf', [ExportController::class, 'exportarClientesNaturales'])->name('export.clientes-naturales.pdf');
    Route::get('/export/clientes-establecimientos/pdf', [ExportController::class, 'exportarClientesEstablecimientos'])->name('export.clientes-establecimientos.pdf');
    Route::get('/export/clientes-consolidado/pdf', [ExportController::class, 'exportarClientesConsolidado'])->name('export.clientes-consolidado.pdf');
    Route::get('/export/productos/pdf', [ExportController::class, 'exportarProductos'])->name('export.productos.pdf');


    // En el grupo de rutas para rolVendedor
    Route::get('/rolVendedor/dashboard', [VendedorController::class, 'dashboard'])->name('rolVendedor.dashboard');
});


// =========================================================================
// ROL DISEÑADOR - FLUJO SIMPLIFICADO
// =========================================================================
Route::middleware('auth')->group(function () {

    // Trabajar en un diseño específico
    Route::get('/diseñador/trabajar/{idDiseno}', [DisenadorController::class, 'trabajar'])->name('diseñador.trabajar');

    // Mis Diseños
    Route::get('/diseñador/mis-disenos', [DisenadorController::class, 'misDisenos'])->name('mis-disenos.index');

    // Subir diseño terminado
    Route::post('/diseñador/{idDiseno}/subir', [DisenadorController::class, 'subirDisenoTerminado'])->name('diseñador.subir');
});



// =========================================================================
// ROL OPERADOR - FLUJO SIMPLIFICADO
// =========================================================================

Route::middleware(['auth'])->group(function () {

    // Gestión de Pedidos para Operador
    Route::get('/rolOperador/index', [OperadorController::class, 'index'])->name('rolOperador.index');

    // Catálogo para Operador
    Route::get('/rolOperador/catalogo', [OperadorController::class, 'catalogo'])->name('rolOperador.catalogo');

    // Catálogo de consulta (ruta adicional para compatibilidad)
    Route::get('/catalogo/consulta', [OperadorController::class, 'catalogo'])->name('catalogo.consulta');

    // Ver detalle de pedido para Operador
    Route::get('/rolOperador/{pedido}/show', [OperadorController::class, 'show'])->name('rolOperador.show');
});
// =========================================================================
// ROL CLIENTE - FLUJO SIMPLIFICADO
// =========================================================================

// Rutas para Clientes
Route::middleware(['auth'])->group(function () {
    
  
    
    // Historial de pedidos del cliente
    Route::get('/rolCliente/historial', [ClienteDashboardController::class, 'historial'])->name('rolCliente.historial');
    
    // Detalle de un pedido específico
    Route::get('/rolCliente/pedido/{idVenta}', [ClienteDashboardController::class, 'detallePedido'])->name('rolCliente.detalle-pedido');
    
    // API para estadísticas (si necesitas AJAX)
    Route::get('/rolCliente/estadisticas', [ClienteDashboardController::class, 'getEstadisticas'])->name('rolCliente.estadisticas');
    
    // Vista principal/consulta
    Route::get('/rolCliente/consulta', [ClienteDashboardController::class, 'consulta'])->name('rolCliente.consulta');
    
    
    // Mostrar perfil del cliente
    Route::get('/rolCliente/perfil', [ClienteDashboardController::class, 'perfil'])->name('rolCliente.perfil');
    
});