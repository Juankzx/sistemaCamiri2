<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Factura extends Model
{
    protected $fillable = [
        'guia_despacho_id', 
        'numero_factura', 
        'fecha_emision', 
        'monto_total', 
        'estado_pago',  // Corrige el nombre del campo para reflejar 'estado_pago'
    ];
    

    protected $dates = ['fecha_emision'];

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
    // Relación con Pagos
    public function pagos()
    {
        return $this->hasMany(Pago::class);
    }
    
}
