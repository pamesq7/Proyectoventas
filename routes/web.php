<?php

use Illuminate\Support\Facades\Route;
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
use App\Http\Controllers\TiendaController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ClienteDashboardController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// =========================================================================
// RUTAS PÚBLICAS
// =========================================================================

// Ruta de inicio
Route::get('/', [TiendaController::class, 'index'])->name('home');

// RUTAS PÚBLICAS DE TIENDA
Route::get('/tienda', [TiendaController::class, 'tienda'])->name('tienda.index');
Route::get('/categoria/{categoria}', [TiendaController::class, 'categoria'])->name('tienda.categoria');
Route::get('/producto/{id}', [TiendaController::class, 'show'])->name('producto.show');

// RUTAS DE AUTENTICACIÓN (PÚBLICAS)
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

// LOGIN CLIENTE
Route::get('/cliente/login', [ClienteController::class, 'showLogin'])->name('cliente.login');
Route::post('/cliente/login', [ClienteController::class, 'login'])->name('cliente.login.post');

// LOGOUT (protegido por auth en el controlador)
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::post('/cliente/logout', [ClienteController::class, 'logout'])->name('cliente.logout');

// =========================================================================
// RUTAS PROTEGIDAS (REQUIEREN AUTENTICACIÓN)
// =========================================================================
Route::middleware('auth')->group(function () {
    
    // DASHBOARD PRINCIPAL
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    // Dashboards específicos por rol (CON MIDDLEWARE DE ROLES AÑADIDO)
    Route::get('/admin/dashboard', [HomeController::class, 'adminDashboard'])
        ->name('dashboard.admin')
        ->middleware(['auth', 'role:administrador']);
        
    Route::get('/disenador/dashboard', [HomeController::class, 'disenadorDashboard'])
        ->name('dashboard.disenador')
        ->middleware(['auth', 'role:diseñador']);
        
    Route::get('/vendedor/dashboard', [HomeController::class, 'vendedorDashboard'])
        ->name('dashboard.vendedor')
        ->middleware(['auth', 'role:vendedor']);
        
    Route::get('/operador/dashboard', [HomeController::class, 'operadorDashboard'])
        ->name('dashboard.operador')
        ->middleware(['auth', 'role:operador']);
        
    Route::get('/cliente/dashboard', [HomeController::class, 'clienteDashboard'])
        ->name('dashboard.cliente')
        ->middleware(['auth', 'role:cliente']);

    // RUTAS ESPECÍFICAS POR ROL (AÑADIDAS DEL CÓDIGO 1)
    Route::prefix('disenos')->group(function () {
        Route::get('/mis-disenos', [DisenoController::class, 'misDisenos'])
            ->name('disenos.mis-disenos');
    });

    Route::prefix('pedidos')->group(function () {
        Route::get('/asignados', [PedidoController::class, 'pedidosAsignados'])
            ->name('pedidos.asignados');
    });

    Route::prefix('clientes')->group(function () {
        Route::get('/consulta', [ClienteNaturalController::class, 'consulta'])
            ->name('clientes.consulta');
    });

    // DASHBOARD CLIENTE
    Route::get('/cliente/dashboard', [ClienteDashboardController::class, 'index'])->name('dashboard.cliente');
    Route::get('/cliente/productos', [TiendaController::class, 'catalogoCliente'])->name('cliente.productos');
    Route::get('/cliente/producto/{id}', [TiendaController::class, 'verProducto'])->name('cliente.producto');
    Route::get('/cliente/pedidos/historial', [PedidoController::class, 'historialCliente'])->name('pedidos.historial');
    Route::get('/cliente/perfil', [UserController::class, 'perfil'])->name('perfil.cliente');
    Route::put('/cliente/perfil/actualizar', [UserController::class, 'actualizarPerfil'])->name('perfil.actualizar');
    Route::get('/cliente/pedido/{idVenta}', [ClienteDashboardController::class, 'detallePedido'])->name('cliente.pedido.detalle');
    Route::get('/cliente/pedido/{idVenta}/detalle', [ClienteDashboardController::class, 'detallePedido'])->name('cliente.pedido.detalle');
   
    // RUTA CORREGIDA PARA HISTORIAL DE CLIENTE
    Route::get('/cliente/pedidos', [ClienteDashboardController::class, 'historial'])->name('cliente.pedidos');

    /*
    |--------------------------------------------------------------------------
    | Gestión de Ventas
    |--------------------------------------------------------------------------
    */
    Route::prefix('ventas')->group(function () {
        Route::get('/', [VentaController::class, 'index'])->name('ventas.index');
        Route::get('/create', [VentaController::class, 'create'])->name('ventas.create');
        Route::get('/morosos', [VentaController::class, 'clientesMorosos'])->name('ventas.morosos');
        Route::get('/dashboard', [VentaController::class, 'dashboard'])->name('ventas.dashboard');
        Route::post('/', [VentaController::class, 'store'])->name('ventas.store');
        Route::get('/{id}', [VentaController::class, 'show'])->name('ventas.show');
        Route::post('/{id}/estado', [VentaController::class, 'actualizarEstado'])->name('ventas.actualizar-estado');
    });

    /*
    |--------------------------------------------------------------------------
    | Gestión de Pagos
    |--------------------------------------------------------------------------
    */
    Route::prefix('pagos')->group(function () {
        Route::get('/', [PagoController::class, 'index'])->name('pagos.index');
        Route::get('/{id}/edit', [PagoController::class, 'editPago'])->name('pagos.edit');
        Route::put('/{id}', [PagoController::class, 'updatePago'])->name('pagos.update');
    });

    /*
    |--------------------------------------------------------------------------
    | Reportes
    |--------------------------------------------------------------------------
    */
    Route::get('reportes', [ReporteController::class, 'index'])->name('reportes.index');

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
    | Configuración del Sistema
    |--------------------------------------------------------------------------
    */
    // Categorías
    Route::resource('categorias', CategoriaController::class);
    Route::patch('categorias/{categoria}/toggle', [CategoriaController::class, 'toggleEstado'])->name('categorias.toggleEstado');

    // Opciones
    Route::resource('opciones', OpcionController::class)->parameters(['opciones' => 'opcion']);
    Route::patch('opciones/{opcion}/toggle-estado', [OpcionController::class, 'toggleEstado'])->name('opciones.toggleEstado');

    // Características
    Route::resource('caracteristicas', CaracteristicaController::class)->parameters(['caracteristicas' => 'caracteristica']);
    Route::get('api/opciones/{idOpcion}/caracteristicas', [CaracteristicaController::class, 'getByOpcion'])->name('api.caracteristicas.by-opcion');
    Route::get('caracteristicas/por-opcion/{idOpcion}', [CaracteristicaController::class, 'getByOpcion'])->name('caracteristicas.por-opcion');

    // Variantes
    Route::resource('variantes', VarianteController::class);

    /*
    |--------------------------------------------------------------------------
    | Gestión de Productos
    |--------------------------------------------------------------------------
    */
    Route::resource('productos', ProductoController::class);

    // Variantes de Productos
    Route::prefix('productos/{producto}')->group(function () {
        Route::post('/variantes/attach', [ProductoController::class, 'attachVariante'])->name('productos.attachVariante');
        Route::delete('/variantes/{idVariante}/detach', [ProductoController::class, 'detachVariante'])->name('productos.detachVariante');
        Route::put('/variantes/{idVariante}/update-relation', [ProductoController::class, 'updateVarianteRelation'])->name('productos.updateVarianteRelation');
        Route::get('/variantes', [ProductoController::class, 'getProductoVariantes'])->name('productos.getVariantes');
        Route::post('/generar-variantes', [ProductoController::class, 'generarVariantesAutomaticas'])->name('productos.generarVariantesAutomaticas');
    });

    // Rutas legacy para compatibilidad
    Route::post('productos/variante', [ProductoController::class, 'storeVariante'])->name('productos.storeVariante');
    Route::delete('productos/variante/{variante}', [ProductoController::class, 'deleteVariante'])->name('productos.deleteVariante');
    Route::get('productos/caracteristicas/{opcion}', [ProductoController::class, 'getCaracteristicasByOpcion'])->name('productos.caracteristicasByOpcion');

    /*
    |--------------------------------------------------------------------------
    | Gestión de Diseños
    |--------------------------------------------------------------------------
    */
    Route::resource('disenos', DisenoController::class);

    /*
    |--------------------------------------------------------------------------
    | Gestión de Pedidos - CORREGIDO EL ORDEN
    |--------------------------------------------------------------------------
    */
    
    // ✅ CORRECCIÓN: RUTAS INDIVIDUALES PRIMERO (FUERA DEL PREFIX)
    Route::get('pedidos/nuevo', [PedidoController::class, 'nuevoPedido'])->name('pedidos.nuevo');
    Route::post('pedidos/guardar-nuevo', [PedidoController::class, 'guardarNuevoPedido'])->name('pedidos.guardar-nuevo');

    // Catálogo y configuración de pedidos
    Route::get('catalogo', [PedidoController::class, 'catalogo'])->name('pedidos.catalogo');
    Route::get('catalogo/consulta', [PedidoController::class, 'catalogo'])->name('catalogo.consulta');
    Route::get('producto/{idProducto}/configurar', [PedidoController::class, 'configurarProducto'])->name('pedidos.configurar');
    Route::get('personalizar', [PedidoController::class, 'personalizarDiseno'])->name('pedidos.personalizar');
    Route::post('personalizar/iniciar', [PedidoController::class, 'iniciarPedidoConDiseno'])->name('pedidos.personalizar.iniciar');

    // Carrito de compras
    Route::post('carrito/agregar', [PedidoController::class, 'agregarAlCarrito'])->name('pedidos.agregar-carrito');
    Route::get('carrito', [PedidoController::class, 'carrito'])->name('pedidos.carrito');
    Route::delete('carrito/{itemId}', [PedidoController::class, 'eliminarDelCarrito'])->name('pedidos.eliminar-carrito');

    // Checkout y procesamiento
    Route::get('checkout', [PedidoController::class, 'checkout'])->name('pedidos.checkout');
    Route::post('procesar-pedido', [PedidoController::class, 'procesarPedido'])->name('pedidos.procesar');
    Route::get('pedido/{idVenta}/confirmacion', [PedidoController::class, 'confirmacion'])->name('pedidos.confirmacion');
    Route::post('pedidos/{idVenta}/detalle', [PedidoController::class, 'agregarDetalle'])->name('pedidos.detalle.agregar');
    Route::post('pedido/{idVenta}/pagos', [PedidoController::class, 'registrarPago'])->name('pedidos.registrar-pago');

    // ✅ LUEGO: El prefix de pedidos
    Route::prefix('pedidos')->group(function () {
        Route::get('/', [PedidoController::class, 'index'])->name('pedidos.index');
        Route::get('/{idVenta}', [PedidoController::class, 'show'])->name('pedidos.show');
        Route::get('/{idVenta}/edit', [PedidoController::class, 'edit'])->name('pedidos.edit');
        Route::put('/{idVenta}', [PedidoController::class, 'update'])->name('pedidos.update');
        Route::patch('/{idVenta}/estado', [PedidoController::class, 'actualizarEstado'])->name('pedidos.actualizar-estado');
        Route::put('/{idVenta}/detalles', [PedidoController::class, 'updateDetalles'])->name('pedidos.update-detalles');
        Route::delete('/{idVenta}', [PedidoController::class, 'destroy'])->name('pedidos.destroy');
    });

    /*
    |--------------------------------------------------------------------------
    | API para Pedidos
    |--------------------------------------------------------------------------
    */
    Route::prefix('api')->group(function () {
        Route::get('/producto/{idProducto}/variantes', [PedidoController::class, 'apiVariantesPorProducto'])->name('api.variantes.producto');
        Route::get('/producto/{idProducto}/opciones', [PedidoController::class, 'apiOpcionesPorProducto'])->name('api.opciones.producto');
        Route::get('/variante/{idVariante}/caracteristicas', [PedidoController::class, 'apiCaracteristicasDeVariante'])->name('api.variante.caracteristicas');
        Route::get('/variantes', [PedidoController::class, 'apiVariantesActivas'])->name('api.variantes.activas');
        Route::get('/variante/{idVariante}/productos', [PedidoController::class, 'apiProductosPorVariante'])->name('api.variante.productos');
        Route::get('/producto/{idProducto}/tallas-precios', [PedidoController::class, 'apiTallasPreciosPorProducto'])->name('api.producto.tallas-precios');
        Route::get('/clientes/search', [PedidoController::class, 'apiBuscarClientes'])->name('api.clientes.search');
    });

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
    Route::prefix('export')->group(function () {
        Route::get('/disenos/pdf', [ExportController::class, 'exportarDisenos'])->name('export.disenos.pdf');
        Route::get('/pedidos/pdf', [ExportController::class, 'exportarPedidos'])->name('export.pedidos.pdf');
        Route::get('/usuarios/pdf', [ExportController::class, 'exportarUsuarios'])->name('export.usuarios.pdf');
        Route::get('/empleados/pdf', [ExportController::class, 'exportarEmpleados'])->name('export.empleados.pdf');
        Route::get('/clientes-naturales/pdf', [ExportController::class, 'exportarClientesNaturales'])->name('export.clientes-naturales.pdf');
        Route::get('/clientes-establecimientos/pdf', [ExportController::class, 'exportarClientesEstablecimientos'])->name('export.clientes-establecimientos.pdf');
        Route::get('/clientes-consolidado/pdf', [ExportController::class, 'exportarClientesConsolidado'])->name('export.clientes-consolidado.pdf');
        Route::get('/productos/pdf', [ExportController::class, 'exportarProductos'])->name('export.productos.pdf');
    });
});

// Ruta temporal para prueba (AÑADIDA DEL CÓDIGO 1)
Route::get('/test-disenador', function () {
    return "Ruta de prueba del diseñador funciona";
})->name('test.disenador');

// Ruta de debug para pedidos/nuevo
Route::get('/debug-pedido-nuevo', function () {
    try {
        // Verifica si el controlador y método existen
        if (method_exists(App\Http\Controllers\PedidoController::class, 'nuevoPedido')) {
            return "✅ Controlador y método EXISTEN - Ahora prueba: http://127.0.0.1:8000/pedidos/nuevo (debes estar logueado)";
        } else {
            return "❌ El método nuevoPedido NO existe en PedidoController";
        }
    } catch (Exception $e) {
        return "❌ Error: " . $e->getMessage();
    }
});

// Ruta about pública
Route::get('/about', function () {
    return view('about');
})->name('about');