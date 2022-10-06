<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLigaUsuariosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('liga_usuarios', function (Blueprint $table) {
            $table->id();
            $table->date('fecha_aprobado')->nullable();
            $table->unsignedInteger('total_puntos_obtenidos');
            $table->unsignedInteger('estado')->default(1);
            $table->unsignedBigInteger('id_liga');
            $table->unsignedBigInteger('id_usuario');
            $table->timestamps();

            $table->foreign('id_liga')->references('id')->on('liga');
            $table->foreign('id_usuario')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('liga_usuarios');
    }
}
