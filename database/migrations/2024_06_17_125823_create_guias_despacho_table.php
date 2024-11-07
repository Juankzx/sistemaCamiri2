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
    Schema::create('guias_despacho', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('orden_compra_id')->nullable();
        $table->string('numero_guia')->unique()->nullable();
        $table->timestamp('fecha_entrega')->nullable();
        $table->enum('estado', ['emitida', 'en_transito', 'entregada'])->default('emitida');
        $table->integer('total')->nullable();  // Total de los productos entregados
        $table->timestamps();

        // Relación con orden de compra
        $table->foreign('orden_compra_id')->references('id')->on('ordenes_compras')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guias_despachos');
    }
};
