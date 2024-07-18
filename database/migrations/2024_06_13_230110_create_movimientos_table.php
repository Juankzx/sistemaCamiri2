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
        Schema::create('movimientos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('producto_id')->constrained('productos');
            $table->foreignId('sucursal_id')->nullable()->constrained('sucursales');
            $table->foreignId('bodega_id')->nullable()->constrained('bodegas');
            $table->enum('tipo', ['entrada', 'salida', 'transferencia', 'venta', 'compra', 'inicial']);
            $table->integer('cantidad');
            $table->timestamp('fecha')->useCurrent();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movimientos');
    }
};
