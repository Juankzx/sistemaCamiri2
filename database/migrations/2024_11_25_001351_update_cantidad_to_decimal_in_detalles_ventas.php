<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('detalles_ventas', function (Blueprint $table) {
            $table->decimal('cantidad', 8, 2)->change(); // Cambia a decimal (8 dígitos totales, 2 decimales)
        });
    }

    public function down(): void
    {
        Schema::table('detalles_ventas', function (Blueprint $table) {
            $table->integer('cantidad')->change(); // Revertir a integer si es necesario
        });
    }
};
