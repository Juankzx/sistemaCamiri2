<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Configuracion;
use Illuminate\Support\Facades\View;
use App\Services\EstadoService;
use App\Services\InventarioService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    
public function register()
{
    $this->app->singleton(EstadoService::class, function ($app) {
        return new EstadoService(new InventarioService());
    });

    $this->app->singleton(InventarioService::class, function ($app) {
        return new InventarioService();
    });
}

    /**
     * Bootstrap any application services.
     */

     public function boot()
     {
         View::composer('*', function ($view) {
             $view->with('isVendedor', auth()->check() && auth()->user()->hasRole('vendedor'));
         });
     }

}
