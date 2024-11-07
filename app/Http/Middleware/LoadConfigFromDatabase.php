<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Configuracion;

class LoadConfigFromDatabase
{
    public function handle($request, Closure $next)
    {
        // Cargar la configuración desde la base de datos
        $configuracion = Configuracion::first();

        // Compartirla con todas las vistas
        view()->share('configuracion', $configuracion);

        return $next($request);
    }
}
