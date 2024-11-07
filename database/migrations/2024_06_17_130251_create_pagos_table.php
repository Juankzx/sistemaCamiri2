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
            $table->unsignedBigInteger('factura_id')->nullable();
            $table->unsignedBigInteger('metodo_pago_id');
            $table->integer('monto')->nullable();
            $table->integer('numero_transferencia')->nullable(); //en caso de ser transferencia, se debe colocar el numero de transferencia para llevar un orden.
            $table->timestamp('fecha_pago')->nullable();
            $table->text('descripcion')->nullable(); // Descripción opcional para pagos sin factura
            
            
            $table->timestamps();

            // Relación con facturas de compra
            $table->foreign('factura_id')->references('id')->on('facturas')->onDelete('cascade');
            // Relación con métodos de pago
            $table->foreign('metodo_pago_id')->references('id')->on('metodos_pagos')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('pagos');
    }
};
