<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ordenes_compras', function (Blueprint $table) {
            // Si es necesario, elimina primero la restricción de unicidad actual
            $table->dropUnique(['numero_orden']);

            // Cambia el tipo de dato de numero_orden a unsignedBigInteger
            $table->unsignedBigInteger('numero_orden')->change();

            // Vuelve a agregar la restricción de unicidad
            $table->unique('numero_orden');
        });
    }

    public function down(): void
    {
        Schema::table('ordenes_compras', function (Blueprint $table) {
            // Revertir el cambio
            $table->dropUnique(['numero_orden']);
            $table->string('numero_orden')->unique()->change();
        });
    }
};
