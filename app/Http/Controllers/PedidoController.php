<?php

namespace App\Http\Controllers;

use App\Models\Venta; // Assuming your main orders table is called 'ventas'
use Barryvdh\DomPDF\Facade\Pdf;

class PedidoPdfController extends Controller
{
    public function generarRecibo($id)
    {
        $venta = Venta::with([
            'detalleVentas.detalleTallas.talla',
            'clienteNatural.user',
            'clienteEstablecimiento.user',
            'disenos.empleado.user',
            'detalleVentas.producto',
            'direccion.municipio.provincia.departamento' // Cargar relación de dirección completa
        ])->findOrFail($id);

        $pdf = PDF::loadView('pdf.recibo-pedido', ['pedido' => $venta]);
        return $pdf->download("recibo-venta-{$venta->idVenta}.pdf");
    }

    public function verRecibo($id)
    {
        $venta = Venta::with([
            'detalleVentas.detalleTallas.talla',
            'detalleVentas.producto.variante.varianteCaracteristicas.caracteristica.opcion',
            'clienteNatural.user',
            'clienteEstablecimiento.user',
            'disenos.empleado.user',
            'direccion.municipio.provincia.departamento'
        ])->findOrFail($id);

        // Procesar características de la variante
        foreach ($venta->detalleVentas as $detalle) {
            $caracteristicas = [];

            if ($detalle->producto && $detalle->producto->variante) {
                foreach ($detalle->producto->variante->varianteCaracteristicas as $vc) {
                    if ($vc->caracteristica && $vc->caracteristica->opcion) {
                        $caracteristicas[] = [
                            'opcion' => $vc->caracteristica->opcion->nombre,
                            'valor' => $vc->caracteristica->nombre,
                            'precio_extra' => $vc->precioAdicional ?? 0
                        ];
                    }
                }
            }

            $detalle->caracteristicasSeleccionadas = $caracteristicas;
        }

        $pdf = PDF::loadView('pdf.recibo-pedido', ['pedido' => $venta]);
        return $pdf->stream("recibo-venta-{$venta->idVenta}.pdf");
    }
}