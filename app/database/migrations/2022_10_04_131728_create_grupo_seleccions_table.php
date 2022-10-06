<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGrupoSeleccionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('grupo_seleccion', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('estado')->default(1);
            $table->unsignedBigInteger('id_seleccion_pais');
            $table->unsignedBigInteger('id_grupo');
            $table->timestamps();

            $table->foreign('id_seleccion_pais')->references('id')->on('seleccion_pais');
            $table->foreign('id_grupo')->references('id')->on('grupo');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('grupo_seleccion');
    }
}
