<?php

namespace App\Services;

use App\Models\GuiaDespacho;
use App\Models\Factura;
use App\Models\OrdenCompra;

class EstadoService
{
    public function actualizarEstadoGuia(GuiaDespacho $guia, $nuevoEstado)
    {
        $guia->estado = $nuevoEstado;
        $guia->save();

        // Si la guía se completa, verifica la orden de compra relacionada
        if ($nuevoEstado === 'entregada' && $guia->orden_compra_id) {
            $ordenCompra = $guia->ordenCompra;
            $this->verificarEstadoOrdenCompra($ordenCompra);
        }
    }

    public function actualizarEstadoFactura(Factura $factura, $nuevoEstado)
{
    $factura->update(['estado_pago' => $nuevoEstado]);

    if ($nuevoEstado === 'pagado') {
        if ($factura->guiaDespacho) {
            $this->inventarioService->agregarInventarioDesdeGuia($factura->guiaDespacho);
            \Log::info('Inventario actualizado desde la guía de despacho.', ['guia_id' => $factura->guiaDespacho->id]);
        }
    }
}


    public function verificarEstadoOrdenCompra(OrdenCompra $ordenCompra)
    {
        $guiasRelacionadas = $ordenCompra->guiasDespacho;

        // Si todas las guías relacionadas están en estado 'entregada', actualiza la orden
        if ($guiasRelacionadas->every(fn($guia) => $guia->estado === 'entregada')) {
            $ordenCompra->estado = 'entregado';
            $ordenCompra->save();
        }
    }

    public function verificarEstadoGuia(GuiaDespacho $guia)
    {
        $facturasRelacionadas = $guia->facturas;

        // Si todas las facturas relacionadas están en estado 'pagado', actualiza la guía
        if ($facturasRelacionadas->every(fn($factura) => $factura->estado_pago === 'pagado')) {
            $guia->estado = 'entregada';
            $guia->save();
        }
    }
}
