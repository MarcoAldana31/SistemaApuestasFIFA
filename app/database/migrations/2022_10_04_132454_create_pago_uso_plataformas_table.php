<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePagoUsoPlataformasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pago_uso_plataforma', function (Blueprint $table) {
            $table->id();
            $table->date('fecha_pagado')->nullable();
            $table->unsignedDecimal('total_pagado', 10, 2);
            $table->unsignedInteger('estado')->default(1);
            $table->unsignedBigInteger('id_liga_usuarios');
            $table->timestamps();

            $table->foreign('id_liga_usuarios')->references('id')->on('liga_usuarios');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pago_uso_plataforma');
    }
}
