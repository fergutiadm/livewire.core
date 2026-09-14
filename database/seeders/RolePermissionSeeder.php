<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // 1️⃣ Permisos del sistema
        $permissions = [
            // Publico / ecommerce
            'ver catalogo',
            'ver producto',
            'comprar producto',

            // Ventas local
            'registrar venta',
            'ver ventas',

            // Inventario
            'ver inventario',

            // Gestión contable
            'ver gastos',
            'ver periodos contables',

            // Sistema
            'acceder panel interno',

            // Promoción
            'ver promociones',
            'crear promociones',
            'ver metricas promocion',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        // 2️⃣ Roles iniciales
        $roles = [
            'admin',
            'vendedor',
            'cliente',
            'gestor',    // para promoción
            'manager',   // dirección del negocio
        ];

        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName]);
        }

        // 3️⃣ Asignación de permisos por rol

        Role::findByName('admin')->givePermissionTo(Permission::all());

        Role::findByName('vendedor')->givePermissionTo([
            'acceder panel interno',
            'ver catalogo',
            'ver producto',
            'registrar venta',
            'ver ventas',
            'ver inventario',
        ]);

        Role::findByName('cliente')->givePermissionTo([
            'ver catalogo',
            'ver producto',
            'comprar producto',
        ]);

        Role::findByName('gestor')->givePermissionTo([
            'ver catalogo',
            'ver producto',
            'ver promociones',
            'crear promociones',
            'ver metricas promocion',
        ]);

        Role::findByName('manager')->givePermissionTo([
            'acceder panel interno',
            'ver catalogo',
            'ver producto',
            'ver ventas',
            'ver inventario',
            'ver periodos contables',
            'ver gastos',
        ]);

        $this->command->info('Roles y permisos creados correctamente.');
    }
}
