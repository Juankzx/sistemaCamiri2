<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use NumberFormatter;

class Pago extends Model
{
    use HasFactory;

    protected $fillable = [
        'factura_id', 
        'metodo_pago_id', 
        'monto', 
        'fecha_pago', 
        'numero_transferencia', 
        'estado_pago',
        'descripcion'
    ];

    /**
     * Relación con la factura
     */

     public function getFormattedMontoAttribute()
     {
         $formatter = new NumberFormatter('es_CL', NumberFormatter::CURRENCY);
         return $formatter->formatCurrency($this->monto, 'CLP');
     }

    public function factura()
    {
        return $this->belongsTo(Factura::class);
    }

    /**
     * Relación con el método de pago
     */
    public function metodoPago()
    {
        return $this->belongsTo(MetodosPago::class, 'metodo_pago_id');
    }

    public function guiaDespacho()
{
    return $this->belongsTo(GuiaDespacho::class, 'guia_despacho_id');
}

    /**
     * Evento boot para manejar acciones después de crear un pago
     */
    protected static function boot()
{
    parent::boot();


}


    /**
     * Actualiza el estado de la factura asociada al pago
     * Cambia a "pagado" si el total de pagos cubre o supera el monto de la factura
     */

}
