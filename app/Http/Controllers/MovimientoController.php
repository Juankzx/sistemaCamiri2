<?php

namespace App\Http\Controllers;

use App\Models\Movimiento;
use Illuminate\Http\Request;

class MovimientoController extends Controller
{
    public function index()
    {
        $movimientos = Movimiento::with(['producto', 'sucursal', 'bodega', 'user'])->paginate(15); // Paginación de 10 elementos

        return view('movimientos.index', compact('movimientos'))
            ->with('i', (request()->input('page', 1) - 1) * 10); // Asegúrate de que coincida con el tamaño de la paginación
    }
}
