<?php

namespace App\Services;

use App\Models\GuiaDespacho;
use App\Models\DetalleGuiaDespacho;
use App\Models\Inventario;

class InventarioService
{
    public function agregarInventarioDesdeGuia(GuiaDespacho $guia)
    {
        foreach ($guia->detalles as $detalle) {
            $inventario = Inventario::firstOrCreate(
                ['producto_id' => $detalle->producto_id],
                ['cantidad' => 0]
            );

            $inventario->cantidad += $detalle->cantidad_entregada;
            $inventario->save();
        }
    }
}
