<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Venta;
use App\Models\Diseno;
use Illuminate\Support\Facades\Storage;

class DisenadorController extends Controller
{
    /**
     * Mostrar todos los diseños asignados al diseñador autenticado
     */
    public function index()
    {
        // Obtener el empleado del usuario autenticado
        $empleadoId = auth()->user()->empleado ? auth()->user()->empleado->idEmpleado : null;

        if (!$empleadoId) {
            return redirect()->route('home')->with('error', 'No tienes un perfil de empleado asignado.');
        }

        // Obtener TODOS los diseños asignados a este diseñador
        $disenos = Diseno::where('idEmpleado', $empleadoId)
            ->with('empleado', 'detalleVenta.venta.clienteNatural', 'detalleVenta.venta.clienteEstablecimiento')
            ->latest()
            ->get();

        return view('dashboard.disenador', compact('disenos'));
    }

    /**
     * Mostrar los detalles de un diseño para trabajar en él
     */
    public function trabajar($idDiseno)
    {
        // Buscar el diseño
        $diseno = Diseno::findOrFail($idDiseno);
        return view('rolDiseñador.trabajar', compact('diseno'));
    }

    /**
     * Subir el diseño terminado y cambiar estados
     */
    public function subirDisenoTerminado(Request $request, $idDiseno)
    {
        // Validar el archivo subido
        $request->validate([
            'disenoTerminado' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ], [
            'disenoTerminado.required' => 'Debes seleccionar un archivo.',
            'disenoTerminado.mimes' => 'El archivo debe ser JPG, PNG o PDF.',
            'disenoTerminado.max' => 'El archivo no debe exceder 5MB.',
        ]);

        // Buscar el diseño
        $diseno = Diseno::findOrFail($idDiseno);

        // Ya no verificamos el estado, el diseñador puede trabajar en cualquier diseño
        return view('rolDiseñador.trabajar', compact('diseno'));


        // Subir el archivo de diseño terminado
        $rutaTerminado = $request->file('disenoTerminado')->store('disenos_terminados', 'public');

        // Reemplazar la imagen de borrador con la imagen terminada
        $diseno->archivo = $rutaTerminado;
        $diseno->estadoDiseño = 'terminado';
        $diseno->save();

        // Cambiar el estado del pedido a 'Producción' (estado 2)
        $venta = $diseno->detalleVenta->venta;
        $venta->estadoPedido = '2'; // Cambiar a Producción
        $venta->save();

        return redirect()->route('dashboard.disenador')
            ->with('success', '✅ ¡Excelente! El diseño ha sido marcado como terminado. El pedido ha pasado automáticamente a producción.');
    }
    /**
     * Mostrar la vista de Mis Diseños con tabs de borradores y terminados
     */
    public function misDisenos()
    {
        $empleadoId = auth()->user()->empleado->idEmpleado;

        // Obtener TODOS los diseños asignados a este diseñador
        $disenos = Diseno::where('idEmpleado', $empleadoId)
            ->with('empleado', 'detalleVenta.venta.clienteNatural', 'detalleVenta.venta.clienteEstablecimiento')
            ->latest()
            ->get();

        // Separar por estado para los tabs
        // Estados reales en BD: 'en proceso' y 'terminado'
        $borradores = $disenos->where('estadoDiseño', 'en proceso');
        $terminados = $disenos->where('estadoDiseño', 'terminado');

        return view('rolDiseñador.mis-disenos', compact('borradores', 'terminados'));
    }
}
