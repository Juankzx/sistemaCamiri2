<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GuiaDespacho extends Model

{
    protected $table = 'guias_despacho';
    protected $fillable = ['orden_compra_id', 'numero_guia', 'fecha_entrega', 'estado'];

    public function ordenCompra()
    {
        return $this->belongsTo(OrdenCompra::class);
    }

    public function facturas()
    {
        return $this->hasMany(Factura::class);
    }
}
