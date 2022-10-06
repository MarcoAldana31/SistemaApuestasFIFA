<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConfiguracionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('configuracion', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('estado')->default(1);
            $table->unsignedInteger('tiempo_limite_vaticinio')->default(15)->comment('Es el tiempo limite en que se puede registrar o actualizars un vacitinio para apostar antes de que inicie el partido. El valor es en minutos.');
            $table->unsignedInteger('puntos_acierto_exactos_vaticinio')->default(3)->comment('Punto que se gana en el vaticinio, si goles fueron exactos para cada equipo del partido');
            $table->unsignedInteger('puntos_acierto_ganador_vaticinio')->default(1)->comment('Punto que se da, si no se cumplio el vaticinio con la cantida de goles, pero aun asi gano el equipo que se speraba ganar.');
            $table->unsignedInteger('puntos_acierto_empate_vaticinio')->default(1)->comment('Aplica aunque no se acerto por compleo el vaticinio, si hubo empate en el partido, gana este punto.');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('configuracion');
    }
}
