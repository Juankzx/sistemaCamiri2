<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GuiaDespacho extends Model

{
    protected $table = 'guias_despacho';
    protected $fillable = ['orden_compra_id', 'numero_guia', 'fecha_entrega', 'estado'];

    public function ordenCompra()
    {
        return $this->belongsTo(OrdenCompra::class, 'orden_compra_id');
    }

    public function detalles()
    {
        return $this->hasMany(DetalleGuiaDespacho::class);
    }

    public function facturas()
    {
        return $this->hasMany(Factura::class);
    }
}
