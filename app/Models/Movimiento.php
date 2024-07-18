<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Movimiento extends Model
{
    use HasFactory;

    protected $fillable = [
        'producto_id',
        'sucursal_id',
        'bodega_id',
        'tipo',
        'cantidad',
        'fecha',
        'user_id'
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }

    public function sucursal()
    {
        return $this->belongsTo(Sucursale::class);
    }
    public function bodega()
    {
        return $this->belongsTo(Bodega::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
