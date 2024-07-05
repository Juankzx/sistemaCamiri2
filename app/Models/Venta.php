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
    protected $fillable = [
        'user_id', 
        'sucursal_id', 
        'metodo_pago_id', 
        'fecha', 
        'total'];


    protected $dates = [
        'fecha',
    ];

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
    public function metodo_pago()
    {
        return $this->belongsTo(\App\Models\MetodosPago::class, 'metodo_pago_id', 'id');
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function sucursal()
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
    public function detallesVenta()
    {
        return $this->hasMany(\App\Models\DetallesVentum::class, 'venta_id', 'id');
    }

    public function inventarios()
    {
        return $this->hasManyThrough(
            Inventario::class,
            DetallesVentum::class,
            'venta_id',   // Clave foránea en la tabla DetallesVentum
            'id',         // Clave foránea en la tabla Inventario
            'id',         // Clave local en la tabla Ventas
            'inventario_id'  // Clave local en la tabla DetallesVentum
        );
    }
    
}
