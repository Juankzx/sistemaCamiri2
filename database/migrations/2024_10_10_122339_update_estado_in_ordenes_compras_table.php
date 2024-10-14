<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateEstadoInOrdenesComprasTable extends Migration
{
    public function up()
    {
        Schema::table('ordenes_compras', function (Blueprint $table) {
            $table->enum('estado', ['solicitado', 'en_transito', 'entregado', 'cancelado'])->default('solicitado')->change();
        });
    }

    public function down()
    {
        Schema::table('ordenes_compras', function (Blueprint $table) {
            $table->enum('estado', ['solicitado', 'entregado', 'cancelado'])->default('solicitado')->change();
        });
    }
}
