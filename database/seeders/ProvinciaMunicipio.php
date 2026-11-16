<?php

namespace Database\Seeders;

use App\Models\Municipio;
use App\Models\Provincia;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProvinciaMunicipio extends Seeder
{
    /**
     * Provincias y municipios de Cuba.
     *
     * Fuente territorial:
     * https://es.wikipedia.org/wiki/Municipios_de_Cuba
     *
     * Cuba:
     * - 15 provincias
     * - 1 municipio especial: Isla de la Juventud
     * - 168 municipios en total
     */
    private const TERRITORIO = [
        'Pinar del Río' => [
            'Consolación del Sur',
            'Guane',
            'La Palma',
            'Los Palacios',
            'Mantua',
            'Minas de Matahambre',
            'Pinar del Río',
            'San Juan y Martínez',
            'San Luis',
            'Sandino',
            'Viñales',
        ],

        'Artemisa' => [
            'Alquízar',
            'Artemisa',
            'Bahía Honda',
            'Bauta',
            'Caimito',
            'Candelaria',
            'Guanajay',
            'Güira de Melena',
            'Mariel',
            'San Antonio de los Baños',
            'San Cristóbal',
        ],

        'La Habana' => [
            'Arroyo Naranjo',
            'Boyeros',
            'Centro Habana',
            'Cerro',
            'Cotorro',
            'Diez de Octubre',
            'Guanabacoa',
            'La Habana del Este',
            'La Habana Vieja',
            'La Lisa',
            'Marianao',
            'Playa',
            'Plaza de la Revolución',
            'Regla',
            'San Miguel del Padrón',
        ],

        'Mayabeque' => [
            'Batabanó',
            'Bejucal',
            'Güines',
            'Jaruco',
            'Madruga',
            'Melena del Sur',
            'Nueva Paz',
            'Quivicán',
            'San José de las Lajas',
            'San Nicolás',
            'Santa Cruz del Norte',
        ],

        'Matanzas' => [
            'Calimete',
            'Cárdenas',
            'Ciénaga de Zapata',
            'Colón',
            'Jagüey Grande',
            'Jovellanos',
            'Limonar',
            'Los Arabos',
            'Martí',
            'Matanzas',
            'Pedro Betancourt',
            'Perico',
            'Unión de Reyes',
        ],

        'Cienfuegos' => [
            'Abreus',
            'Aguada de Pasajeros',
            'Cienfuegos',
            'Cruces',
            'Cumanayagua',
            'Lajas',
            'Palmira',
            'Rodas',
        ],

        'Villa Clara' => [
            'Caibarién',
            'Camajuaní',
            'Cifuentes',
            'Corralillo',
            'Encrucijada',
            'Manicaragua',
            'Placetas',
            'Quemado de Güines',
            'Ranchuelo',
            'San Juan de los Remedios',
            'Sagua la Grande',
            'Santa Clara',
            'Santo Domingo',
        ],

        'Sancti Spíritus' => [
            'Cabaiguán',
            'Fomento',
            'Jatibonico',
            'La Sierpe',
            'Sancti Spíritus',
            'Taguasco',
            'Trinidad',
            'Yaguajay',
        ],

        'Ciego de Ávila' => [
            'Baraguá',
            'Bolivia',
            'Chambas',
            'Ciego de Ávila',
            'Ciro Redondo',
            'Florencia',
            'Majagua',
            'Morón',
            'Primero de Enero',
            'Venezuela',
        ],

        'Camagüey' => [
            'Camagüey',
            'Carlos M. de Céspedes',
            'Esmeralda',
            'Florida',
            'Guáimaro',
            'Jimaguayú',
            'Minas',
            'Najasa',
            'Nuevitas',
            'Santa Cruz del Sur',
            'Sibanicú',
            'Sierra de Cubitas',
            'Vertientes',
        ],

        'Las Tunas' => [
            'Amancio',
            'Colombia',
            'Jesús Menéndez',
            'Jobabo',
            'Las Tunas',
            'Majibacoa',
            'Manatí',
            'Puerto Padre',
        ],

        'Granma' => [
            'Bartolomé Masó',
            'Bayamo',
            'Buey Arriba',
            'Campechuela',
            'Cauto Cristo',
            'Guisa',
            'Jiguaní',
            'Manzanillo',
            'Media Luna',
            'Niquero',
            'Pilón',
            'Río Cauto',
            'Yara',
        ],

        'Holguín' => [
            'Antilla',
            'Báguanos',
            'Banes',
            'Cacocum',
            'Calixto García',
            'Cueto',
            'Frank País',
            'Gibara',
            'Holguín',
            'Mayarí',
            'Moa',
            'Rafael Freyre',
            'Sagua de Tánamo',
            'Urbano Noris',
        ],

        'Santiago de Cuba' => [
            'Contramaestre',
            'Guamá',
            'Mella',
            'Palma Soriano',
            'San Luis',
            'Santiago de Cuba',
            'Segundo Frente',
            'Songo-La Maya',
            'Tercer Frente',
        ],

        'Guantánamo' => [
            'Baracoa',
            'Caimanera',
            'El Salvador',
            'Guantánamo',
            'Imías',
            'Maisí',
            'Manuel Tames',
            'Niceto Pérez',
            'San Antonio del Sur',
            'Yateras',
        ],
    ];

    /**
     * Municipio especial fuera de cualquier provincia.
     */
    private const MUNICIPIOS_ESPECIALES = [
        'Isla de la Juventud',
    ];

    public function run(): void
    {
        DB::transaction(function (): void {
            $orden = 1;
            foreach (self::TERRITORIO as $nombreProvincia => $nombresMunicipios) {
                $provincia = Provincia::updateOrCreate(
                    [
                        'slug' => Str::slug($nombreProvincia),
                    ],
                    [
                        'nombre' => $nombreProvincia,
                        'activa' => true,
                        'orden' => $orden++,
                    ]
                );

                foreach ($nombresMunicipios as $nombreMunicipio) {
                    $slug = Str::slug($nombreMunicipio);

                    if ($nombreProvincia === 'Santiago de Cuba' && $nombreMunicipio === 'San Luis') {
                        $slug .= '-sc';
                    }

                    Municipio::updateOrCreate(
                        ['slug' => $slug],
                        [
                            'provincia_id' => $provincia->id,
                            'nombre' => $nombreMunicipio,
                            'activo' => true,
                        ]
                    );
                }
            }

            foreach (self::MUNICIPIOS_ESPECIALES as $nombreMunicipio) {
                Municipio::updateOrCreate(
                    [
                        'slug' => Str::slug($nombreMunicipio),
                    ],
                    [
                        'provincia_id' => null,
                        'nombre' => $nombreMunicipio,
                        'activo' => true,
                    ]
                );
            }
        });
    }
}