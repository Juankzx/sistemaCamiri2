<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('facturas', function (Blueprint $table) {
            $table->unsignedBigInteger('orden_compra_id')->nullable()->after('id');

            // Definir la clave foránea si es necesario
            $table->foreign('orden_compra_id')->references('id')->on('ordenes_compras')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('facturas', function (Blueprint $table) {
            $table->dropForeign(['orden_compra_id']);
            $table->dropColumn('orden_compra_id');
        });
    }
};
