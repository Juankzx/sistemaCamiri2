<?php

namespace App\Http\Controllers;

use App\Models\Movimiento;
use Illuminate\Http\Request;

class MovimientoController extends Controller
{
    public function __construct()
{
    $this->middleware(function ($request, $next) {
        if (auth()->check() && auth()->user()->hasRole('vendedor')) {
            abort(403, 'No tienes permiso para acceder a esta página.');
        }
        return $next($request);
    });
}


    public function index()
    {
        $movimientos = Movimiento::with(['producto', 'sucursal', 'bodega', 'user'])
        ->orderBy('created_at', 'desc') // Ordenar por la fecha de creación en orden descendente
        ->paginate(15); // Paginación de 10 elementos

        return view('movimientos.index', compact('movimientos'))
            ->with('i', (request()->input('page', 1) - 1) * 10); // Asegúrate de que coincida con el tamaño de la paginación
    }
}
