<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Pedido
 *
 * @property $id
 * @property $proveedor_id
 * @property $fecha_pedido
 * @property $total
 * @property $estado
 * @property $created_at
 * @property $updated_at
 *
 * @property Proveedore $proveedore
 * @property PagosProveedor[] $pagosProveedors
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Pedido extends Model
{
    
    protected $perPage = 20;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['proveedor_id', 'fecha_pedido', 'total', 'estado'];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function proveedore()
    {
        return $this->belongsTo(\App\Models\Proveedore::class, 'proveedor_id', 'id');
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function pagosProveedors()
    {
        return $this->hasMany(\App\Models\PagosProveedor::class, 'id', 'pedido_id');
    }
    
}
