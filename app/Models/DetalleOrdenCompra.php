<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleOrdenCompra extends Model
{
    use HasFactory;

    protected $table = 'detalles_ordenes_compras';

    protected $fillable = [
        'orden_compra_id',
        'producto_id',
        'cantidad',
        'subtotal', // 'subtotal' calculado a partir de 'cantidad' y precio en la guía de despacho
    ];

    /**
     * Relación con el producto
     */
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    /**
     * Relación con la orden de compra
     */
    public function ordenCompra()
    {
        return $this->belongsTo(OrdenCompra::class, 'orden_compra_id');
    }
}
