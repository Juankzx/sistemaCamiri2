<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('pagos', function (Blueprint $table) {
        $table->enum('estado_pago', ['pendiente', 'pagado'])->default('pendiente')->after('descripcion');
    });
}

public function down()
{
    Schema::table('pagos', function (Blueprint $table) {
        $table->dropColumn('estado_pago');
    });
}

};
