<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('pagos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('factura_id');
            $table->unsignedBigInteger('metodo_pago_id');
            $table->integer('monto');
            $table->timestamp('fecha_pago')->nullable();
            $table->string('numero_transferencia')->nullable(); // Añadir la columna aquí directamente
            $table->enum('estado_pago', ['pendiente', 'pagado'])->default('pendiente');
            $table->timestamps();

            $table->foreign('factura_id')->references('id')->on('facturas');
            $table->foreign('metodo_pago_id')->references('id')->on('metodos_pagos');
        });
    }

    public function down()
    {
        Schema::dropIfExists('pagos');
    }
};
