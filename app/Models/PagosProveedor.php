<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class PagosProveedor
 *
 * @property $id
 * @property $pedido_id
 * @property $monto
 * @property $fecha_pago
 * @property $referencia_pago
 * @property $numero_factura
 * @property $estado
 * @property $created_at
 * @property $updated_at
 *
 * @property Pedido $pedido
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class PagosProveedor extends Model
{
    
    protected $perPage = 20;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['pedido_id', 'monto', 'fecha_pago', 'referencia_pago', 'numero_factura', 'estado'];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function pedido()
    {
        return $this->belongsTo(\App\Models\Pedido::class, 'pedido_id', 'id');
    }
    
}
