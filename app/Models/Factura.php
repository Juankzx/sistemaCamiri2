<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Factura extends Model
{
    use HasFactory;

    protected $table = 'facturas';

    protected $fillable = [
        'guia_despacho_id',
        'numero_factura',
        'monto_total',
        'fecha_emision',
        'estado_pago',
    ];

    /**
     * Relación con GuiaDespacho
     * Cada factura pertenece a una guía de despacho
     */
    public function guiaDespacho()
    {
        return $this->belongsTo(GuiaDespacho::class, 'guia_despacho_id');
    }
    
    public function factura()
{
    return $this->belongsTo(Factura::class);
}


    /**
     * Relación a través de GuiaDespacho para acceder a OrdenCompra
     * Esto permite acceder a la orden de compra asociada a través de la guía de despacho
     */
    public function ordenCompra()
    {
        return $this->hasOneThrough(OrdenCompra::class, GuiaDespacho::class, 'id', 'id', 'guia_despacho_id', 'orden_compra_id');
    }

    /**
     * Relación con pagos
     * Una factura puede tener múltiples pagos asociados
     */
    public function pagos()
    {
        return $this->hasMany(Pago::class);
    }

    /**
     * Método para verificar si la factura está pagada
     * @return bool
     */
    public function isPagado()
    {
        return $this->estado_pago === 'pagado';
    }

    

}
