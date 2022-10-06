<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVaticiniosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vaticinio', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('goles_equipo_a')->default(0);
            $table->unsignedInteger('goles_equipo_b')->default(0);
            $table->unsignedInteger('puntos_obtenidos')->default(0);
            $table->unsignedInteger('estado')->default(1);
            $table->unsignedBigInteger('id_liga_usuario');
            $table->unsignedBigInteger('id_partido');
            $table->timestamps();

            $table->foreign('id_liga_usuario')->references('id')->on('liga_usuarios');
            $table->foreign('id_partido')->references('id')->on('partido');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vaticinio');
    }
}
