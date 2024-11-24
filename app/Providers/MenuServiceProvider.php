<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

class MenuServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Compartir el menú con todas las vistas, dependiendo del rol del usuario
        View::composer('*', function ($view) {
            $user = Auth::user();

            if ($user) {
                if ($user->hasRole('root')) {
                    $menu = config('menus.root');
                } elseif ($user->hasRole('administrador')) {
                    $menu = config('menus.administrador');
                } elseif ($user->hasRole('bodeguero')) {
                    $menu = config('menus.bodeguero');
                } elseif ($user->hasRole('vendedor')) {
                    $menu = config('menus.vendedor');
                } else {
                    $menu = [];
                }
            } else {
                $menu = [];
            }

            // Asigna el menú al archivo de configuración 'adminlte.menu'
            config(['adminlte.menu' => $menu]);
        });
    }
}
