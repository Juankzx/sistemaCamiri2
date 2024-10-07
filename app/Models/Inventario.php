<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventario extends Model
{
    
    protected $perPage = 20;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'producto_id',
        'sucursal_id', 
        'bodega_id', 
        'cantidad',
        'stock_minimo',
        'stock_critico',
    ];

     // Verifica si el inventario está en nivel crítico
     public function esCritico()
     {
         return $this->cantidad <= $this->stock_critico;
     }
 
     // Verifica si el inventario está en nivel mínimo
     public function esMinimo()
     {
         return $this->cantidad <= $this->stock_minimo;
     }
    
    public function producto()
    {
        //return $this->belongsTo(\App\Models\Producto::class, 'producto_id', 'id');
        return $this->belongsTo(Producto::class);
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function sucursal()
    {
        //return $this->belongsTo(\App\Models\Sucursale::class, 'sucursal_id', 'id');
        return $this->belongsTo(Sucursale::class);
    }

    public function bodega()
    {
        //return $this->belongsTo(\App\Models\Bodega::class, 'bodega_id', 'id');
        return $this->belongsTo(Bodega::class);
    }
    
    
}
