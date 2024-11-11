<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleGuiaDespacho extends Model
{
    use HasFactory;

    protected $table = 'detalles_guias_despacho';

    protected $fillable = [
        'guia_despacho_id',
        'producto_id',
        'cantidad_entregada',
        'precio_compra',
        'subtotal',
    ];

    /**
     * Relación con el modelo GuiaDespacho
     * Cada detalle pertenece a una guía de despacho específica.
     */
    public function guiaDespacho()
    {
        return $this->belongsTo(GuiaDespacho::class, 'guia_despacho_id');
    }

    public function detalles()
{
    return $this->hasMany(DetalleGuiaDespacho::class, 'guia_despacho_id');
}

    /**
     * Relación con el modelo Producto
     * Cada detalle de la guía de despacho está asociado a un producto.
     */
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    /**
     * Calcula y establece el subtotal automáticamente.
     */
    public function calcularSubtotal()
    {
        $this->subtotal = $this->cantidad_entregada * $this->precio_compra;
    }

    /**
     * Evento boot para calcular y guardar el subtotal automáticamente cuando se crea o actualiza un detalle.
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($detalle) {
            $detalle->calcularSubtotal();
        });
    }
}
