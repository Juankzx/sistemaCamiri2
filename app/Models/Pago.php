<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    protected $fillable = ['factura_id', 'metodo_pago_id', 'monto', 'fecha_pago', 'numero_transferencia', 'estado_pago'];

    public function factura()
    {
        return $this->belongsTo(Factura::class);
    }

    public function metodoPago()
    {
        return $this->belongsTo(MetodosPago::class, 'metodo_pago_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::created(function ($pago) {
            if ($pago->estado_pago == 'pagado') {
                $factura = $pago->factura;
                if ($factura->estado_pago == 'pendiente') {
                    $factura->update(['estado_pago' => 'pagado']);
                }
            }
        });
    }
}