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
    Schema::table('ventas', function (Blueprint $table) {
        $table->integer('monto_recibido')->nullable()->after('total'); // Monto recibido del cliente
        $table->integer('vuelto')->nullable()->after('monto_recibido'); // Vuelto calculado
    });
}

public function down(): void
{
    Schema::table('ventas', function (Blueprint $table) {
        $table->dropColumn(['monto_recibido', 'vuelto']);
    });
}

};
