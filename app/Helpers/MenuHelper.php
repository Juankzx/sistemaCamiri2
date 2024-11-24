<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;

class MenuHelper
{
    public static function getMenuForCurrentRole()
    {
        $user = Auth::user();

        if ($user->hasRole('root')) {
            return config('menus.root');
        } elseif ($user->hasRole('administrador')) {
            return config('menus.administrador');
        } elseif ($user->hasRole('bodeguero')) {
            return config('menus.bodeguero');
        } elseif ($user->hasRole('vendedor')) {
            return config('menus.vendedor');
        } else {
            return []; // Si no tiene rol, no muestra menú
        }
    }
}
