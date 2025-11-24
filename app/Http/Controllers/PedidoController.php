<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\DetalleVenta;
use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Talla;
use App\Models\Variante;
use App\Models\Caracteristica;
use App\Models\ClienteNatural;
use App\Models\ClienteEstablecimiento;
use App\Models\Transaccion;
use App\Models\Empleado;
use App\Models\Diseno;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Arr;
use App\Models\Pack;
use App\Models\Departamento;
use App\Models\Provincia;
use App\Models\Municipio;
use App\Models\DetalleTalla;
use App\Models\Direccion;


class PedidoController extends Controller
{
    /**
     * Mostrar catálogo de productos para clientes
     */
    public function catalogo()
    {
        $productos = Producto::with([
            'categoria',
            'variante.varianteCaracteristicas.caracteristica.opcion',
            'productoDiseno.diseno'
        ])
            ->leftJoin('categorias', 'productos.idCategoria', '=', 'categorias.idCategoria')
            ->where('productos.estado', 1)
            ->orderBy('categorias.nombreCategoria')
            ->orderBy('productos.nombre')
            ->select('productos.*')
            ->get();

        $categorias = Categoria::where('estado', 1)
            ->orderBy('nombreCategoria')
            ->get();

        return view('pedidos.catalogo', compact('productos', 'categorias'));
    }
    public function nuevoPersonalizado($tipo = null)
    {
        try {
            // Obtener datos necesarios para el formulario
            $tallas = Talla::where('estado', 1)->get();
            $clientesNaturales = ClienteNatural::where('estado', 1)->get();
            $establecimientos = ClienteEstablecimiento::where('estado', 1)->get();
            $empleados = Empleado::where('estado', 1)->get();

            // Obtener diseñadores (empleados con rol de diseñador)
            $diseñadores = Empleado::whereHas('user', function ($query) {
                $query->where('rol', 'diseñador')
                    ->orWhere('rol', 'administrador');
            })->where('estado', 1)->get();

            // Obtener productos (solo IDs 1-4 como menciona el comentario en la vista)
            $productos = Producto::whereIn('idProducto', [1, 2, 3, 4])
                ->where('estado', 1)
                ->get();

            // Obtener información del producto si se especificó un tipo
            $productoRapido = null;
            if ($tipo) {
                $productosRapidos = [
                    'polera' => ['nombre' => 'Polera', 'precio' => 85],
                    'corto' => ['nombre' => 'Corto', 'precio' => 75],
                    'conjunto_pyc' => ['nombre' => 'Conjunto (Polera + Corto)', 'precio' => 150],
                    'chamarra' => ['nombre' => 'Chamarra', 'precio' => 120],
                    'buzo' => ['nombre' => 'Buzo', 'precio' => 110],
                    'conjunto_cb' => ['nombre' => 'Conjunto (Chamarra + Buzo)', 'precio' => 210],
                ];

                if (array_key_exists($tipo, $productosRapidos)) {
                    $productoRapido = (object)$productosRapidos[$tipo];
                }
            }

            return view('pedidos.nuevoPersonalizado', compact(
                'tallas',
                'clientesNaturales',
                'establecimientos',
                'empleados',
                'diseñadores',
                'productos',  // Añadir productos a la vista
                'productoRapido',
                'tipo'
            ));
        } catch (\Exception $e) {
            \Log::error('Error en nuevoPersonalizado: ' . $e->getMessage());
            return back()->with('error', 'Error al cargar el formulario: ' . $e->getMessage());
        }
    }

    public function guardarNuevoPersonalizado(Request $request)
    {
        try {
            // ✅ Validación
            $request->validate([
                'fechaEntrega'        => 'required|date|after:today',
                'lugarEntrega'        => 'required|string|max:255',
                'tipoCliente'         => 'required|in:natural,establecimiento',
                'idCliente'           => 'required_if:tipoCliente,natural|nullable|integer',
                'idEstablecimiento'   => 'required_if:tipoCliente,establecimiento|nullable|integer',
                'idEmpleado'          => 'required|integer|exists:empleados,idEmpleado',  // diseñador
                'idProducto'          => 'required|integer|exists:productos,idProducto',
                'disenoPersonalizado' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
                'ruta_diseno'         => 'nullable|string',
                'itemsBySize_json'    => 'required|json',   // [{idTallas,cantidad,observaciones}]
                'roster_json'         => 'nullable|json',   // [{idTallas,nombre,numero}]
            ]);

            DB::beginTransaction();

            // 🔹 0) Crear dirección para el pedido
            $direccion = Direccion::create([
                'direccionPrincipal' => $request->lugarEntrega,
                'referencia' => $request->lugarEntrega,
                'estado' => 1
            ]);

            // 🔹 1) Parsear items y roster
            $items  = json_decode($request->itemsBySize_json, true) ?? [];
            $roster = json_decode($request->roster_json, true) ?? [];

            if (!is_array($items) || empty($items)) {
                throw new \Exception('Debe ingresar al menos una talla con cantidad mayor a cero.');
            }
            if (!is_array($roster)) $roster = [];

            // Indexar roster por talla
            $rosterPorTalla = [];
            foreach ($roster as $r) {
                $tid = (int)($r['idTallas'] ?? 0);
                if ($tid > 0) {
                    $rosterPorTalla[$tid][] = [
                        'nombre' => (string)($r['nombre'] ?? ''),
                        'numero' => (string)($r['numero'] ?? ''),
                    ];
                }
            }

            // 🔹 2) Producto y precios
            $producto   = Producto::findOrFail($request->idProducto);
            $precioBase = (float)($producto->precioVenta ?? 0);

            // 🔹 3) Empleado “seguro” (vendedor / responsable de la venta)
            $idEmpleadoSeguro = optional(optional(auth()->user())->empleado)->idEmpleado
                ?? DB::table('empleados')->value('idEmpleado');

            if (!$idEmpleadoSeguro) {
                throw new \Exception('No existe ningún empleado para asociar la venta.');
            }

            // 🔹 4) Calcular totales y preparar items
            $subtotal        = 0.0;
            $itemsCalculados = [];

            foreach ($items as $it) {
                $idTallas  = (int)($it['idTallas'] ?? 0);
                $cantidad  = (int)($it['cantidad'] ?? 0);
                if ($idTallas <= 0 || $cantidad <= 0) continue;

                $precioUnit = $precioBase;
                $sub        = $precioUnit * $cantidad;
                $subtotal  += $sub;

                $itemsCalculados[] = [
                    'idProducto'     => $producto->idProducto,
                    'idTallas'       => $idTallas,
                    'cantidad'       => $cantidad,
                    'precioUnitario' => $precioUnit,
                    'subtotal'       => $sub,
                    'observaciones'  => trim((string)($it['observaciones'] ?? '')),
                    'roster'         => $rosterPorTalla[$idTallas] ?? [],
                ];
            }

            if (empty($itemsCalculados)) {
                throw new \Exception('No hay tallas válidas con cantidad > 0.');
            }

            $total = $subtotal;
            $saldo = $total; // aquí no manejamos adelanto en este formulario

            $adelanto = (float)($request->montoAdelanto ?? 0);
            if ($adelanto > 0 && $adelanto <= $subtotal) {
                $saldo = max($subtotal - $adelanto, 0);
            } else {
                $saldo = $subtotal;
            }

            // 🔹 5) Crear venta (tabla ventas)
            $venta = Venta::create([
                'subtotal'          => $subtotal,
                'total'             => $subtotal,
                'fechaEntrega'      => $request->fechaEntrega,
                'lugarEntrega'      => $request->lugarEntrega,
                'estadoPedido'      => '0',
                'saldo'             => $saldo,
                'estado'            => 1,
                'idEmpleado'        => $idEmpleadoSeguro,
                'idCliente'         => $request->tipoCliente === 'natural' ? ($request->idCliente ?: null) : null,
                'idEstablecimiento' => $request->tipoCliente === 'establecimiento' ? ($request->idEstablecimiento ?: null) : null,
                'idDireccion'       => $direccion->idDireccion,
            ]);

            // 🔹 6) Crear detalles + tallas
            $primerDetalle = null;
            foreach ($itemsCalculados as $item) {

                $descripcionJson = json_encode([
                    'obs'    => $item['observaciones'],
                    'roster' => $item['roster'],
                ]);

                $detalle = DetalleVenta::create([
                    'cantidad'       => $item['cantidad'],
                    'precioUnitario' => $item['precioUnitario'],
                    'descuento'      => 0,
                    'descripcion'    => $descripcionJson,
                    'tipo_pack'      => 'producto',
                    'subtotal'       => $item['subtotal'],
                    'estado'         => 1,
                    'idProducto'     => $item['idProducto'],
                    'idPack'         => null,
                    'idEmpleado'     => $idEmpleadoSeguro,
                    'idVenta'        => $venta->idVenta,
                ]);

                DetalleTalla::create([
                    'idDetalleVenta' => $detalle->idDetalleVenta,
                    'idTallas'       => $item['idTallas'],
                    'nombre'         => null,
                    'numero'         => null,
                    'adicional'      => null,
                    'estado'         => 1,
                ]);

                if (!$primerDetalle) {
                    $primerDetalle = $detalle;
                }
            }

            // 🔹 7) Manejar diseño (archivo) y vincularlo al primer detalle
            $rutaDiseno = null;

            if ($request->hasFile('disenoPersonalizado')) {
                $rutaDiseno = $request->file('disenoPersonalizado')
                    ->store('disenos_personalizados', 'public');
            } elseif ($request->filled('ruta_diseno')) {
                // Si te llega una ruta ya existente
                $rutaDiseno = $request->ruta_diseno;
            }

            if ($rutaDiseno && $primerDetalle) {
                Diseno::create([
                    'archivo'        => $rutaDiseno,
                    'iddetalleVenta' => $primerDetalle->idDetalleVenta,
                    'estado'         => 1,
                    'idEmpleado'     => (int)$request->idEmpleado, // diseñador
                ]);
            }

            DB::commit();

            return redirect()->route('pedidos.confirmacion', $venta->idVenta)
                ->with('success', 'Pedido personalizado creado exitosamente');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al guardar pedido personalizado: ' . $e->getMessage());
            return back()->with('error', 'Error al guardar el pedido: ' . $e->getMessage())
                ->withInput();
        }
    }


    /**
     * API: Precios por talla para un producto
     */
    // PedidoController.php  (reemplaza el método entero)

    // En PedidoController.php

