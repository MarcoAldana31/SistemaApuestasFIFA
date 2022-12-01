<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddRelacionDirectaGrupoASeleccion extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('seleccion_pais', function (Blueprint $table) {
            $table->unsignedBigInteger('id_grupo')->after('id_liga')->nullable();
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
        Schema::table('seleccion_pais', function (Blueprint $table) {
            if ( DB::getDriverName() !== 'sqlite' ){
                $table->dropForeign(['id_grupo']);
            }

            $table->dropColumn(['id_grupo']);
        });
    }
}
