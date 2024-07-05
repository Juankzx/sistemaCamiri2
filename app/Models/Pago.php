<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    protected $fillable = ['factura_id', 'metodo_pago_id', 'monto', 'fecha_pago', 'numero_transferencia', 'estado_pago'];

    public function factura()
    {
        return $this->belongsTo(Factura::class, 'factura_id');
    }
    public function metodoPago()
    {
        return $this->belongsTo(MetodosPago::class, 'metodo_pago_id');
    }
}
