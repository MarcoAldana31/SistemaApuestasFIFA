<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSeleccionPaisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('seleccion_pais', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_pais', 100);
            $table->unsignedInteger('estado')->default(1);
            $table->unsignedBigInteger('id_liga');
            $table->timestamps();

            $table->foreign('id_liga')->references('id')->on('liga');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('seleccion_pais');
    }
}
