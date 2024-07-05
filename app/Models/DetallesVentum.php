<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class DetallesVentum
 *
 * @property $id
 * @property $venta_id
 * @property $producto_id
 * @property $iva_id
 * @property $cantidad
 * @property $precio_unitario
 * @property $created_at
 * @property $updated_at
 *
 * @property Iva $iva
 * @property Producto $producto
 * @property Venta $venta
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class DetallesVentum extends Model
{
    
    protected $perPage = 20;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $table = 'detalles_ventas';

    protected $fillable = [
        'venta_id', 
        'producto_id', 
        'inventario_id', 
        'cantidad', 
        'precio_unitario'];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function iva()
    {
        return $this->belongsTo(\App\Models\Iva::class, 'iva_id', 'id');
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function producto()
    {
        return $this->belongsTo(\App\Models\Producto::class, 'producto_id', 'id');
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function venta()
    {
        return $this->belongsTo(\App\Models\Venta::class, 'venta_id', 'id');
    }

    public function inventarios()
    {
        return $this->belongsTo(\App\Models\Inventario::class, 'inventario_id', 'id');
    }

    public function sucursal()
    {
        return $this->belongsTo(\App\Models\Sucursale::class, 'sucursal_id', 'id');
    }
    
}