    // Método para la API de precios por talla
    public function apiTallasPreciosPorProducto($idProducto)
    {
        $producto = \App\Models\Producto::findOrFail($idProducto);
        $tallas = \App\Models\Talla::orderBy('nombre', 'asc')->get();
        $precios = $tallas->map(fn($t) => [
            'idTallas' => (int)$t->idTallas,
            'talla' => $t->nombre,
            'precioUnitario' => (float)$producto->precioVenta,
        ]);
        return response()->json(['precios' => $precios]);
    }
    public function guardarNuevoPedido(Request $request)
    {
        // ✅ Validación principal
        $request->validate([
            'fechaEntrega'       => ['required', 'date', 'after:today'],
            'lugarEntrega'       => ['required', 'string', 'max:255'],
            'idProducto'         => ['required', 'integer', 'exists:productos,idProducto'],
            'idEmpleado'         => ['required', 'integer', 'exists:empleados,idEmpleado'], // diseñador asignado
            'tipoCliente'        => ['required', 'in:natural,establecimiento'],
            'idCliente'          => ['required_if:tipoCliente,natural', 'nullable', 'integer'],
            'idEstablecimiento'  => ['required_if:tipoCliente,establecimiento', 'nullable', 'integer'],
            'itemsBySize_json'   => ['required', 'string'],  // [{idTallas,talla,cantidad,observaciones}]
            'roster_json'        => ['nullable', 'string'],  // [{idTallas,nombre,numero}]
            'tipoTransaccion'    => ['nullable', 'in:efectivo,qr,cheque,transferencia'],
            'montoAdelanto'      => ['nullable', 'numeric', 'min:0'],
        ]);

        // 🔹 1) Parseo de JSONs
        $itemsBySize = json_decode($request->input('itemsBySize_json', '[]'), true);
        $roster      = json_decode($request->input('roster_json', '[]'), true);

        if (!is_array($itemsBySize) || empty($itemsBySize)) {
            return back()->with('error', 'Debes cargar cantidades por talla.')->withInput();
        }
        if (!is_array($roster)) $roster = [];

        // Indexar roster por idTallas
        $rosterPorTalla = [];
        foreach ($roster as $r) {
            $tid = (int)($r['idTallas'] ?? 0);
            if ($tid > 0) {
                $rosterPorTalla[$tid][] = [
                    'nombre' => (string)($r['nombre'] ?? ''),
                    'numero' => (string)($r['numero'] ?? ''),
                ];
            }
        }

        // 🔹 2) Producto y precios
        $producto   = Producto::findOrFail($request->idProducto);
        $precioBase = (float)($producto->precioVenta ?? 0);

        // 🔹 3) Empleado “seguro” (responsable de la venta)
        $idEmpleadoSeguro = optional(optional(auth()->user())->empleado)->idEmpleado
            ?? DB::table('empleados')->value('idEmpleado');

        if (!$idEmpleadoSeguro) {
            return back()->with('error', 'No existe ningún empleado para asociar la venta.')->withInput();
        }

        DB::beginTransaction();
        try {
            // 🔹 4) Calcular subtotales
            $subtotal        = 0.0;
            $itemsCalculados = [];

            foreach ($itemsBySize as $it) {
                $idTallas  = (int)($it['idTallas'] ?? 0);
                $cantidad  = (int)($it['cantidad'] ?? 0);
                if ($idTallas <= 0 || $cantidad <= 0) continue;

                $precioUnit = $precioBase;
                $sub        = $precioUnit * $cantidad;
                $subtotal  += $sub;

                $itemsCalculados[] = [
                    'idProducto'     => $producto->idProducto,
                    'idTallas'       => $idTallas,
                    'cantidad'       => $cantidad,
                    'precioUnitario' => $precioUnit,
                    'subtotal'       => $sub,
                    'observaciones'  => trim((string)($it['observaciones'] ?? '')),
                    'roster'         => $rosterPorTalla[$idTallas] ?? [],
                ];
            }

            if (empty($itemsCalculados)) {
                throw new \Exception('No hay tallas válidas con cantidad > 0.');
            }

            $total    = $subtotal;
            $saldo    = $total;
            $adelanto = (float)($request->montoAdelanto ?? 0);
            if ($adelanto > 0 && $adelanto <= $total) {
                $saldo = max($total - $adelanto, 0);
            }

            // 🔹 5) Crear venta
            $venta = Venta::create([
                'subtotal'          => $subtotal,
                'total'             => $total,
                'fechaEntrega'      => $request->fechaEntrega,
                'lugarEntrega'      => $request->lugarEntrega,
                'estadoPedido'      => '0',
                'saldo'             => $saldo,
                'estado'            => 1,
                'idEmpleado'        => $idEmpleadoSeguro,
                'idCliente'         => $request->tipoCliente === 'natural' ? ($request->idCliente ?: null) : null,
                'idEstablecimiento' => $request->tipoCliente === 'establecimiento' ? ($request->idEstablecimiento ?: null) : null,
                'idDireccion'       => $direccion->idDireccion,
            ]);

            // 🔹 6) Crear detalles + tallas
            $primerDetalle = null;
            foreach ($itemsCalculados as $item) {

                $descripcionJson = json_encode([
                    'obs'    => $item['observaciones'],
                    'roster' => $item['roster'],
                ]);

                $detalle = DetalleVenta::create([
                    'cantidad'       => $item['cantidad'],
                    'precioUnitario' => $item['precioUnitario'],
                    'descuento'      => 0,
                    'descripcion'    => $descripcionJson,
                    'tipo_pack'      => 'producto',   // o 'pack'
                    'subtotal'       => $item['subtotal'],
                    'estado'         => 1,
                    'idProducto'     => $item['idProducto'],
                    'idPack'         => null,
                    'idEmpleado'     => $idEmpleadoSeguro,
                    'idVenta'        => $venta->idVenta,
                ]);

                DetalleTalla::create([
                    'idDetalleVenta' => $detalle->idDetalleVenta,
                    'idTallas'       => $item['idTallas'],
                    'nombre'         => null,
                    'numero'         => null,
                    'adicional'      => null,
                    'estado'         => 1,
                ]);

                if (!$primerDetalle) {
                    $primerDetalle = $detalle;
                }
            }

            // 🔹 7) Asociar diseño temporal (si existe) al primer detalle
            if (session()->has('disenoTemporal') && $primerDetalle) {
                $rutaDiseno = session()->get('disenoTemporal');
                Diseno::create([
                    'archivo'        => $rutaDiseno,
                    'iddetalleVenta' => $primerDetalle->idDetalleVenta,
                    'estado'         => 1,
                    'idEmpleado'     => (int)$request->idEmpleado, // diseñador asignado
                ]);
                session()->forget('disenoTemporal');
            }

            // 🔹 8) Registrar adelanto (si corresponde)
            if ($adelanto > 0) {
                if ($adelanto > $venta->total) {
                    throw new \Exception('El adelanto no puede ser mayor que el total.');
                }

                Transaccion::create([
                    'tipoTransaccion' => 'pago',
                    'monto'           => $adelanto,
                    'metodoPago'      => $request->input('tipoTransaccion', 'efectivo'),
                    'observaciones'   => 'Adelanto de pedido',
                    'estado'          => 1,
                    'idVenta'         => $venta->idVenta,
                ]);
            }

            DB::commit();

            return redirect()->route('pedidos.confirmacion', $venta->idVenta)
                ->with('success', 'Pedido creado exitosamente');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al guardar nuevo pedido', ['error' => $e->getMessage()]);
            return back()->with('error', 'Error al guardar el pedido: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * API: Variantes disponibles para un producto
     */
    public function apiVariantesPorProducto($idProducto)
    {
        $producto = Producto::with(['variante'])
            ->where('idProducto', $idProducto)
            ->firstOrFail();

        $variantes = Variante::where('idProducto', $idProducto)
            ->where('estado', 1)
            ->orderBy('nombre')
            ->get(['idVariante', 'nombre']);

        return response()->json([
            'producto' => ['idProducto' => $producto->idProducto, 'nombre' => $producto->nombre],
            'variantes' => $variantes,
        ]);
    }

    /**
     * API: Opciones de personalización por producto con sus características activas
     */
    // app/Http/Controllers/PedidoController.php
    public function apiOpcionesPorProducto($idProducto)
    {
        try {
            // Cargar el producto con sus relaciones
            $producto = Producto::with([
                'productoOpcion' => function ($query) {
                    $query->where('estado', 1)
                        ->with(['opcion' => function ($q) {
                            $q->where('estado', 1)
                                ->with(['caracteristicas' => function ($q) {
                                    $q->where('estado', 1);
                                }]);
                        }]);
                }
            ])->findOrFail($idProducto);

            // Mapear las opciones y características
            $opciones = $producto->productoOpcion->map(function ($po) {
                if (!$po->opcion) return null;

                return [
                    'idOpcion' => $po->opcion->idOpcion,
                    'nombreOpcion' => $po->opcion->nombre,
                    'caracteristicas' => $po->opcion->caracteristicas->map(function ($c) {
                        return [
                            'idCaracteristica' => $c->idCaracteristica,
                            'nombre' => $c->nombre,
                        ];
                    })->toArray()
                ];
            });

            return response()->json([
                'success' => true,
                'producto' => [
                    'idProducto' => $producto->idProducto,
                    'nombre' => $producto->nombre
                ],
                'opciones' => $opciones,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error en apiOpcionesPorProducto: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());

            return response()->json([
                'success' => false,
                'error' => 'Error al cargar las opciones del producto',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    /**
     * API: Características agrupadas por opción para una variante
     */
    public function apiCaracteristicasDeVariante($idVariante)
    {
        $variante = Variante::with(['varianteCaracteristicas.caracteristica.opcion'])
            ->findOrFail($idVariante);

        $grupo = [];
        foreach ($variante->varianteCaracteristicas as $vc) {
            $car = $vc->caracteristica;
            if (!$car) {
                continue;
            }
            $op = $car->opcion;
            $opKey = $op ? ($op->idOpcion . '|' . $op->nombre) : ('otros|Otros');
            if (!isset($grupo[$opKey])) {
                $grupo[$opKey] = [
                    'idOpcion' => $op->idOpcion ?? null,
                    'nombreOpcion' => $op->nombre ?? 'Otros',
                    'caracteristicas' => []
                ];
            }
            $grupo[$opKey]['caracteristicas'][] = [
                'idCaracteristica' => $car->idCaracteristica,
                'nombre' => $car->nombre,
            ];
        }

        $resultado = array_values($grupo);

        return response()->json([
            'idVariante' => $variante->idVariante,
            'nombreVariante' => $variante->nombre,
            'opciones' => $resultado,
        ]);
    }


    public function configurar($idProducto)
    {
        // 1) Producto
        $producto = Producto::findOrFail($idProducto);

        // 2) Variante “fallback” (case-insensitive + trim)
        $varianteId = $producto->idVariante
            ?? Variante::whereRaw('TRIM(LOWER(nombre)) LIKE ?', ['polera%'])->value('idVariante');
        $varianteNombre = Variante::where('idVariante', $varianteId)->value('nombre') ?? '—';

        // 3) Opciones de la variante (NO PACK)
        $opcionesVariante = $this->getOpcionesVariante($varianteId);

        // 4) Tallas
        $tallas = Talla::where('estado', 1)->orderBy('nombre')->get();

        // 5) Empleados
        $empleados = Empleado::with('user')->where('estado', 1)->get();

        // 6) Clientes naturales/establecimientos
        $clientesNaturales = ClienteNatural::with('user')->where('estado', 1)->get();

        $clientesEstablecimientos = ClienteEstablecimiento::with('representante')->where('estado', 1)->get();

        // 6) PACK — derivamos $packId aunque productos.idPackProducto sea NULL
        $packId = $producto->idPackProducto;
        if (is_null($packId)) {
            $packId = DB::table('pack')->where('idProducto', $producto->idProducto)->value('idPackProducto');
        }

        $esPack        = !is_null($packId);
        $packInfo      = null;
        $packProductos = collect();
        $variantesPack = collect();

        if ($esPack) {
            $packInfo = DB::table('pack')->where('idPackProducto', $packId)->first();

            $idsDesdeProductos = Producto::where('idPackProducto', $packId)->pluck('idProducto');
            $idsDesdePack      = DB::table('pack')->where('idPackProducto', $packId)->pluck('idProducto');

            $ids = $idsDesdeProductos->merge($idsDesdePack)->filter()->unique()->values();

            $packProductos = Producto::whereIn('idProducto', $ids)->orderBy('nombre')->get();

            // 🔹 Variantes ÚNICAS del pack (p.ej. Polera + Corto + Buzo):
            $variantesPack = $packProductos
                ->pluck('idVariante')
                ->filter()
                ->unique()
                ->map(function ($vId) {
                    $vNombre = Variante::where('idVariante', $vId)->value('nombre') ?? '—';
                    return [
                        'idVariante'     => $vId,
                        'nombreVariante' => trim($vNombre),
                        'opciones'       => $this->getOpcionesVariante($vId),
                    ];
                })->values();
        }

        /*
     * 7) MODOS DE PRODUCTO PARA LAS PESTAÑAS (nav-pills)
     *    - pack_polera_corto / solo_polera / solo_corto
     *    - pack_chamarra_buzo / solo_chamarra / solo_buzo
     *    - o 'individual' si no es pack
     */
        $modosProducto = [];

        if ($esPack && $variantesPack->isNotEmpty()) {

            // Buscamos variantes por nombre, case-insensitive
            $polera = $variantesPack->first(function ($v) {
                return strcasecmp($v['nombreVariante'], 'polera') === 0;
            });
            $corto = $variantesPack->first(function ($v) {
                return strcasecmp($v['nombreVariante'], 'corto') === 0;
            });
            $chamarra = $variantesPack->first(function ($v) {
                return strcasecmp($v['nombreVariante'], 'chamarra') === 0;
            });
            $buzo = $variantesPack->first(function ($v) {
                return strcasecmp($v['nombreVariante'], 'buzo') === 0;
            });

            if ($polera && $corto) {
                // Familia Polera + Corto
                $modosProducto = [
                    [
                        'key'   => 'pack_polera_corto',
                        'label' => 'PACK: ' . $polera['nombreVariante'] . ' + ' . $corto['nombreVariante'],
                        'icon'  => 'tshirt',
                    ],
                    [
                        'key'   => 'solo_polera',
                        'label' => $polera['nombreVariante'],
                        'icon'  => 'tshirt',
                    ],
                    [
                        'key'   => 'solo_corto',
                        'label' => $corto['nombreVariante'],
                        'icon'  => 'shorts',
                    ],
                ];
            } elseif ($chamarra && $buzo) {
                // Familia Chamarra + Buzo
                $modosProducto = [
                    [
                        'key'   => 'pack_chamarra_buzo',
                        'label' => 'PACK: ' . $chamarra['nombreVariante'] . ' + ' . $buzo['nombreVariante'],
                        'icon'  => 'jacket',
                    ],
                    [
                        'key'   => 'solo_chamarra',
                        'label' => $chamarra['nombreVariante'],
                        'icon'  => 'jacket',
                    ],
                    [
                        'key'   => 'solo_buzo',
                        'label' => $buzo['nombreVariante'],
                        'icon'  => 'hoodie',
                    ],
                ];
            } else {
                // Pack raro con otras variantes: dejamos una sola pestaña genérica
                $modosProducto[] = [
                    'key'   => 'pack_generico',
                    'label' => 'Pack: ' . $producto->nombre,
                    'icon'  => 'tshirt',
                ];
            }
        } else {
            // Producto individual (no pack)
            $modosProducto[] = [
                'key'   => 'individual',
                'label' => $producto->nombre,
                'icon'  => 'tshirt', // Icono por defecto
            ];
        }

        // Clave del modo seleccionado por defecto (la primera pestaña)
        $modoSeleccionado = $modosProducto[0]['key'] ?? 'individual';

        return view('pedidos.configurar', [
            'producto'                 => $producto,
            'tallas'                   => $tallas,
            'opcionesVariante'         => $opcionesVariante,
            'varianteId'               => $varianteId,
            'varianteNombre'           => $varianteNombre,
            'empleados'                => $empleados,
            'clientesNaturales'        => $clientesNaturales,
            'clientesEstablecimientos' => $clientesEstablecimientos,
            'esPack'                   => $esPack,
            'pack'                     => $packInfo,
            'packProductos'            => $packProductos,
            'variantesPack'            => $variantesPack,

            // 🔹 NUEVO: para las pestañas de la UI
            'modosProducto'            => $modosProducto,
            'modoSeleccionado'         => $modoSeleccionado,
        ]);
    }




    // Método auxiliar para obtener opciones de variante
    private function getOpcionesVariante($varianteId)
    {
        $rows = DB::table('caracteristicas as c')
            ->join('opcions as o', 'o.idOpcion', '=', 'c.idOpcion')   // ✅ opcions
            ->join('variante_caracteristicas as vc', 'vc.idCaracteristica', '=', 'c.idCaracteristica')
            ->where('vc.idVariante', $varianteId)
            ->where('c.estado', 1)
            ->select(
                'o.idOpcion',
                DB::raw('TRIM(o.nombre) as nombreOpcion'),
                'o.descripcion as descOpcion',
                'c.idCaracteristica',
                DB::raw('TRIM(c.nombre) as nombreCaracteristica'),
                'vc.precioAdicional'
            )
            ->orderBy('o.nombre')
            ->orderBy('c.nombre')
            ->get();

        return $rows->groupBy('idOpcion')->map(function ($grupo) {
            return [
                'idOpcion' => $grupo->first()->idOpcion,
                'nombre' => $grupo->first()->nombreOpcion,
                'descripcion' => $grupo->first()->descOpcion,
                'caracteristicas' => $grupo->map(function ($car) {
                    return [
                        'idCaracteristica' => $car->idCaracteristica,
                        'nombre' => $car->nombreCaracteristica,
                        'precioAdicional' => $car->precioAdicional ?? 0,
                    ];
                })->values(),
            ];
        })->values();
    }

    /**
     * Configurar producto con opciones de personalización
     */
    public function configurarProducto($idProducto)
    {
        try {
            $producto = Producto::with([
                'variante.varianteCaracteristicas.caracteristica.opcion' => function ($query) {
                    $query->where('estado', 1);
                },
                'productoDiseno.diseno'
            ])->where('estado', 1)->findOrFail($idProducto);

            $tallas = Talla::where('estado', 1)
                ->orderBy('nombre')
                ->get();

            $diseñadores = Empleado::with('user')
                ->whereHas('user', function ($q) {
                    $q->where('rol', 'diseñador')->where('estado', 1);
                })
                ->where('estado', 1)
                ->get();

            $productos = Producto::where('estado', 1)->get();
            $clientesNaturales = ClienteNatural::with(['user' => function ($query) {
                $query->where('estado', 1);
            }])->where('estado', 1)->get();

            // Orden personalizado para tallas
            $ordenPersonalizado = [
                '3XL',
                '2XL',
                'XL',
                'L',
                'M',
                'S',
                'XS',
                '14',
                '12',
                '10',
                '8',
                '6',
                '4',
                '2',
                '2XLD',
                'XLD',
                'LD',
                'MD',
                'SD',
                '14D'
            ];

            $tallas = $tallas->sortBy(function ($talla) use ($ordenPersonalizado) {
                $index = array_search($talla->nombre, $ordenPersonalizado);
                return $index === false ? 999 : $index;
            });

            $clientesEstablecimientos = ClienteEstablecimiento::where('estado', 1)->get();
            $configuracion = session('configuracion_pedido', []);

            // ¿Este producto participa en algún pack?
            $pack = Pack::where('estado', 1)
                ->whereHas('packProducto', function ($q) use ($idProducto) {
                    $q->where('idProducto', $idProducto);
                })
                ->with(['packProducto.producto']) // para listar los ítems
                ->first();
            $pack = Pack::where('estado', 1)
                ->whereHas('packProducto', function ($q) use ($idProducto) {
                    $q->where('idProducto', $idProducto);
                })
                ->with(['packProducto.producto']) // para listar los ítems
                ->first();


            return view('pedidos.configurar', compact(
                'producto',
                'tallas',
                'diseñadores',
                'productos',
                'clientesNaturales',
                'clientesEstablecimientos',
                'configuracion',
                'pack' // ⬅️ pásalo a la vista

            ));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('pedidos.catalogo')
                ->with('error', 'Producto no encontrado o no disponible.');
        }
    }

    /**
     * Guardar configuración temporal del pedido
     */
    public function guardarConfiguracion(Request $request)
    {
        try {
            $validated = $request->validate([
                'idProducto' => 'required|exists:productos,idProducto',
                'items' => 'required|array|min:1',
                'items.*.idTallas' => 'required|exists:tallas,idTallas',
                'items.*.nombre' => 'nullable|string|max:100',
                'items.*.numero' => 'nullable|integer|min:0|max:999',
                'items.*.observaciones' => 'nullable|string|max:255',
                'fechaEntrega' => 'required|date|after:today',
                'lugarEntrega' => 'required|string|max:255',
                'idEmpleado' => 'required|exists:empleados,idEmpleado'
            ]);

            session([
                'configuracion_pedido' => [
                    'idProducto' => $request->idProducto,
                    'items' => $request->items,
                    'fechaEntrega' => $request->fechaEntrega,
                    'lugarEntrega' => $request->lugarEntrega,
                    'idEmpleado' => $request->idEmpleado,
                    'timestamp' => now()
                ]
            ]);

            return redirect()->route('pedidos.nuevo')
                ->with('success', 'Configuración guardada correctamente. Ahora completa la información del cliente.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        }
    }

    /**
     * Página para "Personalizar mi diseño"
     */
    public function personalizarDiseno(Request $request)
    {
        $ventaParam = $request->query('venta');
        if ($ventaParam) {
            $ventaExiste = Venta::where('idVenta', $ventaParam)->exists();
            if ($ventaExiste) {
                session()->put('ventaDestino', (int) $ventaParam);
            }
        }

        $productosBase = Producto::where('estado', 1)
            ->orderBy('nombre')
            ->get(['idProducto', 'nombre']);

        $tallas = Talla::where('estado', 1)->orderBy('nombre')->get();

        return view('pedidos.personalizar', compact('productosBase', 'tallas'));
    }

    /**
     * Guardar temporalmente el diseño
     */
    public function iniciarPedidoConDiseno(Request $request)
    {
        $request->validate([
            'disenoPersonalizado' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120'
        ]);

        $ruta = $request->file('disenoPersonalizado')->store('disenos_personalizados', 'public');
        session()->put('disenoTemporal', $ruta);

        return redirect()->route('pedidos.nuevo')
            ->with('success', 'Diseño cargado. Completa los datos de tu pedido.');
    }

    /**
     * Mostrar formulario "Nuevo Pedido"
     */
    public function nuevoPedido()
    {
        $user = auth()->user();

        $esAdministrador = optional($user->empleado)->rol === 'administrador';
        $tieneDiseno = session()->has('disenoTemporal');

        if (!$tieneDiseno && !$esAdministrador) {
            return redirect()->route('pedidos.personalizar')
                ->with('error', 'Primero sube tu diseño.');
        }

        $productos = Producto::where('estado', 1)
            ->whereBetween('idProducto', [1, 4])
            ->orderBy('idProducto')
            ->get();

        $tallas = Talla::where('estado', 1)->orderBy('idTallas')->get();
        $clientesNaturales = ClienteNatural::with('user')->where('estado', 1)->get();
        $clientesEstablecimientos = ClienteEstablecimiento::with('user')->where('estado', 1)->get();

        $diseñadores = Empleado::with('user')
            ->where('rol', 'diseñador')
            ->where('estado', 1)
            ->get();

        if ($esAdministrador && !$tieneDiseno) {
            session()->flash('info', 'Modo administrador: Puedes crear pedidos sin diseño. Los clientes normales necesitarán subir un diseño.');
        }

        return view('pedidos.nuevo', compact('productos', 'tallas', 'clientesNaturales', 'clientesEstablecimientos', 'diseñadores'));
    }

    /**
     * Guardar pedido desde formulario con diseño temporal
     */


    /**
     * Guardar pedido directamente desde catálogo (sin diseño)
     */
    public function guardarDesdeCatalogo(Request $request)
    {
        // ✅ 1. Validación limpia (agregamos modo_producto y quitamos código viejo)
        $request->validate([
            'idProducto'        => 'required|exists:productos,idProducto',
            'fechaEntrega'      => 'required|date|after:today',
            'lugarEntrega'      => 'required|string|max:255',
            'idEmpleado'        => 'required|exists:empleados,idEmpleado',
            'tipoCliente'       => 'required|in:natural,establecimiento',
            'idCliente'         => 'required_if:tipoCliente,natural',
            'idEstablecimiento' => 'required_if:tipoCliente,establecimiento',
            'modo_producto'     => 'nullable|string|max:50',          // 👈 NUEVO
            'items'             => 'required|array|min:1',
            'items.*.idTallas'  => 'required|exists:tallas,idTallas',
            'items.*.cantidad'  => 'required|integer|min:1',
            'items.*.nombre'    => 'nullable|string|max:100',
            'items.*.numero'    => 'nullable|integer|min:0|max:999',
            'items.*.observaciones' => 'nullable|string|max:255',
            'caracteristicas'   => 'nullable|array',
            'tipoTransaccion'   => 'nullable|in:efectivo,qr,cheque,transferencia',
            'montoAdelanto'     => 'nullable|numeric|min:0'
        ]);

        DB::beginTransaction();
        try {
            $producto = Producto::findOrFail($request->idProducto);

            // si quieres usar el modo para algo:
            $modoProducto = $request->input('modo_producto'); // ej: pack_polera_corto / solo_polera / etc.

            // Usar el empleado seleccionado del formulario
            $idEmpleadoSeguro = $request->idEmpleado;

            if (!$idEmpleadoSeguro) {
                throw new \Exception('Debe seleccionar un vendedor/empleado para el pedido.');
            }

            // Crear dirección para el pedido
            $direccion = Direccion::create([
                'direccionPrincipal' => $request->lugarEntrega,
                'referencia' => $request->lugarEntrega,
                'estado' => 1
            ]);

            $subtotal        = 0;
            $itemsCalculados = [];

            foreach ($request->items as $item) {
                $idTallas = $item['idTallas'];
                $cantidad = (int) $item['cantidad'];

                if ($cantidad <= 0) continue;

                // Precio base del producto (sin adicional por talla)
                $precioUnit = (float)($producto->precioVenta ?? 0);
                $sub        = $precioUnit * $cantidad;
                $subtotal  += $sub;

                $itemsCalculados[] = [
                    'idTallas'       => $idTallas,
                    'cantidad'       => $cantidad,
                    'precioUnitario' => $precioUnit,
                    'subtotal'       => $sub,
                    'nombre'         => $item['nombre'] ?? null,
                    'numero'         => $item['numero'] ?? null,
                    'observacion'    => $item['observaciones'] ?? null,
                ];
            }

            if (empty($itemsCalculados)) {
                throw new \Exception('Debes agregar al menos una prenda válida.');
            }

            $total = $subtotal;

            // 👇 Si en tu tabla ventas tienes una columna para guardar el modo, podrías agregarla aquí:
            $venta = Venta::create([
                'subtotal'          => $subtotal,
                'total'             => $total,
                'fechaEntrega'      => $request->fechaEntrega,
                'lugarEntrega'      => $request->lugarEntrega,
                'estadoPedido'      => '0',
                'saldo'             => $total,
                'estado'            => 1,
                'idEmpleado'        => $idEmpleadoSeguro,
                'idCliente'         => $request->tipoCliente === 'natural' ? $request->idCliente : null,
                'idEstablecimiento' => $request->tipoCliente === 'establecimiento' ? $request->idEstablecimiento : null,
                // 'modo_producto'  => $modoProducto, // solo si tienes esta columna en la BD
            ]);

            $primerDetalle                = null;
            $caracteristicasSeleccionadas = $request->input('caracteristicas', []);

            // 🔁 FOREACH CORREGIDO
            foreach ($itemsCalculados as $item) {

                // 1) Guardar en detalle_ventas SOLO lo que le corresponde
                $detalle = DetalleVenta::create([
                    'cantidad'       => $item['cantidad'],
                    'precioUnitario' => $item['precioUnitario'],
                    'descuento'      => 0,
                    'descripcion'    => $item['observacion'],
                    'tipo_pack'      => 'producto',
                    'subtotal'       => $item['subtotal'],
                    'estado'         => 1,
                    'idProducto'     => $producto->idProducto,
                    'idPack'         => null,
                    'idEmpleado'     => $idEmpleadoSeguro,
                    'idVenta'        => $venta->idVenta,
                ]);

                // 2) Relacionar talla + personalización en detalle_tallas
                DetalleTalla::create([
                    'idDetalleVenta' => $detalle->idDetalleVenta,
                    'idTallas'       => $item['idTallas'],
                    'nombre'         => $item['nombre'] ?? null,
                    'numero'         => $item['numero'] ?? null,
                    'adicional'      => null,
                    'estado'         => 1,
                ]);

                // 3) Características (solo 1 vez, sobre el primer detalle)
                if (!empty($caracteristicasSeleccionadas) && !$primerDetalle) {
                    foreach ($caracteristicasSeleccionadas as $idOpcion => $idCaracteristica) {
                        if ($idCaracteristica) {
                            DB::table('variante_caracteristicas')->insert([
                                'iddetalleVenta'  => $detalle->idDetalleVenta,
                                'idCaracteristica' => $idCaracteristica,
                                'created_at'      => now(),
                                'updated_at'      => now(),
                            ]);
                        }
                    }
                }

                if (!$primerDetalle) {
                    $primerDetalle = $detalle;
                }
            }

            $montoAdelanto = (float)($request->montoAdelanto ?? 0);
            if ($montoAdelanto > 0) {
                if ($montoAdelanto > $venta->total) {
                    throw new \Exception('El adelanto no puede ser mayor que el total.');
                }

                Transaccion::create([
                    'tipoTransaccion' => 'pago',
                    'monto'           => $montoAdelanto,
                    'metodoPago'      => $request->tipoTransaccion ?? 'efectivo',
                    'observaciones'   => null,
                    'estado'          => 1,
                    'idVenta'         => $venta->idVenta,
                ]);

                $venta->saldo = max($venta->total - $montoAdelanto, 0);
                $venta->save();
            }

            DB::commit();

            return redirect()->route('pedidos.confirmacion', $venta->idVenta)
                ->with('success', 'Pedido creado exitosamente desde el catálogo');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al guardar pedido desde catálogo', ['error' => $e->getMessage()]);
            return redirect()->back()
                ->with('error', 'Error al guardar el pedido: ' . $e->getMessage())
                ->withInput();
        }
    }



    /**
     * Mostrar formulario de checkout
     */
    public function checkout()
    {
        $carrito = session()->get('carrito', []);

        if (empty($carrito)) {
            return redirect()->route('pedidos.catalogo')
                ->with('error', 'El carrito está vacío');
        }

        // Total del carrito
        $total = collect($carrito)->sum(function ($item) {
            return $item['subtotal'] ?? 0;
        });

        $clientesNaturales = ClienteNatural::where('estado', 1)->with('user')->get();
        $clientesEstablecimientos = ClienteEstablecimiento::where('estado', 1)->with('representante')->get();
        $departamentos = Departamento::where('estado', '1')
            ->orderBy('nombreDepartamento')
            ->get();

        // Cargar valores antiguos para repoblación
        $oldDepartamento = old('idDepartamento');
        $oldProvincia = old('idProvincia');
        $oldMunicipio = old('idMunicipio');

        $provincias = [];
        $municipios = [];

        // Si hay departamento seleccionado (old o por defecto), cargar sus provincias
        $selectedDepartamento = $oldDepartamento ?: request('idDepartamento');
        if ($selectedDepartamento) {
            $provincias = Provincia::where('idDepartamento', $selectedDepartamento)
                ->where('estado', '1')
                ->orderBy('nombreProvincia')
                ->get(['idProvincia', 'nombreProvincia']);
        }

        // Si hay provincia seleccionada (old o por defecto), cargar sus municipios
        $selectedProvincia = $oldProvincia ?: request('idProvincia');
        if ($selectedProvincia) {
            $municipios = Municipio::where('idProvincia', $selectedProvincia)
                ->where('estado', '1')
                ->orderBy('nombreMunicipio')
                ->get(['idMunicipio', 'nombreMunicipio']);
        }

        return view('pedidos.checkout', compact(
            'carrito',
            'total',
            'clientesNaturales',
            'clientesEstablecimientos',
            'departamentos',
            'provincias',
            'municipios',
            'oldDepartamento',
            'oldProvincia',
            'oldMunicipio'
        ));
    }

    public function procesarPedido(Request $request)
    {
        $request->validate([
            'tipoCliente' => 'required|in:natural,establecimiento',
            'idCliente' => 'required_if:tipoCliente,natural',
            'idEstablecimiento' => 'required_if:tipoCliente,establecimiento',
            'fechaEntrega' => 'required|date|after:today',
            'lugarEntrega' => 'required|string|max:200',
            'observaciones' => 'nullable|string|max:500',
            'idMunicipio' => 'required|exists:municipios,idMunicipio', // Asegúrate de tener este campo
            'idProvincia' => 'required|exists:provincias,idProvincia', // Asegúrate de tener este campo
            'idDepartamento' => 'required|exists:departamentos,idDepartamento' // Asegúrate de tener este campo
        ]);

        $carrito = session()->get('carrito', []);

        if (empty($carrito)) {
            return redirect()->route('pedidos.catalogo')
                ->with('error', 'El carrito está vacío');
        }

        DB::beginTransaction();

        try {
            $subtotal = collect($carrito)->sum('subtotal');
            $idEmpleadoSeguro = optional(auth()->user()->empleado)->idEmpleado;

            if (!$idEmpleadoSeguro) {
                $idEmpleadoSeguro = DB::table('empleados')->value('idEmpleado');

                if (!$idEmpleadoSeguro) {
                    throw new \Exception('No existe ningún empleado registrado para asociar la venta. Cree un empleado o asocie uno al usuario actual.');
                }
            }

            // 1. Crear la dirección primero
            $direccion = Direccion::create([
                'direccionPrincipal' => $request->lugarEntrega,
                'referencia' => $request->lugarEntrega,
                'idDepartamento' => $request->idDepartamento,
                'idProvincia' => $request->idProvincia,
                'idMunicipio' => $request->idMunicipio,
                'estado' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // 2. Crear la venta
            $ventaData = [
                'subtotal' => $subtotal,
                'total' => $subtotal, // Asumiendo que no hay descuentos por ahora
                'fechaEntrega' => $request->fechaEntrega,
                'lugarEntrega' => $request->lugarEntrega,
                'estadoPedido' => '0',
                'saldo' => $subtotal,
                'estado' => 1,
                'idEmpleado' => $idEmpleadoSeguro,
                'idDireccion' => $direccion->idDireccion,
                'created_at' => now(),
                'updated_at' => now()
            ];

            // Asignar el cliente correcto según el tipo
            if ($request->tipoCliente === 'natural') {
                $ventaData['idCliente'] = $request->idCliente;
                $ventaData['idEstablecimiento'] = null;
            } else {
                $ventaData['idEstablecimiento'] = $request->idEstablecimiento;
                $ventaData['idCliente'] = null;
            }

            $venta = Venta::create($ventaData);

            // 3. Procesar los ítems del carrito
            foreach ($carrito as $item) {
                // Validar que el ítem tenga los campos necesarios
                if (!isset($item['idProducto'], $item['cantidad'], $item['precioUnitario'], $item['idTallas'])) {
                    throw new \Exception('Formato de ítem de carrito inválido');
                }

                // Crear detalle de venta
                $detalle = DetalleVenta::create([
                    'cantidad' => $item['cantidad'],
                    'precioUnitario' => $item['precioUnitario'],
                    'descuento' => $item['descuento'] ?? 0,
                    'descripcion' => $item['descripcion'] ?? null,
                    'tipo_pack' => 'producto',
                    'subtotal' => $item['subtotal'] ?? ($item['cantidad'] * $item['precioUnitario']),
                    'estado' => 1,
                    'idProducto' => $item['idProducto'],
                    'idEmpleado' => $idEmpleadoSeguro,
                    'idVenta' => $venta->idVenta,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                // Crear detalle de talla
                DetalleTalla::create([
                    'idDetalleVenta' => $detalle->idDetalleVenta,
                    'idTallas' => $item['idTallas'],
                    'nombre' => $item['nombrePersonalizado'] ?? null,
                    'numero' => $item['numeroPersonalizado'] ?? null,
                    'adicional' => $item['adicional'] ?? null,
                    'estado' => 1,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            DB::commit();
            session()->forget('carrito');

            Log::info('Pedido creado exitosamente', [
                'idVenta' => $venta->idVenta,
                'total' => $subtotal,
                'items' => count($carrito)
            ]);

            return redirect()->route('pedidos.confirmacion', $venta->idVenta)
                ->with('success', 'Pedido creado exitosamente');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al procesar pedido', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->with('error', 'Error al procesar el pedido: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function confirmacion($idVenta)
    {
        $venta = Venta::with([
            'detalleVentas.detalleTallas.talla', // <-- así
            'clienteNatural.user',
            'clienteEstablecimiento.representante',
            'transacciones',
            'empleado.user'
        ])->findOrFail($idVenta);

        $tallas = Talla::where('estado', 1)
            ->orderBy('nombre')
            ->get(['idTallas as idTallas', 'nombre']);


        $metodosPago = collect([
            ['id' => null, 'nombre' => 'Efectivo', 'codigo' => 'efectivo'],
            ['id' => null, 'nombre' => 'QR', 'codigo' => 'qr'],
            ['id' => null, 'nombre' => 'Cheque', 'codigo' => 'cheque'],
            ['id' => null, 'nombre' => 'Transferencia bancaria', 'codigo' => 'transferencia'],
        ]);

        return view('pedidos.confirmacion', compact('venta', 'metodosPago', 'tallas'));
    }

    public function getProvincias($idDepartamento)
    {
        $provincias = Provincia::where('idDepartamento', $idDepartamento)
            ->where('estado', '1')
            ->orderBy('nombreProvincia')
            ->get(['idProvincia', 'nombreProvincia']);

        return response()->json($provincias);
    }

    public function getMunicipios($idProvincia)
    {
        $municipios = Municipio::where('idProvincia', $idProvincia)
            ->where('estado', '1')
            ->orderBy('nombreMunicipio')
            ->get(['idMunicipio', 'nombreMunicipio']);

        return response()->json($municipios);
    }


    /**
     * Agregar un detalle de venta desde la confirmación
     */
    public function agregarDetalle(Request $request, $idVenta)
    {
        $request->validate([
            'idTallas' => 'required|exists:tallas,idTallas',
            'cantidad' => 'required|integer|min:1',
            'precioUnitario' => 'required|numeric|min:0',
            'nombrePersonalizado' => 'nullable|string|max:50',
            'numeroPersonalizado' => 'nullable|string|max:10',
            'textoAdicional' => 'nullable|string|max:200',
            'observacion' => 'nullable|string|max:500',
            'descripcion' => 'nullable|string|max:200',
            'tipoTransaccion' => 'nullable|in:efectivo,qr,cheque,transferencia',
            'montoAdelanto' => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $venta = Venta::with('transacciones')->findOrFail($idVenta);

            $idEmpleadoSeguro = optional(optional(auth()->user())->empleado)->idEmpleado;
            if (!$idEmpleadoSeguro) {
                $idEmpleadoSeguro = DB::table('empleados')->value('idEmpleado');
            }
            if (!$idEmpleadoSeguro) {
                return back()->with('error', 'No existe ningún empleado registrado para asociar el detalle.');
            }

            $rawDelete = $request->input('delete_ids');
            $deleteIds = collect([]);
            if (is_array($rawDelete)) {
                $deleteIds = collect($rawDelete);
            } elseif (is_string($rawDelete) && trim($rawDelete) !== '') {
                $deleteIds = collect(explode(',', $rawDelete));
            }
            $deleteIds = $deleteIds->map(fn($v) => (int) $v)->filter();
            if ($deleteIds->isNotEmpty()) {
                DetalleVenta::where('idVenta', $venta->idVenta)
                    ->whereIn('iddetalleVenta', $deleteIds->all())
                    ->delete();
            }

            $ids = $request->input('row_id', []);
            $tallas = $request->input('idTallas', []);
            $cantidades = $request->input('cantidad', []);
            $precios = $request->input('precioUnitario', []);
            $nombres = $request->input('nombrePersonalizado', []);
            $numeros = $request->input('numeroPersonalizado', []);
            $observs = $request->input('observacion', []);
            $descrs = $request->input('descripcion', []);

            foreach ($ids as $i => $rowId) {
                $dataDet = [
                    'cantidad' => (int) ($cantidades[$i] ?? 0),
                    'nombrePersonalizado' => $nombres[$i] ?? null,
                    'numeroPersonalizado' => $numeros[$i] ?? null,
                    'textoAdicional' => null,
                    'observacion' => $observs[$i] ?? null,
                    'descripcion' => $descrs[$i] ?? null,
                    'precioUnitario' => (float) ($precios[$i] ?? 0),
                    'estado' => 1,
                    'idTallas' => (int) ($tallas[$i] ?? 0),
                    'idVenta' => $venta->idVenta,
                    'idEmpleado' => $idEmpleadoSeguro,
                ];

                if ($dataDet['cantidad'] <= 0) {
                    continue;
                }

                if ($rowId) {
                    $det = DetalleVenta::where('idVenta', $venta->idVenta)
                        ->where('iddetalleVenta', $rowId)
                        ->first();
                    if ($det) {
                        $det->update($dataDet);
                    }
                } else {
                    DetalleVenta::create($dataDet);
                }
            }

            $nuevoSubtotal = DetalleVenta::where('idVenta', $venta->idVenta)
                ->selectRaw('COALESCE(SUM(cantidad * precioUnitario),0) as s')
                ->value('s');
            $venta->subtotal = $nuevoSubtotal;
            $venta->total = $nuevoSubtotal;

            $montoAd = (float) ($request->montoAdelanto ?? 0);
            $tipoPago = $request->tipoTransaccion;
            if ($montoAd > 0) {
                if ($montoAd > $venta->total) {
                    throw new \Exception('El adelanto no puede ser mayor que el total.');
                }
                Transaccion::create([
                    'tipoTransaccion' => 'pago',
                    'monto' => $montoAd,
                    'metodoPago' => $tipoPago ?? 'efectivo',
                    'observaciones' => null,
                    'estado' => 1,
                    'idVenta' => $venta->idVenta,
                ]);
            }

            $pagos = $venta->transacciones
                ->where('tipoTransaccion', 'pago')
                ->sum('monto');
            $venta->saldo = max($venta->total - (float) $pagos, 0);
            $venta->save();

            DB::commit();
            return redirect()->route('pedidos.confirmacion', $venta->idVenta)
                ->with('success', 'Detalle agregado correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al agregar detalle', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'No se pudo agregar el detalle: ' . $e->getMessage());
        }
    }

    /**
     * Eliminar imagen del pedido vía AJAX
     */


    public function agregarAlCarrito(Request $request)
    {
        // Validación
        $request->validate([
            'idProducto'        => 'required|exists:productos,idProducto',
            'modo_producto'     => 'nullable|string|max:50',
            'items'             => 'required|array|min:1',
            'items.*.idTallas'  => 'required|exists:tallas,idTallas',
            'items.*.cantidad'  => 'required|integer|min:1',
            'items.*.nombre'    => 'nullable|string|max:100',
            'items.*.numero'    => 'nullable|string|max:10',
        ]);

        $producto     = Producto::findOrFail($request->idProducto);
        $modoProducto = $request->input('modo_producto');

        // 👇 NUEVO: resolver imagen del producto
        $imagen = null;
        if (!empty($producto->foto)) {
            // columna foto en productos (por ej: 'productos/foto.jpg')
            $imagen = $producto->foto;
        } elseif ($producto->diseno && !empty($producto->diseno->archivo)) {
            // si tiene relación diseno
            $imagen = $producto->diseno->archivo;
        }

        // Traer carrito actual de sesión
        $carrito = session()->get('carrito', []);

        foreach ($request->items as $item) {
            $talla = Talla::findOrFail($item['idTallas']);

            // Precio base del producto (sin adicional por talla)
            $precioUnit = (float) ($producto->precioVenta ?? 0);
            $cantidad   = (int) $item['cantidad'];
            $subtotal   = $precioUnit * $cantidad;

            $carrito[] = [
                'idProducto'          => $producto->idProducto,
                'producto'            => $producto->nombre,
                'modo_producto'       => $modoProducto,
                'idTallas'            => $talla->idTallas,
                'talla'               => $talla->nombre,
                'cantidad'            => $cantidad,
                'precioUnitario'      => $precioUnit,
                'subtotal'            => $subtotal,
                'nombrePersonalizado' => $item['nombre'] ?? null,
                'numeroPersonalizado' => $item['numero'] ?? null,

                // 👇 NUEVO: guardamos solo la ruta relativa (sin asset())
                'imagen'              => $imagen,
            ];
        }

        // Guardar carrito actualizado en sesión
        session()->put('carrito', $carrito);

        // Renderizar el HTML del carrito lateral
        $htmlCarrito = view('pedidos.carrito-lateral-contenido', [
            'carrito' => $carrito,
            'total'   => collect($carrito)->sum('subtotal'),
        ])->render();

        // Calcular items por producto (no por talla)
        $itemsPorProducto = collect($carrito)
            ->groupBy('idProducto')
            ->count();

        // Respuesta para el JS (fetch)
        return response()->json([
            'ok'               => true,
            'mensaje'          => 'Producto(s) agregado(s) al carrito.',
            'html_carrito'     => $htmlCarrito,
            'items_en_carrito' => $itemsPorProducto,
        ]);
    }

    public function eliminarDelCarrito($index)
    {
        $carrito = session()->get('carrito', []);

        if (!isset($carrito[$index])) {
            return response()->json(['ok' => false, 'mensaje' => 'Ítem no encontrado en el carrito'], 404);
        }

        unset($carrito[$index]);
        $carrito = array_values($carrito); // reindexar
        session()->put('carrito', $carrito);

        $htmlCarrito = view('pedidos.carrito-lateral-contenido', [
            'carrito' => $carrito,
            'total'   => collect($carrito)->sum('subtotal'),
        ])->render();

        // Calcular items por producto (no por talla)
        $itemsPorProducto = collect($carrito)
            ->groupBy('idProducto')
            ->count();

        return response()->json([
            'ok'           => true,
            'html_carrito' => $htmlCarrito,
            'items_en_carrito' => $itemsPorProducto,
        ]);
    }

    public function eliminarProductoDelCarrito($idProducto)
    {
        $carrito = session()->get('carrito', []);

        // Filtrar carrito para eliminar todos los items del producto
        $carritoFiltrado = array_filter($carrito, function ($item) use ($idProducto) {
            return $item['idProducto'] != $idProducto;
        });

        // Reindexar array
        $carritoFiltrado = array_values($carritoFiltrado);

        // Guardar carrito actualizado
        session()->put('carrito', $carritoFiltrado);

        // Renderizar HTML
        $htmlCarrito = view('pedidos.carrito-lateral-contenido', [
            'carrito' => $carritoFiltrado,
            'total'   => collect($carritoFiltrado)->sum('subtotal'),
        ])->render();

        // Calcular items por producto (no por talla)
        $itemsPorProducto = collect($carritoFiltrado)
            ->groupBy('idProducto')
            ->count();

        return response()->json([
            'ok'           => true,
            'html_carrito' => $htmlCarrito,
            'items_en_carrito' => $itemsPorProducto,
        ]);
    }

    public function vaciarCarrito()
    {
        session()->forget('carrito');

        $htmlCarrito = view('pedidos.carrito-lateral-contenido', [
            'carrito' => [],
            'total'   => 0,
        ])->render();

        return response()->json([
            'ok'           => true,
            'html_carrito' => $htmlCarrito,
            'items_en_carrito' => 0,
        ]);
    }

    public function eliminarImagen($idVenta)
    {
        try {
            $pedido = Venta::findOrFail($idVenta);
            $diseno = $pedido->disenos->first();

            if ($diseno) {
                Storage::delete('public/' . $diseno->archivo);
                $diseno->delete();
                Log::info('Imagen eliminada vía AJAX', ['idVenta' => $idVenta]);
                return response()->json(['success' => true, 'message' => 'Imagen eliminada correctamente']);
            } else {
                return response()->json(['success' => false, 'message' => 'No se encontró imagen asociada'], 404);
            }
        } catch (\Exception $e) {
            Log::error('Error al eliminar imagen vía AJAX', ['idVenta' => $idVenta, 'error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Error al eliminar la imagen'], 500);
        }
    }

    /**
     * Listar todos los pedidos
     */
    public function index()
    {
        // Cargar relaciones necesarias
        $pedidos = Venta::with([
            'clienteNatural.user', // Asegurarse de cargar el usuario del cliente natural
            'clienteEstablecimiento.representante', // Cargar el representante del establecimiento
            'empleado.user', // Cargar el usuario del empleado
            'detalleVentas' => function ($query) {
                $query->with([
                    'producto',
                    'diseno',
                    'tallas' // Asegurarse de cargar las tallas si las necesitas
                ]);
            },
            'disenos.empleado.user' // Cargar el diseñador y su usuario
        ])
            ->where('estado', 1) // Solo pedidos activos
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Contador para la numeración
        $contador = ($pedidos->currentPage() - 1) * $pedidos->perPage() + 1;

        // Agregar contador a cada pedido
        $pedidos->each(function ($pedido) use (&$contador) {
            $pedido->contador = $contador++;

            // Calcular total pagado y saldo pendiente
            $pedido->total_pagado = $pedido->transacciones->sum('monto');
            $pedido->saldo_pendiente = $pedido->total - $pedido->total_pagado;
        });

        return view('pedidos.index', compact('pedidos'));
    }
    /**
     * Ver detalle de pedido
     */
    /**
     * Mostrar detalles de un pedido
     */
    public function show($pedido)
    {
        $pedido = Venta::with([
            'detalleVentas.tallas',
            'detalleVentas.diseno',
            'clienteNatural.user',
            'clienteEstablecimiento.representante',
            'empleado.user',
            'transacciones' // Asumiendo que tienes una relación transacciones
        ])->findOrFail($pedido);

        // Verificar si hay un diseño temporal cargado en la sesión
        $disenoUrl = null;
        if (session()->has('disenoTemporal')) {
            $disenoUrl = asset('storage/' . session()->get('disenoTemporal'));
        }

        // Calcular total pagado
        $totalPagado = $pedido->transacciones->sum('monto');
        $saldoPendiente = $pedido->total - $totalPagado;

        return view('pedidos.show', compact('pedido', 'disenoUrl', 'totalPagado', 'saldoPendiente'));
    }

    
public function edit($id)
{
    // Obtener el pedido con sus relaciones
    $pedido = Venta::with([
        'detalleVentas.producto',
        'detalleVentas.detalleTallas.talla',
        'clienteNatural.user',
        'clienteEstablecimiento.user',
        'disenos.empleado.user'
    ])->findOrFail($id);

    // Obtener el primer producto del pedido (si existe)
    $producto = $pedido->detalleVentas->first()?->producto;

    // Inicializar variables para packs
    $esPack = false;
    $pack = null;
    $variantesPack = collect();

    // Obtener datos para los selects
    $diseñadores = Empleado::with('user')
        ->where('rol', 'diseñador')
        ->where('estado', 1)
        ->get();

    $productos = Producto::where('estado', 1)->get();
    $tallas = Talla::where('estado', 1)->get();
    $clientesNaturales = ClienteNatural::with('user')->where('estado', 1)->get();
    $clientesEstablecimientos = ClienteEstablecimiento::with('user')->where('estado', 1)->get();

    // Modos de producto
    $modosProducto = [
        ['key' => 'normal', 'label' => 'Normal', 'icon' => 'tshirt'],
        ['key' => 'estandar', 'label' => 'Estándar', 'icon' => 'tshirt'],
        ['key' => 'personalizado', 'label' => 'Personalizado', 'icon' => 'paint-brush'],
    ];

    // Establecer modo seleccionado (puedes ajustar según necesites)
    $modoSeleccionado = 'normal';

    return view('pedidos.edit', compact(
        'pedido',
        'producto',
        'productos',
        'tallas',
        'clientesNaturales',
        'clientesEstablecimientos',
        'diseñadores',
        'esPack',
        'pack',
        'variantesPack',
        'modosProducto',
        'modoSeleccionado'
    ));
}
    /**
     * Actualizar pedido
     */
    public function update(Request $request, $idVenta)
    {
        DB::beginTransaction();

        try {
            // Validación de los campos del pedido
            $validated = $request->validate([
                'fechaEntrega' => 'required|date|after_or_equal:today',
                'lugarEntrega' => 'required|string|max:200',
                'estadoPedido' => 'required|in:0,1,2,3,4',
                'imagenPedido' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
                'tipoCliente' => 'required|string',
                'idEmpleado' => 'required|exists:empleados,idEmpleado',
                'observaciones' => 'nullable|string|max:500',
            ]);

            // Buscar el pedido con relaciones necesarias
            $pedido = Venta::with('disenos', 'detalleVentas.diseno')->findOrFail($idVenta);

            // Actualizar datos básicos
            $pedido->fill([
                'fechaEntrega' => $validated['fechaEntrega'],
                'lugarEntrega' => $validated['lugarEntrega'],
                'estadoPedido' => (string) $validated['estadoPedido'],
                'observaciones' => $validated['observaciones'] ?? null,
            ]);

            // Actualizar relación con cliente
            $this->actualizarCliente($pedido, $validated['tipoCliente']);

            // Asignar empleado (diseñador)
            $pedido->idEmpleado = $validated['idEmpleado'];

            // Manejar la imagen del pedido
            $this->manejarImagenPedido($pedido, $request);

            // Guardar cambios
            $pedido->save();

            DB::commit();

            return redirect()
                ->route('pedidos.index')
                ->with('success', 'Pedido actualizado correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al actualizar pedido: ' . $e->getMessage(), [
                'exception' => $e,
                'pedido_id' => $idVenta,
                'request' => $request->all()
            ]);

            return back()
                ->withInput()
                ->with('error', 'Error al actualizar el pedido: ' . $e->getMessage());
        }
    }

    /**
     * Actualiza la relación del pedido con el cliente
     */
    protected function actualizarCliente(Venta $pedido, string $tipoCliente): void
    {
        if (str_starts_with($tipoCliente, 'natural:')) {
            $pedido->idCliente = explode(':', $tipoCliente)[1];
            $pedido->idEstablecimiento = null;
        } elseif (str_starts_with($tipoCliente, 'establecimiento:')) {
            $pedido->idEstablecimiento = explode(':', $tipoCliente)[1];
            $pedido->idCliente = null;
        }
    }

    /**
     * Maneja la carga/eliminación de la imagen del pedido
     */
    protected function manejarImagenPedido(Venta $pedido, Request $request): void
    {
        // Eliminar imagen existente si se marca el checkbox
        if ($request->boolean('delete_imagen')) {
            $this->eliminarImagenExistente($pedido);
            return;
        }

        // Subir nueva imagen si se proporciona
        if ($request->hasFile('imagenPedido')) {
            // Eliminar imagen anterior si existe
            $this->eliminarImagenExistente($pedido);

            // Subir nueva imagen
            $path = $request->file('imagenPedido')->store('imagenes_pedidos', 'public');

            // Crear registro de diseño
            $primerDetalle = $pedido->detalleVentas->first();
            if ($primerDetalle) {
                Diseno::create([
                    'archivo' => $path,
                    'iddetalleVenta' => $primerDetalle->iddetalleVenta,
                    'estado' => 1,
                    'idEmpleado' => $pedido->idEmpleado
                ]);
            }
        }
    }

    /**
     * Elimina la imagen existente del pedido
     */
    protected function eliminarImagenExistente(Venta $pedido): void
    {
        $diseno = $pedido->disenos->first();
        if ($diseno) {
            Storage::delete('public/' . $diseno->archivo);
            $diseno->delete();
        }
    }

    /**
     * Actualizar detalles del pedido
     */
    public function updateDetalles(Request $request, $idVenta)
    {
        $pedido = Venta::with('transacciones')->findOrFail($idVenta);

        $request->validate([
            'idProducto' => 'nullable|exists:productos,idProducto',
            'row_id' => 'required|array',
            'idTallas' => 'required|array|min:1',
            'idTallas.*' => 'required|exists:tallas,idTallas',
            'cantidad' => 'required|array|min:1',
            'cantidad.*' => 'required|integer|min:1',
            'precioUnitario' => 'required|array|min:1',
            'precioUnitario.*' => 'required|numeric|min:0',
            'nombrePersonalizado' => 'nullable|array',
            'numeroPersonalizado' => 'nullable|array',
            'observacion' => 'nullable|array',
            'descripcion' => 'nullable|array',
            'tipoTransaccion' => 'nullable|in:efectivo,qr,cheque,transferencia',
            'montoAdelanto' => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $rawDelete = (string) $request->input('delete_ids', '');
            $deleteIds = collect(array_filter(array_map('trim', explode(',', $rawDelete))))
                ->map(fn($v) => (int) $v)
                ->filter();

            if ($deleteIds->isNotEmpty()) {
                DetalleVenta::where('idVenta', $pedido->idVenta)
                    ->whereIn('iddetalleVenta', $deleteIds->all())
                    ->delete();
            }

            $ids = $request->input('row_id', []);
            $tallas = $request->input('idTallas', []);
            $cantidades = $request->input('cantidad', []);
            $precios = $request->input('precioUnitario', []);
            $nombres = $request->input('nombrePersonalizado', []);
            $numeros = $request->input('numeroPersonalizado', []);
            $observs = $request->input('observacion', []);
            $descrs = $request->input('descripcion', []);

            $idEmpleadoSeguro = optional(optional(auth()->user())->empleado)->idEmpleado
                ?? DB::table('empleados')->value('idEmpleado');

            if (!$idEmpleadoSeguro) {
                return back()->with('error', 'No existe ningún empleado registrado para asociar los detalles.');
            }

            foreach ($ids as $i => $rowId) {
                $dataDet = [
                    'cantidad' => (int) ($cantidades[$i] ?? 0),
                    'nombrePersonalizado' => $nombres[$i] ?? null,
                    'numeroPersonalizado' => $numeros[$i] ?? null,
                    'textoAdicional' => null,
                    'observacion' => $observs[$i] ?? null,
                    'descripcion' => $descrs[$i] ?? null,
                    'precioUnitario' => (float) ($precios[$i] ?? 0),
                    'estado' => 1,
                    'idTallas' => (int) ($tallas[$i] ?? 0),
                    'idVenta' => $pedido->idVenta,
                    'idEmpleado' => $idEmpleadoSeguro,
                ];

                if ($dataDet['cantidad'] <= 0) {
                    continue;
                }

                if (!empty($rowId)) {
                    $det = DetalleVenta::where('idVenta', $pedido->idVenta)
                        ->where('iddetalleVenta', $rowId)
                        ->first();
                    if ($det) {
                        $det->update($dataDet);
                    }
                } else {
                    DetalleVenta::create($dataDet);
                }
            }

            $nuevoSubtotal = DetalleVenta::where('idVenta', $pedido->idVenta)
                ->selectRaw('COALESCE(SUM(cantidad * precioUnitario),0) as s')
                ->value('s');

            $pedido->subtotal = $nuevoSubtotal;
            $pedido->total = $nuevoSubtotal;

            $montoAd = (float) ($request->montoAdelanto ?? 0);
            $tipoPago = $request->tipoTransaccion;
            if ($montoAd > 0) {
                if ($montoAd > $pedido->total) {
                    throw new \Exception('El adelanto no puede ser mayor que el total.');
                }
                Transaccion::create([
                    'tipoTransaccion' => 'pago',
                    'monto' => $montoAd,
                    'metodoPago' => $tipoPago ?? 'efectivo',
                    'observaciones' => null,
                    'estado' => 1,
                    'idVenta' => $pedido->idVenta,
                ]);
            }

            $pagosAcumulados = Transaccion::where('idVenta', $pedido->idVenta)
                ->where('tipoTransaccion', 'pago')
                ->sum('monto');

            $pedido->saldo = max($pedido->total - (float) $pagosAcumulados, 0);
            $pedido->save();

            DB::commit();

            return redirect()->route('pedidos.edit', $pedido->idVenta)
                ->with('success', 'Detalles actualizados correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al actualizar detalles', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'No se pudo actualizar los detalles: ' . $e->getMessage());
        }
    }

    /**
     * API: Búsqueda unificada de clientes
     */
    public function apiBuscarClientes(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        if ($q === '') {
            return response()->json(['results' => []]);
        }

        $qLike = '%' . $q . '%';

        $naturales = ClienteNatural::query()
            ->where('cliente_naturals.estado', 1)
            ->leftJoin('users', 'cliente_naturals.idCliente', '=', 'users.idUser')
            ->where(function ($w) use ($qLike) {
                $w->where('users.ci', 'like', $qLike)
                    ->orWhere('users.name', 'like', $qLike)
                    ->orWhere('users.telefono', 'like', $qLike)
                    ->orWhere('cliente_naturals.nit', 'like', $qLike);
            })
            ->orderBy('users.name')
            ->limit(15)
            ->get([
                'cliente_naturals.idCliente',
                'cliente_naturals.nit',
                'users.ci',
                'users.name',
                'users.telefono',
            ])
            ->map(function ($row) {
                $doc = $row->ci ?: $row->nit;
                $label = trim(($doc ? 'CI: ' . $doc . ' - ' : '') . ($row->name ?: 'Cliente') . ($row->telefono ? ' - Tel: ' . $row->telefono : ''));
                return [
                    'type' => 'natural',
                    'value' => 'natural:' . $row->idCliente,
                    'label' => $label,
                ];
            });

        $establecimientos = ClienteEstablecimiento::query()
            ->where('cliente_establecimientos.estado', 1)
            ->leftJoin('users', 'cliente_establecimientos.idRepresentante', '=', 'users.idUser')
            ->where(function ($w) use ($qLike) {
                $w->where('cliente_establecimientos.nit', 'like', $qLike)
                    ->orWhere('cliente_establecimientos.razonSocial', 'like', $qLike)
                    ->orWhere('users.name', 'like', $qLike)
                    ->orWhere('users.telefono', 'like', $qLike);
            })
            ->orderBy('cliente_establecimientos.razonSocial')
            ->limit(15)
            ->get([
                'cliente_establecimientos.idEstablecimiento',
                'cliente_establecimientos.nit',
                'cliente_establecimientos.razonSocial',
                'users.telefono',
                'users.name as representante',
            ])
            ->map(function ($row) {
                $doc = $row->nit ?: '';
                $nom = $row->razonSocial ?: 'Establecimiento';
                $tel = $row->telefono ?: '';
                $label = trim(($doc ? 'NIT: ' . $doc . ' - ' : '') . $nom . ($tel ? ' - Tel: ' . $tel : ''));
                return [
                    'type' => 'establecimiento',
                    'value' => 'establecimiento:' . $row->idEstablecimiento,
                    'label' => $label,
                ];
            });

        $results = $naturales->concat($establecimientos)->values();

        return response()->json(['results' => $results]);
    }

    /**
     * Actualizar estado de pedido (AJAX)
     */
    public function actualizarEstado(Request $request, $idVenta)
    {
        try {
            $request->validate([
                'estadoPedido' => 'required|in:0,1,2,3,4'
            ]);

            $pedido = Venta::findOrFail($idVenta);
            $estadoAnterior = $pedido->estadoPedido;

            $pedido->estadoPedido = $request->estadoPedido;
            $pedido->save();

            Log::info('Estado de pedido actualizado', [
                'idVenta' => $idVenta,
                'estadoAnterior' => $estadoAnterior,
                'estadoNuevo' => $request->estadoPedido
            ]);

            $estados = [
                '0' => 'En Diseño',
                '1' => 'Producción',
                '2' => 'Terminado',
                '3' => 'Entregado',
                '4' => 'Cancelado'
            ];

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Estado actualizado correctamente',
                    'estadoNuevo' => $estados[$request->estadoPedido] ?? 'Desconocido',
                    'pedidoId' => $idVenta
                ]);
            }

            return redirect()->back()
                ->with('success', 'Estado del pedido actualizado exitosamente');
        } catch (\Exception $e) {
            Log::error('Error al actualizar estado de pedido', [
                'idVenta' => $idVenta,
                'error' => $e->getMessage()
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al actualizar el estado: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Error al actualizar el estado: ' . $e->getMessage());
        }
    }
    public function procesarCheckout(Request $request)
    {
        $carrito = session()->get('carrito', []);

        if (empty($carrito)) {
            return redirect()->route('pedidos.catalogo')
                ->with('error', 'El carrito está vacío');
        }

        // 1) Validar datos del formulario - CORREGIDO
        $request->validate([
            'clienteSeleccionado' => 'required|string',
            'fechaEntrega'        => 'required|date|after:today',
            'tipoTransaccion'     => 'required|in:efectivo,qr,cheque,transferencia',
            'montoAdelanto'       => 'nullable|numeric|min:0',
            'idDepartamento'      => 'required|integer|exists:departamentos,idDepartamento',
            'idProvincia'         => 'required|integer|exists:provincias,idProvincia',
            'idMunicipio'         => 'required|integer|exists:municipios,idMunicipio',
            'nombreDireccion'     => 'required|string|max:255',
        ]);

        // 2) Validar y procesar cliente - CORREGIDO
        if (!str_contains($request->clienteSeleccionado, ':')) {
            return back()->with('error', 'Seleccione un cliente válido')->withInput();
        }

        [$tipoCliente, $idRaw] = explode(':', $request->clienteSeleccionado);

        $idCliente = null;
        $idEstablecimiento = null;

        if ($tipoCliente === 'natural') {
            $idCliente = (int) $idRaw;
            // Verificar que el cliente natural existe
            if (!ClienteNatural::where('idCliente', $idCliente)->where('estado', 1)->exists()) {
                return back()->with('error', 'El cliente seleccionado no existe')->withInput();
            }
        } elseif ($tipoCliente === 'establecimiento') {
            $idEstablecimiento = (int) $idRaw;
            // Verificar que el establecimiento existe
            if (!ClienteEstablecimiento::where('idEstablecimiento', $idEstablecimiento)->where('estado', 1)->exists()) {
                return back()->with('error', 'El establecimiento seleccionado no existe')->withInput();
            }
        } else {
            return back()->with('error', 'Tipo de cliente inválido')->withInput();
        }

        // 3) Totales del carrito
        $total    = collect($carrito)->sum(fn($item) => $item['subtotal'] ?? 0);
        $adelanto = (float) $request->input('montoAdelanto', 0);

        if ($adelanto < 0) {
            $adelanto = 0;
        }
        if ($adelanto > $total) {
            $adelanto = $total;
        }

        $saldo = max($total - $adelanto, 0);

        DB::beginTransaction();

        try {
            // 4.1 Crear dirección normalizada
            $direccion = Direccion::create([
                'nombreDireccion' => $request->nombreDireccion,
                'estado'          => '1',
                'idMunicipio'     => $request->idMunicipio,
            ]);

            // 4.2 Resolver idEmpleado seguro
            $idEmpleadoSeguro = optional(optional(auth()->user())->empleado)->idEmpleado
                ?? \DB::table('empleados')->where('estado', 1)->value('idEmpleado');

            if (!$idEmpleadoSeguro) {
                throw new \Exception('No existe ningún empleado activo para asociar la venta.');
            }

            // 4.3 Crear venta
            $venta = Venta::create([
                'subtotal'          => $total,
                'total'             => $total,
                'fechaEntrega'      => $request->fechaEntrega,
                'estadoPedido'      => 0,    // 0: Solicitado / En diseño
                'saldo'             => $saldo,
                'estado'            => '1',
                'idCliente'         => $idCliente,
                'idEstablecimiento' => $idEstablecimiento,
                'idDireccion'       => $direccion->idDireccion,
                'idEmpleado'        => $idEmpleadoSeguro,
            ]);

            // 4.4 Crear DetalleVenta + DetalleTalla a partir del carrito
            foreach ($carrito as $item) {
                // Guardar fila en detalle_ventas
                $detalle = DetalleVenta::create([
                    'cantidad'       => $item['cantidad'],
                    'precioUnitario' => $item['precioUnitario'],
                    'descuento'      => 0,
                    'descripcion'    => null,
                    'tipo_pack'      => 'producto',
                    'subtotal'       => $item['subtotal'],
                    'estado'         => 1,
                    'idProducto'     => $item['idProducto'],
                    'idPack'         => null,
                    'idEmpleado'     => $idEmpleadoSeguro,
                    'idVenta'        => $venta->idVenta,
                ]);

                // Guardar talla/personalización en detalle_tallas
                DetalleTalla::create([
                    'idDetalleVenta' => $detalle->idDetalleVenta,
                    'idTallas'       => $item['idTallas'] ?? null,
                    'nombre'         => $item['nombrePersonalizado'] ?? null,
                    'numero'         => $item['numeroPersonalizado'] ?? null,
                    'adicional'      => null,
                    'estado'         => 1,
                ]);
            }

            // 4.5 Registrar transacción de adelanto (si corresponde)
            if ($adelanto > 0) {
                Transaccion::create([
                    'tipoTransaccion' => 'pago',
                    'monto'           => $adelanto,
                    'metodoPago'      => $request->tipoTransaccion,
                    'observaciones'   => 'Adelanto registrado desde checkout',
                    'estado'          => 1,
                    'idVenta'         => $venta->idVenta,
                ]);
            }

            DB::commit();

            // 5) Limpiar carrito
            session()->forget('carrito');

            return redirect()->route('pedidos.confirmacion', $venta->idVenta)
                ->with('success', 'Pedido procesado correctamente. Nº Venta: ' . $venta->idVenta);
        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);

            return back()
                ->with('error', 'Ocurrió un error al procesar el pedido: ' . $e->getMessage())
                ->withInput();
        }
    }


    /**
     * Registrar un pago para una venta
     */
    public function registrarPago(Request $request, $idVenta)
    {
        // Obtener la venta con el saldo actual
        $venta = Venta::findOrFail($idVenta);
        $saldoPendiente = (float) $venta->saldo;

        $validated = $request->validate([
            'monto' => [
                'required',
                'numeric',
                'min:0.01',
                'max:' . $saldoPendiente,
                function ($attribute, $value, $fail) use ($saldoPendiente) {
                    if ((float)$value > $saldoPendiente) {
                        $fail('El monto no puede ser mayor al saldo pendiente: $' . number_format($saldoPendiente, 2));
                    }
                }
            ],
            'metodoPago' => 'required|string|max:50',
            'observaciones' => 'nullable|string|max:255'
        ]);

        try {
            DB::beginTransaction();

            // Crear la transacción
            $transaccion = new Transaccion([
                'idVenta' => $venta->idVenta,
                'monto' => $validated['monto'],
                'fecha' => now(),
                'metodoPago' => $validated['metodoPago'],
                'observaciones' => $validated['observaciones'] ?? null,
                'estado' => 1,
            ]);
            $transaccion->save();

            // Actualizar saldo
            $venta->saldo = max(0, $venta->saldo - $validated['monto']);

            // Actualizar estado de pago usando estadoPedido
            if ($venta->saldo <= 0) {
                $venta->estadoPedido = 4; // Pagado
            } else {
                $venta->estadoPedido = 5; // Pago parcial
            }

            $venta->save();

            DB::commit();

            return redirect()
                ->route('pedidos.confirmacion', $venta->idVenta)
                ->with('success', 'Pago registrado correctamente. Saldo restante: $' . number_format($venta->saldo, 2));
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al registrar pago: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error al registrar el pago: ' . $e->getMessage());
        }
    }


    /**
     * Eliminar un pedido
     */
    public function destroy($idVenta)
    {
        DB::beginTransaction();
        try {
            $venta = Venta::findOrFail($idVenta);

            // Eliminación lógica
            $venta->estado = 0;
            $venta->save();

            DB::commit();
            return redirect()
                ->route('pedidos.index')
                ->with('success', 'Pedido eliminado correctamente.'); // Cambié 'successdelete' a 'success' para mantener consistencia
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al eliminar (lógico) pedido', [
                'idVenta' => $idVenta,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()
                ->route('pedidos.index')
                ->with('error', 'No se pudo eliminar el pedido: ' . $e->getMessage());
        }
    }

    /**
     * Mostrar los pedidos asignados al diseñador actual
     */
    public function pedidosAsignados(Request $request)
    {
        $idEmpleado = auth()->user()->empleado->idEmpleado;

        $query = Venta::with(['clienteNatural', 'clienteEstablecimiento', 'detalles'])
            ->whereHas('disenos', function ($q) use ($idEmpleado) {
                $q->where('disenos.idEmpleado', $idEmpleado);
            });

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        $pedidos = $query->latest()->paginate(10);

        return view('pedidos.asignados', compact('pedidos'));
    }

    /**
     * Crear nuevo pedido
     */
    public function create()
    {
        $diseñadores = Empleado::with('user')
            ->where('rol', 'diseñador')
            ->where('estado', 1)
            ->get();

        $productos = Producto::where('estado', 1)->get();
        $tallas = Talla::where('estado', 1)->get();
        $clientesNaturales = ClienteNatural::with('user')->where('estado', 1)->get();
        $clientesEstablecimientos = ClienteEstablecimiento::with('user')->where('estado', 1)->get();

        return view('pedidos.nuevo', compact(
            'productos',
            'tallas',
            'clientesNaturales',
            'clientesEstablecimientos',
            'diseñadores'
        ));
    }

    /**
     * Mostrar el carrito de compras
     */
    public function verCarrito()
    {
        $carrito = session()->get('carrito', []);
        $total = collect($carrito)->sum('subtotal');

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'carrito' => $carrito,
                'total' => $total,
                'count' => count($carrito),
                'html' => view('pedidos.carrito-lateral-contenido', compact('carrito', 'total'))->render()
            ]);
        }

        return view('pedidos.carrito', compact('carrito', 'total'));
    }
}
