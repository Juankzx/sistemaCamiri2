<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array
     */
    protected $middleware = [
        // Mantiene la confianza en los proxies (si la app está detrás de un proxy)
        \App\Http\Middleware\TrustProxies::class,
        // Gestiona el tamaño de las solicitudes
        \Fruitcake\Cors\HandleCors::class,
        // Valida el tamaño máximo de las peticiones
        \App\Http\Middleware\ValidatePostSize::class,
        // Convierte todas las cadenas vacías a nulos
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
            // Middleware de sesión, cookies y seguridad para aplicaciones web
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            // Comparten errores de validación en sesiones
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            // Protege contra ataques CSRF
            \App\Http\Middleware\VerifyCsrfToken::class,
            // Rutas asignadas para autenticación
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            // Middleware personalizado para cargar configuración desde la base de datos
            \App\Http\Middleware\LoadConfigFromDatabase::class,
        ],

        'api' => [
            'throttle:api',  // Limita el número de solicitudes por usuario para APIs
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'auth' => \App\Http\Middleware\Authenticate::class,  // Verifica que el usuario esté autenticado
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,  // Autenticación básica HTTP
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,  // Manejo de headers de cache
        'can' => \Illuminate\Auth\Middleware\Authorize::class,  // Autoriza las acciones del usuario
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,  // Redirige si el usuario ya está autenticado
        'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,  // Requiere confirmación de contraseña
        'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class,  // Valida la firma de una URL
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,  // Limita el número de solicitudes por usuario
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,  // Verifica que el correo esté validado
    ];
}
