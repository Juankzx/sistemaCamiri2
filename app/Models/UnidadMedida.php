<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnidadMedida extends Model
{
    use HasFactory;

    protected $table = 'unidad_medida';
    protected $fillable = ['nombre', 'abreviatura'];
    
    public function productos()
    {
        return $this->hasMany(Producto::class, 'unidadmedida_id');
    }
}

