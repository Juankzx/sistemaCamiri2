<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrdenCompra extends Model
{
    use HasFactory;

    protected $table = 'ordenes_compras';
    protected $fillable = [
        'proveedor_id', 'numero_orden', 'estado'
    ];

    public function detalles()
    {
        return $this->hasMany(DetalleOrdenCompra::class, 'orden_compra_id');
    }
    public function producto()
{
    return $this->hasManyThrough(
        Producto::class,
        DetalleOrdenCompra::class,
        'orden_compra_id', // Foreign key on DetalleOrdenCompra table
        'id', // Foreign key on Producto table
        'id', // Local key on OrdenCompra table
        'producto_id' // Local key on DetalleOrdenCompra table
    );
}

    public function guiasDespacho()
    {
        return $this->hasMany(GuiaDespacho::class);
    }
    public function proveedor()
{
    return $this->belongsTo(\App\Models\Proveedore::class, 'proveedor_id');
}
}
