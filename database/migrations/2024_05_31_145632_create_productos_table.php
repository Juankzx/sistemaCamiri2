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
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('categoria_id')->nullable();
            $table->unsignedBigInteger('proveedor_id')->nullable();
            $table->string('nombre');
            $table->string('imagen')->nullable();
            $table->integer('precioCompra');
            $table->integer('precioVenta');
            $table->integer('cantidad');
            

            $table->timestamps();

            $table->foreign('categoria_id')->references('id')->on('categorias')->onDelete('set null');
            $table->foreign('proveedor_id')->references('id')->on('proveedores')->onDelete('set null');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
