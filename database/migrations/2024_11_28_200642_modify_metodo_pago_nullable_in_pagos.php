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
        Schema::table('pagos', function (Blueprint $table) {
            // Cambiar la columna 'metodo_pago_id' para permitir valores nulos
            $table->unsignedBigInteger('metodo_pago_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pagos', function (Blueprint $table) {
            // Revertir el cambio para que 'metodo_pago_id' no permita valores nulos
            $table->unsignedBigInteger('metodo_pago_id')->nullable(false)->change();
        });
    }
};
