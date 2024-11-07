<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Producto
 *
 * @property $id
 * @property $categoria_id
 * @property $proveedor_id
 * @property $nombre
 * @property $imagen
 * @property $precioCompra
 * @property $precioVenta
 * @property $cantidad
 * @property $created_at
 * @property $updated_at
 *
 * @property Categoria $categoria
 * @property Proveedore $proveedore
 * @property DetallesVentum[] $detallesVentas
 * @property Inventario[] $inventarios
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Producto extends Model
{
    


    protected $fillable = [
        'codigo_barra',
        'nombre',
        'unidadmedida_id',
        'imagen',
        'preciocompra',
        'precioventa',
        'categoria_id',
        'proveedor_id',

    ];

    
    public function inventarios()
    {
        return $this->hasMany(Inventario::class, 'producto_id', 'id');
    }

    public function categoria()
    {
        return $this->belongsTo(\App\Models\Categoria::class, 'categoria_id', 'id');
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function proveedor()
    {
        return $this->belongsTo(\App\Models\Proveedore::class, 'proveedor_id', 'id');
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function detallesVentas()
    {
        return $this->hasMany(\App\Models\DetallesVentum::class, 'producto_id', 'id');
    }
    


    public function ventas()
    {
        return $this->hasManyThrough(Venta::class, DetalleVenta::class, 'producto_id', 'id', 'id', 'venta_id');
    }
    
    
    public function unidadMedida()
    {
        return $this->belongsTo(\App\Models\UnidadMedida::class, 'unidadmedida_id');
    }
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */

    
}
