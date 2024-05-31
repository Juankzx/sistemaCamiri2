<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Sucursale
 *
 * @property $id
 * @property $nombre
 * @property $direccion
 * @property $created_at
 * @property $updated_at
 *
 * @property Caja[] $cajas
 * @property Venta[] $ventas
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Sucursale extends Model
{
    
    protected $perPage = 20;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['nombre', 'direccion'];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function cajas()
    {
        return $this->hasMany(\App\Models\Caja::class, 'id', 'sucursal_id');
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function ventas()
    {
        return $this->hasMany(\App\Models\Venta::class, 'id', 'sucursal_id');
    }
    
}
