<?php

namespace App\Console\Commands;

use App\Models\Venta;
use App\Models\DetalleVenta;
use App\Models\Transaccion;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RecalcularTotalesSinIGV extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ventas:recalcular-sin-igv';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recalcula todos los totales de ventas eliminando el IGV (18%)';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Iniciando recálculo de totales sin IGV...');

        DB::beginTransaction();
        try {
            $ventas = Venta::where('estado', 1)->get();
            $this->info("Se encontraron {$ventas->count()} ventas activas.");

            $bar = $this->output->createProgressBar($ventas->count());
            $bar->start();

            foreach ($ventas as $venta) {
                // Recalcular subtotal desde detalles
                $nuevoSubtotal = DetalleVenta::where('idVenta', $venta->idVenta)
                    ->selectRaw('COALESCE(SUM(cantidad * precioUnitario), 0) as s')
                    ->value('s');

                // Total = Subtotal (sin IGV)
                $nuevoTotal = $nuevoSubtotal;

                // Recalcular saldo basado en pagos
                $pagos = Transaccion::where('idVenta', $venta->idVenta)
                    ->where('tipoTransaccion', 'pago')
                    ->sum('monto');

                $nuevoSaldo = max($nuevoTotal - (float)$pagos, 0);

                // Actualizar venta
                $venta->subtotal = $nuevoSubtotal;
                $venta->total = $nuevoTotal;
                $venta->saldo = $nuevoSaldo;
                $venta->save();

                $bar->advance();
            }

            $bar->finish();
            $this->newLine();

            DB::commit();
            $this->info('✅ Recálculo completado exitosamente.');
            $this->info("Total de ventas actualizadas: {$ventas->count()}");

            return Command::SUCCESS;
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('❌ Error al recalcular totales: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
