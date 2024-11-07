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
        Schema::create('reportes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre'); // Nombre del reporte
            $table->text('descripcion')->nullable(); // Descripción del reporte
            $table->enum('tipo', ['ventas', 'compras', 'inventario', 'productos'])->default('ventas'); // Tipo de reporte
            $table->unsignedBigInteger('sucursal_id')->nullable(); // ID de la sucursal si aplica
            $table->unsignedBigInteger('user_id'); // ID del usuario que generó el reporte
            $table->json('datos')->nullable(); // Almacenar datos de reporte en formato JSON
            $table->timestamp('fecha_generacion')->useCurrent(); // Fecha de generación del reporte
            $table->timestamps();

            // Foreign keys
            $table->foreign('sucursal_id')->references('id')->on('sucursales')->onDelete('set null');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reportes');
    }
};
