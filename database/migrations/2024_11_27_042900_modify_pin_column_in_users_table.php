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
    Schema::table('users', function (Blueprint $table) {
        $table->string('pin', 6)->nullable()->change(); // Cambiar INT a VARCHAR(6)
    });
}

public function down()
{
    Schema::table('users', function (Blueprint $table) {
        $table->integer('pin')->nullable()->change(); // Volver a INT en caso de rollback
    });
}

};
