<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrdenCompra extends Model
{
    use HasFactory;

    protected $table = 'ordenes_compras';
    
        protected $fillable = ['proveedor_id', 'numero_orden', 'estado', 'total'];
    
        public function detalles()
        {
            return $this->hasMany(DetalleOrdenCompra::class, 'orden_compra_id');
        }
    
        public function proveedor()
        {
            return $this->belongsTo(Proveedore::class, 'proveedor_id');
        }
        
        public function facturas()
        {
            return $this->hasManyThrough(Factura::class, GuiaDespacho::class, 'orden_compra_id', 'guia_despacho_id');
        }
        // App\Models\OrdenCompra.php

        public function guiasDespacho()
        {
            return $this->hasMany(GuiaDespacho::class, 'orden_compra_id');
        }


    }