<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetalleOrdenCompra extends Model
{
    protected $table = 'detalles_ordenes_compras';
    protected $fillable = ['orden_compra_id', 'producto_id', 'cantidad', 'precio_compra', 'inventario_id'];

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }

    public function ordenCompra()
    {
        return $this->belongsTo(OrdenCompra::class);
    }

    public function inventario()
    {
        return $this->belongsTo(Inventario::class, 'inventario_id');
    }
}
