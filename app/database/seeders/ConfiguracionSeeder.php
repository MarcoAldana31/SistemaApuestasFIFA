<?php

namespace Database\Seeders;

use App\Models\Configuracion;
use Exception;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Throwable;

class ConfiguracionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::beginTransaction();

        try{
            $configuracion = new Configuracion();
            $configuracion->estado = 1;
            $configuracion->tiempo_limite_vaticinio = 15;
            $configuracion->puntos_acierto_exactos_vaticinio = 3;
            $configuracion->puntos_acierto_ganador_vaticinio = 1;
            $configuracion->puntos_acierto_empate_vaticinio = 1;
            $configuracion->save();

            DB::commit();
        }catch(Exception|Throwable $e){
            DB::rollBack();
            $this->command->warn($e->getMessage());
        }
    }
}
