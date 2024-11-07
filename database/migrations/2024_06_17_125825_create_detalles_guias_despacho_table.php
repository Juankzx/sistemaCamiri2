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
    Schema::create('detalles_guias_despacho', function (Blueprint $table) {
        $table->id();
            $table->unsignedBigInteger('guia_despacho_id');
            $table->unsignedBigInteger('producto_id');
            $table->integer('cantidad_entregada');  // Lo que efectivamente se entregó
            $table->integer('precio_compra')->nullable();  // Precio de compra unitario
            $table->integer('subtotal')->nullable();  // Subtotal (cantidad_entregada * precio_compra)
            $table->timestamps(); // Agrega las columnas created_at y updated_at
         
            // Foreign keys
         $table->foreign('guia_despacho_id')->references('id')->on('guias_despacho')->onDelete('cascade');
         $table->foreign('producto_id')->references('id')->on('productos')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalles_guias_despacho');
    }
};
