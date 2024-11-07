<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleGuiaDespacho extends Model
{
    protected $table = 'detalles_guias_despacho';
    use HasFactory;

    protected $fillable = [
        'guia_despacho_id',
        'producto_id', 
        'cantidad_entregada', 
        'precio_compra', 
        'subtotal',
    ];

    // Relación con Guía de Despacho
    public function guiaDespacho()
    {
        return $this->belongsTo(GuiaDespacho::class);
    }

    // Relación con Producto
    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }
}
