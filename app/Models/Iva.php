<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Iva
 *
 * @property $id
 * @property $porcentaje
 * @property $created_at
 * @property $updated_at
 *
 * @property DetallesVentum[] $detallesVentas
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Iva extends Model
{
    
    protected $perPage = 20;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['porcentaje'];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function detallesVentas()
    {
        return $this->hasMany(\App\Models\DetallesVentum::class, 'id', 'iva_id');
    }
    
}
