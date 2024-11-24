<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Crear permisos para cada módulo
        $permissions = [
            'productos.ver', 'productos.crear', 'productos.editar', 'productos.eliminar',
            'inventario.ver', 'inventario.crear', 'inventario.editar', 'inventario.eliminar',
            'categorias.ver', 'proveedores.ver', 'sucursales.ver', 'metodos-pagos.ver',
            'bodegas.ver', 'movimientos.ver', 'facturas.ver', 'unidades.ver',
            'cajas.ver', 'cajas.crear', 'cajas.cerrar',
            'ventas.ver', 'ventas.crear', 'ventas.imprimir',
            'ordenes-compras.ver', 'ordenes-compras.crear', 'ordenes-compras.editar', 'ordenes-compras.eliminar',
            'guias-despacho.ver', 'pagos.ver', 'reportes.ventas', 'reportes.inventario', 'reportes.compras', 'reportes.financieros'
        ];

        foreach ($permissions as $permission) {
            if (!Permission::where('name', $permission)->exists()) {
                Permission::create(['name' => $permission]);
            }
        }

        // Crear roles con validación
        if (!Role::where('name', 'root')->exists()) {
            $root = Role::create(['name' => 'root']);
            $root->givePermissionTo(Permission::all());
        }

        if (!Role::where('name', 'administrador')->exists()) {
            $admin = Role::create(['name' => 'administrador']);
            $admin->givePermissionTo(Permission::all());
        }

        if (!Role::where('name', 'bodeguero')->exists()) {
            $bodeguero = Role::create(['name' => 'bodeguero']);
            $bodeguero->givePermissionTo([
                'ordenes-compras.ver', 'ordenes-compras.crear', 'ordenes-compras.editar', 'ordenes-compras.eliminar',
                'guias-despacho.ver', 'facturas.ver', 'pagos.ver',
                'inventario.ver', 'productos.ver', 'productos.crear', 'productos.editar', 'productos.eliminar'
            ]);
        }

        if (!Role::where('name', 'vendedor')->exists()) {
            $vendedor = Role::create(['name' => 'vendedor']);
            $vendedor->givePermissionTo([
                'ventas.ver', 'ventas.crear', 'ventas.imprimir',
                'cajas.ver', 'cajas.crear', 'cajas.cerrar'
            ]);
        }
    }
}
