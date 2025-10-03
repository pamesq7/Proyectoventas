<?php

namespace App\Http\Controllers;

use App\Models\Transaccion;
use App\Models\Venta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PagoController extends Controller
{
    /**
     * Mostrar formulario de edición de pago
     */
    public function editPago($idTransaccion)
    {
        // Buscar la transacción
        $transaccion = Transaccion::with(['venta'])->findOrFail($idTransaccion);

        // Verificar que sea un pago (tipoTransaccion = 'pago')
        if ($transaccion->tipoTransaccion !== 'pago') {
            return redirect()->back()->with('error', 'Solo se pueden editar pagos, no otros tipos de transacciones.');
        }

        // Verificar permisos (opcional - por ahora permite edición)
        // if (Auth::id() !== $transaccion->idUser && !Auth::user()->hasRole('admin')) {
        //     return redirect()->back()->with('error', 'No tienes permisos para editar este pago.');
        // }

        // Obtener métodos de pago disponibles
        $metodosPago = [
            ['codigo' => 'efectivo', 'nombre' => 'Efectivo'],
            ['codigo' => 'tarjeta', 'nombre' => 'Tarjeta'],
            ['codigo' => 'transferencia', 'nombre' => 'Transferencia'],
            ['codigo' => 'yape', 'nombre' => 'Yape'],
            ['codigo' => 'plin', 'nombre' => 'Plin'],
        ];

        return view('pagos.edit', compact('transaccion', 'metodosPago'));
    }

    /**
     * Procesar actualización de pago
     */
    public function updatePago(Request $request, $idTransaccion)
    {
        // Buscar la transacción
        $transaccion = Transaccion::findOrFail($idTransaccion);

        // Verificar que sea un pago
        if ($transaccion->tipoTransaccion !== 'pago') {
            return response()->json([
                'success' => false,
                'message' => 'Solo se pueden editar pagos, no otros tipos de transacciones.'
            ], 422);
        }

        // Validar datos
        $request->validate([
            'monto' => 'required|numeric|min:0.01',
            'metodoPago' => 'required|string|max:50',
            'observaciones' => 'nullable|string|max:500',
            'fecha_pago' => 'required|date'
        ], [
            'monto.required' => 'El monto es obligatorio.',
            'monto.numeric' => 'El monto debe ser un número válido.',
            'monto.min' => 'El monto debe ser mayor a 0.',
            'metodoPago.required' => 'El método de pago es obligatorio.',
            'fecha_pago.required' => 'La fecha es obligatoria.',
            'fecha_pago.date' => 'La fecha debe ser válida.'
        ]);

        return DB::transaction(function () use ($request, $transaccion) {
            $venta = $transaccion->venta;
            $montoAnterior = $transaccion->monto;
            $nuevoMonto = $request->monto;

            // Calcular diferencia de monto
            $diferencia = $nuevoMonto - $montoAnterior;

            // Verificar que el nuevo monto no exceda el saldo disponible
            $saldoMaximo = $venta->saldo + $montoAnterior; // Saldo actual + monto anterior que se va a restar
            if ($nuevoMonto > $saldoMaximo) {
                return response()->json([
                    'success' => false,
                    'message' => 'El monto no puede ser mayor al saldo pendiente (Bs. ' . number_format($saldoMaximo, 2) . ')'
                ], 422);
            }

            // Actualizar transacción
            $transaccion->update([
                'monto' => $nuevoMonto,
                'metodoPago' => $request->metodoPago,
                'observaciones' => $request->observaciones,
                'created_at' => $request->fecha_pago // Nota: Laravel usa created_at para la fecha
            ]);

            // Recalcular saldo de la venta
            $venta->saldo = max(0, $venta->saldo - $diferencia);
            $venta->save();

            // Log del cambio
            Log::info('Pago editado', [
                'transaccion_id' => $transaccion->idTransaccion,
                'venta_id' => $venta->idVenta,
                'usuario' => Auth::user()->name ?? 'Sistema',
                'monto_anterior' => $montoAnterior,
                'nuevo_monto' => $nuevoMonto,
                'diferencia' => $diferencia,
                'saldo_anterior' => $venta->saldo + $diferencia,
                'nuevo_saldo' => $venta->saldo
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Pago actualizado correctamente. Saldo recalculado automáticamente.',
                'data' => [
                    'monto_actual' => number_format($nuevoMonto, 2),
                    'saldo_actual' => number_format($venta->saldo, 2),
                    'fecha_actualizacion' => now()->format('d/m/Y H:i:s')
                ]
            ]);
        });
    }

    /**
     * Listar transacciones/pagos (opcional)
     */
    public function index()
    {
        $transacciones = Transaccion::with(['venta', 'user'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('pagos.index', compact('transacciones'));
    }
}
