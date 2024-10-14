<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GuiaDespacho extends Model

{
    protected $table = 'guias_despacho';
    protected $fillable = ['orden_compra_id', 'numero_guia', 'fecha_entrega', 'estado'];

    public function ordenCompra()
    {
        return $this->belongsTo(OrdenCompra::class, 'orden_compra_id');
    }


    // Relación con Facturas a través de la Orden de Compra
    public function facturas()
    {
        return $this->hasMany(Factura::class, 'guia_despacho_id');
    }
    // Relación a través de la Orden de Compra para obtener los productos
    public function productos()
    {
        return $this->hasManyThrough(Producto::class, DetalleOrdenCompra::class, 'orden_compra_id', 'id', 'orden_compra_id', 'producto_id');
    }
}
