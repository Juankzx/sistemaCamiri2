<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Venta
 *
 * @property $id
 * @property $user_id
 * @property $sucursal_id
 * @property $metodo_pago_id
 * @property $caja_id
 * @property $fecha
 * @property $total
 * @property $created_at
 * @property $updated_at
 *
 * @property Caja $caja
 * @property MetodosPago $metodosPago
 * @property Sucursale $sucursale
 * @property User $user
 * @property DetallesVentum[] $detallesVentas
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Venta extends Model
{
    
    protected $perPage = 20;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['user_id', 'sucursal_id', 'metodo_pago_id', 'caja_id', 'fecha', 'total'];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function caja()
    {
        return $this->belongsTo(\App\Models\Caja::class, 'caja_id', 'id');
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function metodosPago()
    {
        return $this->belongsTo(\App\Models\MetodosPago::class, 'metodo_pago_id', 'id');
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function sucursale()
    {
        return $this->belongsTo(\App\Models\Sucursale::class, 'sucursal_id', 'id');
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id', 'id');
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function detallesVentas()
    {
        return $this->hasMany(\App\Models\DetallesVentum::class, 'id', 'venta_id');
    }
    
}
