<?php

return [

    /*
    |-------------------------------------------------------------------------- 
    | Title 
    |-------------------------------------------------------------------------- 
    | 
    | Here you can change the default title of your admin panel. 
    |
    */
    'title' => 'Sistema Administrativo Minimarket',
    'title_prefix' => '',
    'title_postfix' => '',

    /*
    |-------------------------------------------------------------------------- 
    | Favicon 
    |-------------------------------------------------------------------------- 
    */
    'use_ico_only' => false,
    'use_full_favicon' => false,

    /*
    |-------------------------------------------------------------------------- 
    | Google Fonts 
    |-------------------------------------------------------------------------- 
    */
    'google_fonts' => [
        'allowed' => true,
    ],

    /*
    |-------------------------------------------------------------------------- 
    | Admin Panel Logo 
    |-------------------------------------------------------------------------- 
    */
    'logo' => '<b>Sistema</b>Administrativo',
    'logo_img' => 'vendor/adminlte/dist/img/AdminLTELogo.png',
    'logo_img_class' => 'brand-image img-circle elevation-3',
    'logo_img_xl' => null,
    'logo_img_xl_class' => 'brand-image-xs',
    'logo_img_alt' => 'Admin Logo',

    /*
    |-------------------------------------------------------------------------- 
    | Authentication Logo 
    |-------------------------------------------------------------------------- 
    */
    'auth_logo' => [
        'enabled' => false,
        'img' => [
            'path' => 'vendor/adminlte/dist/img/AdminLTELogo.png',
            'alt' => 'Auth Logo',
            'class' => '',
            'width' => 50,
            'height' => 50,
        ],
    ],

    /*
    |-------------------------------------------------------------------------- 
    | Preloader Animation 
    |-------------------------------------------------------------------------- 
    */
    'preloader' => [
        'enabled' => true,
        'mode' => 'fullscreen',
        'img' => [
            'path' => 'vendor/adminlte/dist/img/AdminLTELogo.png',
            'alt' => 'AdminLTE Preloader Image',
            'effect' => 'animation__shake',
            'width' => 60,
            'height' => 60,
        ],
    ],

    /*
    |-------------------------------------------------------------------------- 
    | User Menu 
    |-------------------------------------------------------------------------- 
    */
    'usermenu_enabled' => true,
    'usermenu_header' => false,
    'usermenu_header_class' => 'bg-primary',
    'usermenu_image' => false,
    'usermenu_desc' => false,
    'usermenu_profile_url' => false,

    /*
    |-------------------------------------------------------------------------- 
    | Layout 
    |-------------------------------------------------------------------------- 
    */
    'layout_topnav' => null,
    'layout_boxed' => null,
    'layout_fixed_sidebar' => null,
    'layout_fixed_navbar' => null,
    'layout_fixed_footer' => null,
    'layout_dark_mode' => null,

    /*
    |-------------------------------------------------------------------------- 
    | Authentication Views Classes 
    |-------------------------------------------------------------------------- 
    */
    'classes_auth_card' => 'card-outline card-primary',
    'classes_auth_header' => '',
    'classes_auth_body' => '',
    'classes_auth_footer' => '',
    'classes_auth_icon' => '',
    'classes_auth_btn' => 'btn-flat btn-primary',

    /*
    |-------------------------------------------------------------------------- 
    | Admin Panel Classes 
    |-------------------------------------------------------------------------- 
    */
    'classes_body' => '',
    'classes_brand' => '',
    'classes_brand_text' => '',
    'classes_content_wrapper' => '',
    'classes_content_header' => '',
    'classes_content' => '',
    'classes_sidebar' => 'sidebar-dark-primary elevation-4',
    'classes_sidebar_nav' => 'nav-child-indent',  // Aquí agregas esta línea
    'classes_topnav' => 'navbar-white navbar-light',
    'classes_topnav_nav' => 'navbar-expand',
    'classes_topnav_container' => 'container',
    

    /*
    |-------------------------------------------------------------------------- 
    | Sidebar 
    |-------------------------------------------------------------------------- 
    */
    'sidebar_mini' => 'lg',
    'sidebar_collapse' => false,
    'sidebar_collapse_auto_size' => false,
    'sidebar_collapse_remember' => false,
    'sidebar_collapse_remember_no_transition' => true,
    'sidebar_scrollbar_theme' => 'os-theme-light',
    'sidebar_scrollbar_auto_hide' => 'l',
    'sidebar_nav_accordion' => true,
    'sidebar_nav_animation_speed' => 300,

    /*
    |-------------------------------------------------------------------------- 
    | Control Sidebar (Right Sidebar) 
    |-------------------------------------------------------------------------- 
    */
    'right_sidebar' => false,
    'right_sidebar_icon' => 'fas fa-cogs',
    'right_sidebar_theme' => 'dark',
    'right_sidebar_slide' => true,
    'right_sidebar_push' => true,
    'right_sidebar_scrollbar_theme' => 'os-theme-light',
    'right_sidebar_scrollbar_auto_hide' => 'l',

    /*
    |-------------------------------------------------------------------------- 
    | URLs 
    |-------------------------------------------------------------------------- 
    */
    'use_route_url' => false,
    'dashboard_url' => 'home',
    'logout_url' => 'logout',
    'login_url' => 'login',
    'register_url' => 'register',
    'password_reset_url' => 'password/reset',
    'password_email_url' => 'password/email',
    'profile_url' => false,

    /*
    |-------------------------------------------------------------------------- 
    | Laravel Mix 
    |-------------------------------------------------------------------------- 
    */
    'enabled_laravel_mix' => false,
    'laravel_mix_css_path' => 'css/app.css',
    'laravel_mix_js_path' => 'js/app.js',

    /*
    |-------------------------------------------------------------------------- 
    | Menu Items 
    |-------------------------------------------------------------------------- 
    */

    'menu' => [],  // Define el menú vacío aquí
    'menu' => [
        // Navbar items:
        [
            'type' => 'navbar-search',
            'text' => 'search',
            'topnav_right' => false,
        ],
        [
            'type' => 'fullscreen-widget',
            'topnav_right' => false,
        ],

        // Sidebar items:
        [
            'text' => 'Punto de Venta',
            'icon' => 'fa solid fa-cash-register',
            
            
            'submenu' => [
                [
                    'text' => 'Ventas',
                    'icon' => 'fa solid fa-cash-register',
                    'url' => 'ventas',
                    'active' => ['ventas', 'ventas/*'],
                ],
                [
                    'text' => 'Cajas',
                    'icon' => 'fa solid fa-cash-register',
                    'url' => 'cajas',
                    'active' => ['cajas', 'cajas/*'],
                ],
            ],
        ],
        [
            'text' => 'Compras',
            'icon' => 'fa solid fa-cart-plus',
            'submenu' => [
                [
                    'text' => 'Orden de Compras',
                    'icon' => 'fa solid fa-cart-plus',
                    'url' => 'ordenes-compras',
                    'active' => ['ordenes-compras', 'ordenes-compras/*'],
                ],
                [
                    'text' => 'Guias de Despacho',
                    'icon' => 'fa solid fa-cart-plus',
                    'url' => 'guias-despacho',
                    'active' => ['guias-despacho', 'guias-despacho/*'],
                ],
                [
                    'text' => 'Facturas',
                    'icon' => 'fa solid fa-cart-plus',
                    'url' => 'facturas',
                    'active' => ['facturas', 'facturas/*'],
                ],
                [
                    'text' => 'Pagos',
                    'icon' => 'fa solid fa-cart-plus',
                    'url' => 'pagos',
                    'active' => ['pagos', 'pagos/*'],
                ],
            ],
        ],
        [
            'text' => 'Inventario',
            'icon' => 'fa solid fa-warehouse',
            'submenu' => [
                [
                    'text' => 'Movimientos Inventario',
                    'icon' => 'fa solid fa-warehouse',
                    'url' => 'movimientos',
                    'active' => ['movimientos', 'movimientos/*'],
                ],
                [
                    'text' => 'Inventarios',
                    'icon' => 'fa solid fa-warehouse',
                    'url' => 'inventarios',
                    'active' => ['inventarios', 'inventarios/*'],
                ],
                [
                    'text' => 'Productos',
                    'icon' => 'fa solid fa-warehouse',
                    'url' => 'productos',
                    'active' => ['productos', 'productos/*'],
                ],
                [
                    'text' => 'Bodegas',
                    'icon' => 'fa solid fa-warehouse',
                    'url' => 'bodegas',
                    'active' => ['bodegas', 'bodegas/*'],
                ],
                [
                    'text' => 'Categorias',
                    'icon' => 'fa solid fa-warehouse',
                    'url' => 'categorias',
                    'active' => ['categorias', 'categorias/*'],
                ],
            ],
        ],
        [
            'text' => 'Panel de Control',
            'icon' => 'fa fa-cog',
            'submenu' => [
                [
                    'text' => 'Metodos de Pago',
                    'icon' => 'fa fa-cog',
                    'url' => 'metodos-pagos',
                    'active' => ['metodos-pagos', 'metodos-pagos/*'],
                ],
                [
                    'text' => 'Proveedores',
                    'icon' => 'fa fa-cog',
                    'url' => 'proveedores',
                    'active' => ['proveedores', 'proveedores/*'],
                ],
                [
                    'text' => 'Sucursales',
                    'icon' => 'fa fa-cog',
                    'url' => 'sucursales',
                    'active' => ['sucursales', 'sucursales/*'],
                ],
                [
                    'text' => 'Unidad de Medida',
                    'icon' => 'fa fa-cog',
                    'url' => 'unidades',
                    'active' => ['unidades', 'unidades/*'],
                ],
                
            ],
        ],
        [
            'text' => 'Usuarios',
            'icon' => 'fa fa-users',
            'submenu' => [
                [
                    'text' => 'Listar Usuarios',
                    'icon' => 'fa fa-users',
                    'url' => 'users',
                    'active' => ['users', 'users/*'],
                ],
                [
                    'text' => 'Asignar Rol',
                    'icon' => 'fa fa-user-tag',
                    'url' => 'asignar-rol',
                    'active' => ['asignar-rol', 'asignar-rol/*'],
                ],
            ],
        ],
        [
            'text' => 'Reportes',
            'icon' => 'fa fa-file',
            'submenu' => [
                [
                    'text' => 'Ventas',
                    'icon' => 'fa fa-chart-line',
                    'url' => 'reportes/ventas',
                    'active' => ['reportes/ventas', 'reportes/ventas/*'],
                ],
                [
                    'text' => 'Compras',
                    'icon' => 'fa fa-shopping-cart',
                    'url' => 'reportes/compras',
                    'active' => ['reportes/compras', 'reportes/compras/*'],
                ],
                [
                    'text' => 'Inventario',
                    'icon' => 'fa fa-box',
                    'url' => 'reportes/inventario',
                    'active' => ['reportes/inventario', 'reportes/inventario/*'],
                ],
                [
                    'text' => 'Productos',
                    'icon' => 'fa fa-cubes',
                    'url' => 'reportes/productos',
                    'active' => ['reportes/productos', 'reportes/productos/*'],
                ],
                [
                    'text' => 'Financiero',
                    'icon' => 'fa fa-file-invoice',
                    'url' => 'reportes/financiero',
                    'active' => ['reportes/financiero', 'reportes/financiero/*'],
                ],
            ],
        ],
                
    ],

    /*
    |-------------------------------------------------------------------------- 
    | Menu Filters 
    |-------------------------------------------------------------------------- 
    */
    'filters' => [
        JeroenNoten\LaravelAdminLte\Menu\Filters\GateFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\HrefFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\SearchFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\ActiveFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\ClassesFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\LangFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\DataFilter::class,
    ],

    /*
    |-------------------------------------------------------------------------- 
    | Plugins Initialization 
    |-------------------------------------------------------------------------- 
    */
    'plugins' => [
        'Datatables' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js',
                ],
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js',
                ],
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css',
                ],
            ],
        ],
        'Select2' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js',
                ],
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.css',
                ],
            ],
        ],
        'Chartjs' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.0/Chart.bundle.min.js',
                ],
            ],
        ],
        'Sweetalert2' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdn.jsdelivr.net/npm/sweetalert2@8',
                ],
            ],
        ],
        'Pace' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/themes/blue/pace-theme-center-radar.min.css',
                ],
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/pace.min.js',
                ],
            ],
        ],
    ],

    /*
    |-------------------------------------------------------------------------- 
    | IFrame 
    |-------------------------------------------------------------------------- 
    */
    'iframe' => [
        'default_tab' => [
            'url' => null,
            'title' => null,
        ],
        'buttons' => [
            'close' => true,
            'close_all' => true,
            'close_all_other' => true,
            'scroll_left' => true,
            'scroll_right' => true,
            'fullscreen' => true,
        ],
        'options' => [
            'loading_screen' => 1000,
            'auto_show_new_tab' => true,
            'use_navbar_items' => true,
        ],
    ],

    /*
    |-------------------------------------------------------------------------- 
    | Livewire 
    |-------------------------------------------------------------------------- 
    */
    'livewire' => false,
];
