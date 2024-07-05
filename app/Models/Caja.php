<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Caja
 *
 * @property $id
 * @property $sucursal_id
 * @property $user_id
 * @property $fecha_apertura
 * @property $fecha_cierre
 * @property $monto_apertura
 * @property $monto_cierre
 * @property $estado
 * @property $created_at
 * @property $updated_at
 *
 * @property Sucursale $sucursale
 * @property User $user
 * @property Venta[] $ventas
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Caja extends Model
{
    
    protected $perPage = 20;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'sucursal_id', 
        'user_id', 
        'fecha_apertura', 
        'fecha_cierre', 
        'monto_apertura', 
        'monto_cierre', 
        'estado'
    ];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function sucursal()
    {
        return $this->belongsTo(Sucursale::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function ventas()
    {
        return $this->hasMany(\App\Models\Venta::class, 'caja_id');
    }
    
}
