<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Local;
use App\Models\Cliente;
use App\Models\Categoria;
use App\Models\Producto;
use App\Models\PeriodoContable;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1️⃣ Monedas reales
        $this->call(MonedaSeeder::class);
        $this->call(RolePermissionSeeder::class);
        $this->call(UnidadMedidaSeeder::class);

        // 2️⃣ Locales
        $locales = Local::factory(3)->create();

        // 3️⃣ Clientes
        Cliente::factory(15)->create();

        // 4️⃣ Categorías + productos
        $categoriaNombres = [
            'Electrodomesticos', 'Memorias USB', 'Telefonos', 'Pantallas', 'Forros',
            'Tenis precio bajo', 'Tenis precio medio', 'Tenis precio alto',
            'Accesorios', 'Ropa deportiva', 'Cuidado personal', 'Hogar', 'Cocina',
            'Videojuegos', 'Juguetes', 'Muebles', 'Herramientas', 'Audio', 'Decoracion', 'Relojes'
        ];

        foreach ($categoriaNombres as $catNombre) {
            foreach ($locales as $local) {
                $categoria = Categoria::factory()->create([
                    'nombre' => $catNombre,
                    'local_id' => $local->id
                ]);

                // 10 productos por categoria
                Producto::factory(10)->create([
                    'categoria_id' => $categoria->id,
                    'local_id' => $local->id,
                ]);
            }
        }

        // 5️⃣ Periodos contables por local
        foreach ($locales as $local) {
            $fechaInicio = now()->startOfWeek(); // lunes
            for ($i=1; $i <= 5; $i++) {
                PeriodoContable::factory()->create([
                    'local_id' => $local->id,
                    'fecha_inicio' => $fechaInicio,
                    'fecha_fin' => (clone $fechaInicio)->addDays(6),
                    'activo' => $i === 1,   // solo el primero activo
                    'cerrado' => false,
                    'nombre' => 'Periodo ' . $i,
                ]);

                // siguiente periodo inicia el lunes siguiente
                $fechaInicio = $fechaInicio->addWeek();
            }
        }

        $user = new User();
        $user->name = 'Yunger Azcuy Torrens';
        $user->email = 'fergutiadm@gmail.com';
        $user->password = Hash::make('password');
        $user->save();
        $user->assignRole('admin');
        $users = User::factory(3)->create();

    }
}
