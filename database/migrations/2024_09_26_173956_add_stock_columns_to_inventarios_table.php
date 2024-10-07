<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inventarios', function (Blueprint $table) {
            // Agregar las columnas de stock mínimo y stock crítico
            $table->integer('stock_minimo')->default(0)->after('cantidad');
            $table->integer('stock_critico')->default(0)->after('stock_minimo');
        });
    }

    public function down(): void
    {
        Schema::table('inventarios', function (Blueprint $table) {
            // Eliminar las columnas en caso de rollback
            $table->dropColumn('stock_minimo');
            $table->dropColumn('stock_critico');
        });
    }
};