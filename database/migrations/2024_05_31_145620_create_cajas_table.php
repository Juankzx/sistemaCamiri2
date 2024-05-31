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
        Schema::create('cajas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sucursal_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamp('fecha_apertura')->nullable()->default(null);
            $table->timestamp('fecha_cierre')->nullable()->default(null);
            $table->integer('monto_apertura');
            $table->integer('monto_cierre');
            $table->boolean('estado')->default(false);            
            $table->timestamps();

            $table->foreign('sucursal_id')->references('id')->on('sucursales')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cajas');
    }
};
