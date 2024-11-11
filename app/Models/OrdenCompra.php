<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrdenCompra extends Model
{
    use HasFactory;

    protected $table = 'ordenes_compras';

    protected $fillable = ['proveedor_id', 'numero_orden', 'estado', 'total'];

    /**
     * Relación con el modelo DetalleOrdenCompra.
     * Representa los productos solicitados en la orden de compra.
     */
    public function detalles()
    {
        return $this->hasMany(DetalleOrdenCompra::class, 'orden_compra_id');
    }

    /**
     * Relación con el modelo Proveedor.
     * Cada orden de compra pertenece a un proveedor.
     */
    public function proveedor()
    {
        return $this->belongsTo(Proveedore::class, 'proveedor_id');
    }

    /**
     * Relación con el modelo GuiaDespacho.
     * Representa las guías de despacho asociadas a esta orden de compra.
     */
    public function guiasDespacho()
    {
        return $this->hasMany(GuiaDespacho::class, 'orden_compra_id');
    }

    /**
     * Relación con el modelo Factura.
     * Permite acceder a las facturas asociadas a través de las guías de despacho.
     */
    public function facturas()
    {
        return $this->hasManyThrough(Factura::class, GuiaDespacho::class, 'orden_compra_id', 'guia_despacho_id');
    }

    /**
     * Método para actualizar el estado de la orden de compra.
     * Solo permite estados válidos: solicitado, en_transito, entregado, cancelado.
     * 
     * @param string $nuevoEstado
     * @throws \Exception
     */
    public function actualizarEstado(string $nuevoEstado)
    {
        $estadosPermitidos = ['solicitado', 'en_transito', 'entregado', 'cancelado'];
        if (in_array($nuevoEstado, $estadosPermitidos)) {
            $this->estado = $nuevoEstado;
            $this->save();
        } else {
            throw new \Exception("Estado no permitido");
        }
    }

    /**
     * Calcula el total de la orden de compra en función de los detalles (productos solicitados).
     * Actualiza y guarda el total en la base de datos.
     * 
     * @return int
     */
    public function calcularTotal()
    {
        $total = $this->detalles->sum(function ($detalle) {
            return $detalle->cantidad * $detalle->precio_compra;
        });

        $this->total = $total;
        $this->save();

        return $total;
    }

    /**
     * Verifica si la orden de compra está completa (estado entregado).
     * 
     * @return bool
     */
    public function esCompleta()
    {
        return $this->estado === 'entregado';
    }
}
