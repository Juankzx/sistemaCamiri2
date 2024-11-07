<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('detalles_ordenes_compras', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('orden_compra_id');
            $table->unsignedBigInteger('producto_id');
            $table->integer('cantidad')->nullable(); // Cantidad Solicitada
            $table->integer('precio_compra')->nullable(); // Se puede agregar más tarde en la Guía de Despacho
            $table->integer('subtotal')->nullable();
            $table->timestamps();

            // Relación con ordenes de compra y productos
            $table->foreign('orden_compra_id')->references('id')->on('ordenes_compras')->onDelete('cascade');
            $table->foreign('producto_id')->references('id')->on('productos')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalles_ordenes_compras');
    }
};
