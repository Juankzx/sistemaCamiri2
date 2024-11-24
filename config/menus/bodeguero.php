<?php
return [
    [
        'text' => 'Inventario',
        'icon' => 'fa solid fa-warehouse',
        'submenu' => [
            [
                'text' => 'Productos',
                'icon' => 'fa solid fa-warehouse',
                'url' => 'productos',
            ],
            [
                'text' => 'Movimientos Inventario',
                'icon' => 'fa solid fa-warehouse',
                'url' => 'movimientos',
            ],
            [
                'text' => 'Inventarios',
                'icon' => 'fa solid fa-warehouse',
                'url' => 'inventarios',
                'active' => ['inventarios', 'inventarios/*'],
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
            ],
            [
                'text' => 'Guias de Despacho',
                'icon' => 'fa solid fa-cart-plus',
                'url' => 'guias-despacho',
            ],
            [
                'text' => 'Facturas',
                'icon' => 'fa solid fa-cart-plus',
                'url' => 'facturas',
            ],
            [
                'text' => 'Pagos',
                'icon' => 'fa solid fa-cart-plus',
                'url' => 'pagos',
            ],
        ],
    ],
    // Agrega otras opciones para 'bodeguero'
];
