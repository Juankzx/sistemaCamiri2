<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\OrdenCompra;
use App\Models\GuiaDespacho;

class TransferOrderTotalToDispatchGuide extends Command
{
    protected $signature = 'transfer:order-total';
    protected $description = 'Transferir los totales de ordenes_compras a guias_despacho';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        // Obtener todas las ordenes de compra con sus guias de despacho asociadas
        $ordenes = OrdenCompra::with('guiasDespacho')->get();

        foreach ($ordenes as $orden) {
            foreach ($orden->guiasDespacho as $guia) {
                // Transferir el total de la orden a la guía de despacho
                $guia->total = $orden->total;
                $guia->save();
            }
        }

        $this->info('Los valores de total han sido transferidos de ordenes_compras a guias_despacho exitosamente.');
        return 0;
    }
}
