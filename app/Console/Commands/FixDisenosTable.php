<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixDisenosTable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:disenos-table';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix disenos table to make idEmpleado and idDiseñador nullable';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $this->info('Modificando tabla disenos...');
            
            // Ejecutar SQL directo para hacer los campos nullable
            DB::statement('ALTER TABLE disenos MODIFY COLUMN idEmpleado BIGINT UNSIGNED NULL');
            $this->info('✓ Campo idEmpleado modificado a nullable');
            
            DB::statement('ALTER TABLE disenos MODIFY COLUMN idDiseñador BIGINT UNSIGNED NULL');
            $this->info('✓ Campo idDiseñador modificado a nullable');
            
            // Verificar la estructura
            $columns = DB::select('DESCRIBE disenos');
            $this->info('Estructura actual de la tabla disenos:');
            foreach ($columns as $column) {
                if (in_array($column->Field, ['idEmpleado', 'idDiseñador'])) {
                    $this->line("- {$column->Field}: {$column->Type} | Null: {$column->Null}");
                }
            }
            
            $this->info('✅ Cambios aplicados exitosamente');
            return Command::SUCCESS;
            
        } catch (\Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
