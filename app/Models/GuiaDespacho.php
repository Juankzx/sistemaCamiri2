<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GuiaDespacho extends Model
{
    use HasFactory;

    protected $table = 'guias_despacho';
    
    protected $fillable = [
        'orden_compra_id', 
        'numero_guia', 
        'fecha_entrega', 
        'estado',
        'total'
    ];

    /**
     * Relación con Orden de Compra.
     */
    public function ordenCompra()
    {
        return $this->belongsTo(OrdenCompra::class, 'orden_compra_id');
    }

    /**
     * Relación con Detalles de la Guía de Despacho.
     */
    public function detalles()
    {
        return $this->hasMany(DetalleGuiaDespacho::class, 'guia_despacho_id');
    }

    public function user()
{
    return $this->belongsTo(User::class, 'user_id'); // Ajusta 'user_id' según tu base de datos
}

    /**
     * Relación con Facturas asociadas a esta Guía de Despacho.
     */
    public function facturas()
    {
        return $this->hasMany(Factura::class, 'guia_despacho_id');
    }

    /**
     * Relación para obtener los productos asociados a la orden de compra a través de los detalles.
     */
    public function productos()
    {
        return $this->hasManyThrough(
            Producto::class,
            DetalleGuiaDespacho::class, // Si la relación debe ir a través de DetalleGuiaDespacho en vez de DetalleOrdenCompra
            'guia_despacho_id', // Clave externa en DetalleGuiaDespacho
            'id',               // Clave primaria en Producto
            'id',               // Clave primaria en GuiaDespacho
            'producto_id'       // Clave externa en DetalleGuiaDespacho
        );
    }


}