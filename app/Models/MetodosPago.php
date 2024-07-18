<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class MetodosPago extends Model
{
    protected $table = 'metodos_pagos'; 

    protected $perPage = 20;

    protected $fillable = ['nombre'];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function ventas()
    {
        return $this->hasMany(\App\Models\Venta::class, 'id', 'metodo_pago_id');
    }

    public function pagos()
    {
        return $this->hasMany(Pago::class, 'metodo_pago_id');
    }
    
}
