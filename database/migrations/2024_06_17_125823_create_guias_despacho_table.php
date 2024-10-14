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
    if (!Schema::hasTable('guias_despacho')) {
        Schema::create('guias_despacho', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('orden_compra_id');
            $table->string('numero_guia')->unique();
            $table->timestamp('fecha_entrega');
            $table->enum('estado', ['emitida', 'en_transito', 'entregada'])->default('emitida');
            $table->timestamps();

            $table->foreign('orden_compra_id')->references('id')->on('ordenes_compras');
        });
    }
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guias_despachos');
    }
};
