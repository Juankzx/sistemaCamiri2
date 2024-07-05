<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Factura extends Model
{
    protected $fillable = ['guia_despacho_id', 'numero_factura', 'fecha_factura', 'total_factura', 'estado_pago'];

    protected $dates = ['fecha_factura'];

    public function guiaDespacho()
    {
        return $this->belongsTo(GuiaDespacho::class, 'guia_despacho_id');
    }
}