<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Factura extends Model
{
    protected $fillable = [
        'guia_despacho_id', 
        'numero_factura', 
        'fecha_factura', 
        'total_factura', 
        'estado_pago', 
        'orden_compra_id' // Asegúrate de que este campo esté aquí
    ];

    protected $dates = ['fecha_factura'];

    // Relación con la tabla GuiaDespacho
    public function guiaDespacho()
    {
        return $this->belongsTo(GuiaDespacho::class, 'guia_despacho_id');
    }
    

    // Nueva relación con la tabla OrdenCompra
    public function ordenCompra()
    {
        return $this->belongsTo(OrdenCompra::class, 'orden_compra_id');
    }
    
}
