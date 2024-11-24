<?php

return [
    [
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
];
