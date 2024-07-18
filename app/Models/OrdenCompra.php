<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrdenCompra extends Model
{
    use HasFactory;

    protected $table = 'ordenes_compras';
    protected $fillable = [
        'proveedor_id', 
        'numero_orden', 
        'total', 
        'estado'
    ];

    public function proveedor()
    {
        return $this->belongsTo(Proveedore::class);
    }

    public function detalles()
    {
        return $this->hasMany(DetalleOrdenCompra::class);
    }

    public function producto()
    {
        return $this->hasManyThrough(
            Producto::class,
            DetalleOrdenCompra::class,
            'orden_compra_id', // Foreign key on DetalleOrdenCompra table
            'id', // Foreign key on Producto table
            'id', // Local key on OrdenCompra table
            'producto_id' // Local key on DetalleOrdenCompra table
        );
    }

    public function guiasDespacho()
    {
        return $this->hasMany(GuiaDespacho::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::updated(function ($ordenCompra) {
            if ($ordenCompra->isDirty('estado')) {
                $guiaDespacho = $ordenCompra->guiaDespacho;
                if ($guiaDespacho) {
                    $guiaDespacho->update(['estado' => $ordenCompra->estado]);
                }

                if ($ordenCompra->estado == 'entregado') {
                    foreach ($ordenCompra->detalles as $detalle) {
                        // Lógica para agregar al inventario de la bodega general
                        $inventario = Inventario::where('producto_id', $detalle->producto_id)->where('bodega_id', '1')->first(); // Asumiendo '1' es el ID de bodega general
                        if ($inventario) {
                            $inventario->cantidad += $detalle->cantidad;
                            $inventario->save();
                        } else {
                            Inventario::create([
                                'producto_id' => $detalle->producto_id,
                                'bodega_id' => 1, // ID de bodega general
                                'cantidad' => $detalle->cantidad,
                            ]);
                        }
                    }
                }
            }
        });
    }
    
}
