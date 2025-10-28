<?php

namespace App\Http\Controllers;

use App\Models\Venta; // Assuming your main orders table is called 'ventas'
use Barryvdh\DomPDF\Facade\Pdf;

class PedidoPdfController extends Controller
{
    public function generarRecibo($id)
    {
        $venta = Venta::with([
            'clienteNatural.user',
            'clienteEstablecimiento',
            'disenos.empleado.user',
            'detalles.producto'
        ])->findOrFail($id);

        $pdf = PDF::loadView('pdf.recibo-pedido', ['pedido' => $venta]);
        return $pdf->download("recibo-venta-{$venta->idVenta}.pdf");
    }

    public function verRecibo($id)
    {
        $venta = Venta::with([
            'clienteNatural.user',
            'clienteEstablecimiento',
            'disenos.empleado.user',
            'detalleVentas.producto' // Ajusta este nombre según tu modelo
        ])->findOrFail($id);

        $pdf = PDF::loadView('pdf.recibo-pedido', ['pedido' => $venta]);
        return $pdf->stream("recibo-venta-{$venta->idVenta}.pdf");
    }
}
