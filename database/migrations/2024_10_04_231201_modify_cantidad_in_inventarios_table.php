<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyCantidadInInventariosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('inventarios', function (Blueprint $table) {
            // Cambiar el tipo de columna cantidad a decimal con precisión 8 y 2
            $table->decimal('cantidad', 8, 2)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('inventarios', function (Blueprint $table) {
            // Volver a cambiar la columna cantidad a integer si se revierte la migración
            $table->integer('cantidad')->change();
        });
    }
}
