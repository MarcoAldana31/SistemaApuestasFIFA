<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLigasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('liga', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('es_liga_plantilla')->default(0)->comment('Servira para que los usuarios que creen de una liga, ya tengan configurado los equipos de esa liga');
            $table->string('nombre', 100);
            $table->unsignedInteger('tipo_liga');
            $table->unsignedDecimal('total_recaudado', 20, 2);
            $table->unsignedInteger('estado')->default(1);
            $table->unsignedBigInteger('id_liga')->nullable();
            $table->unsignedBigInteger('id_usuario_administrador');
            $table->unsignedBigInteger('id_sede');
            $table->timestamps();

            $table->foreign('id_liga')->references('id')->on('liga');
            $table->foreign('id_usuario_administrador')->references('id')->on('users');
            $table->foreign('id_sede')->references('id')->on('sede');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('liga');
    }
}
