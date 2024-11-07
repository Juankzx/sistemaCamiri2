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
        Schema::create('facturas', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('guia_despacho_id')->nullable();
                $table->string('numero_factura')->unique()->nullable();
                $table->integer('monto_total')->nullable();
                $table->timestamp('fecha_emision')->nullable();
                $table->enum('estado_pago', ['pendiente', 'pagado'])->default('pendiente');
                $table->timestamps();
    
                // Relación con guías de despacho
                $table->foreign('guia_despacho_id')->references('id')->on('guias_despacho')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('facturas');
    }
};
