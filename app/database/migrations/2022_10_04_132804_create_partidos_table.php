<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePartidosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('partido', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('tipo_partido');
            $table->date('fecha_partido');
            $table->time('hora_inicio');
            $table->unsignedInteger('goles_seleccion_a')->default(0);
            $table->unsignedInteger('goles_seleccion_b')->default(0);
            $table->unsignedInteger('estado')->default(1);
            $table->unsignedBigInteger('id_liga');
            $table->unsignedBigInteger('id_estadio');
            $table->unsignedBigInteger('id_grupo')->nullable();
            $table->unsignedBigInteger('id_seleccion_pais_a');
            $table->unsignedBigInteger('id_seleccion_pais_b');
            $table->timestamps();

            $table->foreign('id_liga')->references('id')->on('liga');
            $table->foreign('id_estadio')->references('id')->on('estadio');
            $table->foreign('id_grupo')->references('id')->on('grupo');
            $table->foreign('id_seleccion_pais_a')->references('id')->on('seleccion_pais');
            $table->foreign('id_seleccion_pais_b')->references('id')->on('seleccion_pais');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('partido');
    }
}
