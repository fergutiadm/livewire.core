<?php

namespace Database\Seeders;

use App\Models\Banco;
use App\Models\BancoSucursal;
use App\Models\Municipio;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class BancoSucursalSeeder extends Seeder
{
    /**
     * Sucursales identificadas en directorios públicos.
     *
     * banco: alias del registro en bancos.
     * municipio: slug del municipio.
     * codigo: código oficial cuando está publicado.
     *
     * Los códigos no publicados se dejan en NULL.
     */
    private const SUCURSALES = [

        /*
        |--------------------------------------------------------------------------
        | PINAR DEL RÍO
        |--------------------------------------------------------------------------
        */

        [
            'banco' => 'BANDEC',
            'municipio' => 'vinales',
            'nombre' => 'Sucursal Viñales',
            'codigo' => '1181',
            'direccion' => 'Salvador Cisneros e/ Ceferino Fernández y Joaquín Pérez',
            'telefonos' => null,
        ],
        [
            'banco' => 'BANDEC',
            'municipio' => 'consolacion-del-sur',
            'nombre' => 'Sucursal Consolación del Sur',
            'codigo' => '1461',
            'direccion' => 'Ave. 51 No. 5216 e/ 52 y 54',
            'telefonos' => null,
        ],
        [
            'banco' => 'BANDEC',
            'municipio' => 'san-luis',
            'nombre' => 'Sucursal San Luis',
            'codigo' => '1581',
            'direccion' => 'Juana Romero No. 188 e/ Leonardo Mármol y Capitán Mariano',
            'telefonos' => null,
        ],
        [
            'banco' => 'BANDEC',
            'municipio' => 'pinar-del-rio',
            'nombre' => 'Sucursal Pinar del Río',
            'codigo' => '1591',
            'direccion' => 'Martí No. 11 Este e/ Gerardo Medina y Osmani Arenado',
            'telefonos' => null,
        ],
        [
            'banco' => 'BANDEC',
            'municipio' => 'san-juan-y-martinez',
            'nombre' => 'Sucursal San Juan y Martínez',
            'codigo' => '1621',
            'direccion' => 'Francisco Rivera No. 27 e/ Libertad y Padre Bentín',
            'telefonos' => null,
        ],
        [
            'banco' => 'BFI',
            'municipio' => 'pinar-del-rio',
            'nombre' => 'Sucursal Pinar del Río',
            'codigo' => null,
            'direccion' => 'Gerardo Medina No. 46',
            'telefonos' => '48778213',
        ],

        /*
        |--------------------------------------------------------------------------
        | ARTEMISA
        |--------------------------------------------------------------------------
        */

        [
            'banco' => 'BANDEC',
            'municipio' => 'mariel',
            'nombre' => 'Sucursal Mariel',
            'codigo' => '1741',
            'direccion' => 'Calle 128 No. 6307 e/ 63 y 65',
            'telefonos' => null,
        ],
        [
            'banco' => 'BANDEC',
            'municipio' => 'bauta',
            'nombre' => 'Sucursal Bauta',
            'codigo' => '1821',
            'direccion' => 'Calle 251 No. 14607 e/ 146 y 148',
            'telefonos' => null,
        ],
        [
            'banco' => 'BANDEC',
            'municipio' => 'guira-de-melena',
            'nombre' => 'Sucursal Güira de Melena',
            'codigo' => '2241',
            'direccion' => 'Ave. 97 No. 8401 e/ 84 y 86',
            'telefonos' => null,
        ],
        [
            'banco' => 'BANDEC',
            'municipio' => 'alquizar',
            'nombre' => 'Sucursal Alquízar',
            'codigo' => '2261',
            'direccion' => 'Ave. 91 No. 7819 e/ 78 y 80',
            'telefonos' => null,
        ],
        [
            'banco' => 'BANDEC',
            'municipio' => 'artemisa',
            'nombre' => 'Sucursal Artemisa',
            'codigo' => '2281',
            'direccion' => 'Martí No. 2509 e/ 25 y 27',
            'telefonos' => null,
        ],
        [
            'banco' => 'BANDEC',
            'municipio' => 'caimito',
            'nombre' => 'Sucursal Caimito',
            'codigo' => null,
            'direccion' => 'Calle 41 No. 3441',
            'telefonos' => '47319113',
        ],
        [
            'banco' => 'BANDEC',
            'municipio' => 'guanajay',
            'nombre' => 'Sucursal Guanajay',
            'codigo' => null,
            'direccion' => 'Calle 63 No. 5624',
            'telefonos' => '47306015',
        ],
        [
            'banco' => 'BANDEC',
            'municipio' => 'san-antonio-de-los-banos',
            'nombre' => 'Sucursal San Antonio de los Baños',
            'codigo' => null,
            'direccion' => 'Calle 41 No. 6004',
            'telefonos' => '473822299',
        ],
        [
            'banco' => 'BPA',
            'municipio' => 'alquizar',
            'nombre' => 'Sucursal Alquízar',
            'codigo' => null,
            'direccion' => 'Calle 91 No. 7817',
            'telefonos' => '47418423',
        ],
        [
            'banco' => 'BPA',
            'municipio' => 'artemisa',
            'nombre' => 'Sucursal Artemisa - Calle 33',
            'codigo' => null,
            'direccion' => 'Calle 33 No. 4403',
            'telefonos' => '47362265',
        ],
        [
            'banco' => 'BPA',
            'municipio' => 'artemisa',
            'nombre' => 'Sucursal Artemisa - Calle 20',
            'codigo' => null,
            'direccion' => 'Calle 20 y 27',
            'telefonos' => '47362422',
        ],
        [
            'banco' => 'BPA',
            'municipio' => 'bauta',
            'nombre' => 'Sucursal Bauta',
            'codigo' => null,
            'direccion' => 'Calle 251 No. 14218',
            'telefonos' => '47373294',
        ],
        [
            'banco' => 'BPA',
            'municipio' => 'caimito',
            'nombre' => 'Sucursal Caimito',
            'codigo' => null,
            'direccion' => 'Calle 41 No. 4210',
            'telefonos' => '47319513',
        ],
        [
            'banco' => 'BPA',
            'municipio' => 'guanajay',
            'nombre' => 'Sucursal Guanajay',
            'codigo' => null,
            'direccion' => 'Calle 63 No. 5622',
            'telefonos' => '47306837',
        ],
        [
            'banco' => 'BPA',
            'municipio' => 'guira-de-melena',
            'nombre' => 'Sucursal Güira de Melena',
            'codigo' => null,
            'direccion' => 'Calle 84 No. 10103',
            'telefonos' => '47424275',
        ],
        [
            'banco' => 'BPA',
            'municipio' => 'san-antonio-de-los-banos',
            'nombre' => 'Sucursal San Antonio de los Baños',
            'codigo' => null,
            'direccion' => 'Calle 41 No. 6601',
            'telefonos' => '47382642',
        ],

        /*
        |--------------------------------------------------------------------------
        | LA HABANA
        |--------------------------------------------------------------------------
        */

        [
            'banco' => 'BANDEC',
            'municipio' => 'la-habana-vieja',
            'nombre' => 'Sucursal Habana Vieja',
            'codigo' => '2301',
            'direccion' => 'Calle Cuba No. 469 e/ Amargura y Teniente Rey',
            'telefonos' => null,
        ],
        [
            'banco' => 'BANDEC',
            'municipio' => 'plaza-de-la-revolucion',
            'nombre' => 'Sucursal Vedado',
            'codigo' => '2611',
            'direccion' => 'Calle 23 No. 171 e/ N y O',
            'telefonos' => null,
        ],
       // BANCO METROPOLITANO S.A. — sucursales ordinarias de La Habana

// Arroyo Naranjo
['banco' => 'Banmet', 'municipio' => 'arroyo-naranjo', 'nombre' => 'Quinta Canaria', 'codigo' => '327', 'direccion' => 'Calzada de Bejucal No. 1626 e/ 7ma y Lindero', 'telefonos' => '7643-8065, 7644-3995'],
['banco' => 'Banmet', 'municipio' => 'arroyo-naranjo', 'nombre' => 'EXPOCUBA', 'codigo' => '329', 'direccion' => 'Carretera del Rocío Km 13 1/2', 'telefonos' => '7697-4347'],
['banco' => 'Banmet', 'municipio' => 'arroyo-naranjo', 'nombre' => 'Managua', 'codigo' => '331', 'direccion' => 'Independencia #1055 e/ Esperanza y M. Elena', 'telefonos' => '7609-8345'],
['banco' => 'Banmet', 'municipio' => 'arroyo-naranjo', 'nombre' => 'La Güinera', 'codigo' => '332', 'direccion' => '3ra e/ C y D, Rpto. Rosario, La Güinera', 'telefonos' => '7643-4266, 7643-8794'],
['banco' => 'Banmet', 'municipio' => 'arroyo-naranjo', 'nombre' => '10 de Octubre y Bellavista', 'codigo' => '333', 'direccion' => 'Calzada 10 de Octubre #1771 e/ Bellavista y Kessel', 'telefonos' => '7643-8069'],
['banco' => 'Banmet', 'municipio' => 'arroyo-naranjo', 'nombre' => 'La Lira', 'codigo' => '334', 'direccion' => 'Calzada de Managua #267 e/ Alegría y Sanguily', 'telefonos' => '7643-8292, 7647-8754'],

// Boyeros
['banco' => 'Banmet', 'municipio' => 'boyeros', 'nombre' => 'Altahabana', 'codigo' => '321', 'direccion' => 'Calle L No. 15802 e/ 7ma y N', 'telefonos' => '7644-8779, 7644-4002'],
['banco' => 'Banmet', 'municipio' => 'boyeros', 'nombre' => 'Altahabana', 'codigo' => '632', 'direccion' => 'Tres Palmas y Final', 'telefonos' => '7647-2193'],
['banco' => 'Banmet', 'municipio' => 'boyeros', 'nombre' => '100 y Boyeros', 'codigo' => '326', 'direccion' => 'Ave. Independencia 38501 esq. 100', 'telefonos' => '7643-4231, 7643-8120, 7647-8752, 7643-8519'],
['banco' => 'Banmet', 'municipio' => 'boyeros', 'nombre' => 'Wajay', 'codigo' => '322', 'direccion' => 'Calle 276 No. 26901 esq. 269, Wajay', 'telefonos' => '7645-0870'],
['banco' => 'Banmet', 'municipio' => 'boyeros', 'nombre' => 'Santiago de las Vegas', 'codigo' => '323', 'direccion' => 'Calle 13 No. 18808 e/ 2 y 4', 'telefonos' => '7683-3770, 7683-3553'],
['banco' => 'Banmet', 'municipio' => 'boyeros', 'nombre' => 'Boyeros', 'codigo' => '324', 'direccion' => 'Ave. 184 No. 31101 e/ 311 y 313', 'telefonos' => '7683-2916, 7683-4790, 7684-9510'],
['banco' => 'Banmet', 'municipio' => 'boyeros', 'nombre' => 'Aeropuerto José Martí', 'codigo' => '328', 'direccion' => 'Aeropuerto José Martí', 'telefonos' => '7266-4644, 7649-7355, 7649-7356'],

// Centro Habana
['banco' => 'Banmet', 'municipio' => 'centro-habana', 'nombre' => 'Belascoaín y San José', 'codigo' => '254', 'direccion' => 'Belascoaín No. 360 e/ San José y San Rafael', 'telefonos' => '7870-8526, 7879-8830, 7870-8187'],
['banco' => 'Banmet', 'municipio' => 'centro-habana', 'nombre' => 'Infanta y San José', 'codigo' => '255', 'direccion' => 'Ave. Menocal No. 503 e/ Valle y San José', 'telefonos' => '7870-6496, 7878-7148, 7878-2258'],
['banco' => 'Banmet', 'municipio' => 'centro-habana', 'nombre' => 'Galiano y Reina', 'codigo' => '257', 'direccion' => 'Ave. Italia #568 esq. Reina', 'telefonos' => '7862-4331, 7862-4542'],
['banco' => 'Banmet', 'municipio' => 'centro-habana', 'nombre' => 'Galiano y San José', 'codigo' => '258', 'direccion' => 'Galiano #452 esq. San José', 'telefonos' => '7862-6523, 7861-3161, 7862-0665-66'],
['banco' => 'Banmet', 'municipio' => 'centro-habana', 'nombre' => 'Belascoaín y Zanja', 'codigo' => '259', 'direccion' => 'Belascoaín #452 esq. Zanja', 'telefonos' => '7870-8959, 7873-5738, 7870-3088'],

// Cerro
['banco' => 'Banmet', 'municipio' => 'cerro', 'nombre' => 'INDER', 'codigo' => '301', 'direccion' => 'Boyeros y Vía Blanca', 'telefonos' => '7881-7069, 7640-3630'],
['banco' => 'Banmet', 'municipio' => 'cerro', 'nombre' => 'Casino Deportivo', 'codigo' => '303', 'direccion' => 'Calle 4ta #10410 e/ 3ra y 5ta', 'telefonos' => '7649-1006'],
['banco' => 'Banmet', 'municipio' => 'cerro', 'nombre' => 'Cerro y Arzobispo', 'codigo' => '304', 'direccion' => 'Calzada del Cerro No. 1802 esq. Arzobispo', 'telefonos' => '7648-7218'],
['banco' => 'Banmet', 'municipio' => 'cerro', 'nombre' => 'Infanta y Manglar', 'codigo' => '305', 'direccion' => 'Manglar #580 esq. Infanta', 'telefonos' => '7878-2247'],
['banco' => 'Banmet', 'municipio' => 'cerro', 'nombre' => 'Ayestarán y 19 de Mayo', 'codigo' => '306', 'direccion' => 'Ayestarán No. 209 esq. 19 de Mayo', 'telefonos' => '7870-6509, 7870-6500, 7870-3789, 7870-4516'],
['banco' => 'Banmet', 'municipio' => 'cerro', 'nombre' => 'Ayestarán y Aranguren', 'codigo' => '307', 'direccion' => 'Ayestarán #424 e/ Aranguren y 19 de Mayo', 'telefonos' => '7873-5739, 7878-3135, 7879-3588'],

// Cotorro
['banco' => 'Banmet', 'municipio' => 'cotorro', 'nombre' => 'Cotorro', 'codigo' => '336', 'direccion' => 'Ave. 101 s/n e/ 22 y 24, Rpto. Centro Cotorro', 'telefonos' => '7682-4170, 7682-7581, 7682-1784'],
['banco' => 'Banmet', 'municipio' => 'cotorro', 'nombre' => 'Las Brisas', 'codigo' => '338', 'direccion' => 'Ave. 71 #2802 e/ 28 y 30, Rpto. Brisas', 'telefonos' => '7682-6310, 7682-1827, 7682-6960'],

// Diez de Octubre
['banco' => 'Banmet', 'municipio' => 'diez-de-octubre', 'nombre' => 'Concha y Luyanó', 'codigo' => '293', 'direccion' => 'Calzada de Luyanó No. 668 esq. Concha', 'telefonos' => '7699-4234, 7699-3898'],
['banco' => 'Banmet', 'municipio' => 'diez-de-octubre', 'nombre' => '10 de Octubre y Patrocinio', 'codigo' => '294', 'direccion' => '10 de Octubre No. 1313 esq. Patrocinio', 'telefonos' => '7641-4342'],
['banco' => 'Banmet', 'municipio' => 'diez-de-octubre', 'nombre' => '10 de Octubre y Luyanó', 'codigo' => '295', 'direccion' => '10 de Octubre No. 415 esq. Luyanó', 'telefonos' => '7641-7042, 7640-3531'],
['banco' => 'Banmet', 'municipio' => 'diez-de-octubre', 'nombre' => 'Juan Delgado y Lacret', 'codigo' => '296', 'direccion' => 'Lacret #471-475 esq. Juan Delgado', 'telefonos' => '7641-7384, 7641-6441'],
['banco' => 'Banmet', 'municipio' => 'diez-de-octubre', 'nombre' => 'Mónaco', 'codigo' => '297', 'direccion' => 'Mayía Rodríguez #628', 'telefonos' => '7648-7201, 7641-5562'],
['banco' => 'Banmet', 'municipio' => 'diez-de-octubre', 'nombre' => 'Dolores y 18', 'codigo' => '298', 'direccion' => 'Ave. Camilo Cienfuegos No. 863 e/ 18 y 19', 'telefonos' => '7699-4155, 7698-3462'],
['banco' => 'Banmet', 'municipio' => 'diez-de-octubre', 'nombre' => '10 de Octubre y Concepción', 'codigo' => '299', 'direccion' => '10 de Octubre No. 969 esq. Concepción', 'telefonos' => '7640-3041, 7640-9252'],
['banco' => 'Banmet', 'municipio' => 'diez-de-octubre', 'nombre' => '10 de Octubre y Lacret', 'codigo' => '300', 'direccion' => '10 de Octubre #958 esq. Lacret', 'telefonos' => '7648-5327 al 30'],
['banco' => 'Banmet', 'municipio' => 'diez-de-octubre', 'nombre' => 'Concha y Fábrica', 'codigo' => '302', 'direccion' => 'Concha #452 esq. Fábrica', 'telefonos' => '7696-7431, 7696-7188'],

// Guanabacoa
['banco' => 'Banmet', 'municipio' => 'guanabacoa', 'nombre' => 'Guanabacoa', 'codigo' => '279', 'direccion' => 'Pepe A. No. 264 e/ Máximo Gómez y Maceo', 'telefonos' => '7797-8429, 7797-7510, 7797-9402'],
['banco' => 'Banmet', 'municipio' => 'guanabacoa', 'nombre' => 'Guanabacoa', 'codigo' => '280', 'direccion' => 'Martí #66 esq. Versalles', 'telefonos' => '7797-9186, 7797-0316 al 18'],

// La Habana del Este
['banco' => 'Banmet', 'municipio' => 'la-habana-del-este', 'nombre' => 'Guanabo', 'codigo' => '271', 'direccion' => '5ta avenida #47810 e/ 478 y 480', 'telefonos' => '7796-2269, 7796-4461'],
['banco' => 'Banmet', 'municipio' => 'la-habana-del-este', 'nombre' => 'Habana del Este', 'codigo' => '272', 'direccion' => 'Edif. 48, Centro Comercial', 'telefonos' => '7768-3774, 7768-3775'],
['banco' => 'Banmet', 'municipio' => 'la-habana-del-este', 'nombre' => 'Alamar', 'codigo' => '273', 'direccion' => 'Centro Comercial 3, Microdistrito 3', 'telefonos' => '7765-2606, 7765-0264, 7765-1879, 7762-0448'],
['banco' => 'Banmet', 'municipio' => 'la-habana-del-este', 'nombre' => 'Alamar', 'codigo' => '274', 'direccion' => 'Centro Comercial #3, Zona #6', 'telefonos' => '7763-9265, 7765-1086, 7765-3498'],
['banco' => 'Banmet', 'municipio' => 'la-habana-del-este', 'nombre' => 'Guanabo', 'codigo' => '275', 'direccion' => 'Ave. 5ta e/ 470 y 472, Guanabo', 'telefonos' => '7796-3320, 7796-5282, 7796-2320, 7796-4543'],
['banco' => 'Banmet', 'municipio' => 'la-habana-del-este', 'nombre' => 'Villa Panamericana', 'codigo' => '276', 'direccion' => 'Ave. 78 esq. 5ta D, Villa Panamericana', 'telefonos' => '7766-4167 y 68, 7766-2652'],
['banco' => 'Banmet', 'municipio' => 'la-habana-del-este', 'nombre' => 'Guiteras', 'codigo' => '277', 'direccion' => 'Ave. 15, Edif. 12 Plantas, Guiteras', 'telefonos' => '7766-3600, 7766-2358, 7766-4196'],
['banco' => 'Banmet', 'municipio' => 'la-habana-del-este', 'nombre' => 'Alamar Nuevo', 'codigo' => '278', 'direccion' => 'Edif. 12 Plantas, Zona 1, Alamar', 'telefonos' => '7765-2845, 7765-2831'],
['banco' => 'Banmet', 'municipio' => 'la-habana-del-este', 'nombre' => 'Academia Militar', 'codigo' => '284', 'direccion' => 'Vía Blanca Km 13 1/2, Alamar, Habana del Este', 'telefonos' => '7762-9469, 7762-9470'],
['banco' => 'Banmet', 'municipio' => 'la-habana-del-este', 'nombre' => 'Campo Florido', 'codigo' => '289', 'direccion' => 'Martí No. 262, Campo Florido', 'telefonos' => '7790-1222, 7790-1646'],

// La Habana Vieja
['banco' => 'Banmet', 'municipio' => 'la-habana-vieja', 'nombre' => 'O’Reilly y Cuba', 'codigo' => '262', 'direccion' => 'Cuba y O’Reilly 225', 'telefonos' => '7866-0204, 7866-3089, ext. 5501, 5502'],
['banco' => 'Banmet', 'municipio' => 'la-habana-vieja', 'nombre' => 'Monte y Carmen', 'codigo' => '263', 'direccion' => 'Máximo Gómez #702 esq. Carmen', 'telefonos' => '7862-9690, 7862-9619, 7861-9661'],
['banco' => 'Banmet', 'municipio' => 'la-habana-vieja', 'nombre' => 'Empedrado y Aguiar', 'codigo' => '265', 'direccion' => 'Empedrado s/n esq. Aguiar', 'telefonos' => '7860-4245, 7861-6362, 7862-5194, 7862-5199'],
['banco' => 'Banmet', 'municipio' => 'la-habana-vieja', 'nombre' => 'O’Reilly y Compostela', 'codigo' => '266', 'direccion' => 'O’Reilly #402 esq. Compostela', 'telefonos' => '7863-1243, 7862-7011 al 16, 7862-7310'],
['banco' => 'Banmet', 'municipio' => 'la-habana-vieja', 'nombre' => 'Prado y Ánimas', 'codigo' => '267', 'direccion' => 'Prado No. 307 e/ Ánimas y Virtudes', 'telefonos' => '7860-4234, 7863-7563'],
['banco' => 'Banmet', 'municipio' => 'la-habana-vieja', 'nombre' => 'San José y Monserrate', 'codigo' => '309', 'direccion' => 'San José y Monserrate', 'telefonos' => '7863-3953 al 56, 7863-8980'],

// La Lisa
['banco' => 'Banmet', 'municipio' => 'la-lisa', 'nombre' => 'La Lisa', 'codigo' => '315', 'direccion' => 'Ave. 51 No. 21201 e/ 212 y 214', 'telefonos' => '7260-9281, 7260-9825, 7265-3718'],
['banco' => 'Banmet', 'municipio' => 'la-lisa', 'nombre' => 'UCI', 'codigo' => '319', 'direccion' => 'Carretera S. Antonio de los Baños, Comunidad Torrens', 'telefonos' => '7835-8401, 7835-8397 al 99, 7835-8395'],
['banco' => 'Banmet', 'municipio' => 'la-lisa', 'nombre' => 'La Lisa', 'codigo' => '320', 'direccion' => 'Calle 286 #4903 e/ 49 y 51, La Lisa', 'telefonos' => '7202-0103, 7204-4938, 7202-0333'],

// Marianao
['banco' => 'Banmet', 'municipio' => 'marianao', 'nombre' => '35 y 118', 'codigo' => '312', 'direccion' => 'Ave. 35 No. 11801 esq. 118', 'telefonos' => '7267-1152, 7260-8230'],
['banco' => 'Banmet', 'municipio' => 'marianao', 'nombre' => '49 y 120', 'codigo' => '313', 'direccion' => 'Ave. 49 No. 12002 esq. 120', 'telefonos' => '7260-7278, 7260-8096, 7261-4734, 7260-3897, 7261-4733'],
['banco' => 'Banmet', 'municipio' => 'marianao', 'nombre' => 'Marianao', 'codigo' => '314', 'direccion' => 'Ave. 51 #11029 e/ 110 y 112', 'telefonos' => '7260-2288, 7267-1186, 7274-2232'],

// Playa
['banco' => 'Banmet', 'municipio' => 'playa', 'nombre' => 'Palacio', 'codigo' => '232', 'direccion' => 'Calle 146 e/ 11 y 13, Rpto. Cubanacán, Playa', 'telefonos' => '7202-6011 ext. 261'],
['banco' => 'Banmet', 'municipio' => 'playa', 'nombre' => '5ta y 112', 'codigo' => '233', 'direccion' => '5ta y 112', 'telefonos' => '7204-9193, 7208-9576'],
['banco' => 'Banmet', 'municipio' => 'playa', 'nombre' => 'Cimex', 'codigo' => '236', 'direccion' => 'Ave. 1ra #40 e/ 2 y 4', 'telefonos' => '7209-3810, 7209-3812, 7205-1620'],
['banco' => 'Banmet', 'municipio' => 'playa', 'nombre' => '42 y 33', 'codigo' => '237', 'direccion' => 'Calle 42 #3304 esq. 33', 'telefonos' => '7202-5108, 7207-6454, 7203-1545'],
['banco' => 'Banmet', 'municipio' => 'playa', 'nombre' => '41 y 46', 'codigo' => '238', 'direccion' => 'Ave. 41 #4414 esq. 46', 'telefonos' => '7202-2501, 7202-6869, 7209-3765, 7205-1623, 7205-1624'],
['banco' => 'Banmet', 'municipio' => 'playa', 'nombre' => 'ELAM', 'codigo' => '239', 'direccion' => 'Carretera Latinoamericana Km 3 1/2, Santa Fe', 'telefonos' => '7202-2501, 7202-6869, 7209-3765, 7205-1623, 7205-1624'],
['banco' => 'Banmet', 'municipio' => 'playa', 'nombre' => 'Santa Fe', 'codigo' => '240', 'direccion' => 'Calle 296 #312 e/ 3ra A y 298, Santa Fe', 'telefonos' => '7209-8816, 7208-8332, 7208-8006'],
['banco' => 'Banmet', 'municipio' => 'playa', 'nombre' => '5ta y 84', 'codigo' => '241', 'direccion' => '5ta avenida esq. 84', 'telefonos' => '7205-1625 al 27, 7202-4056'],
['banco' => 'Banmet', 'municipio' => 'playa', 'nombre' => '42 y 29', 'codigo' => '242', 'direccion' => 'Calle 42 #2714 e/ 27 y 29', 'telefonos' => '7204-9492, 7204-9493, 7204-9495, 7202-7010, 7209-2318, 7202-2508'],
['banco' => 'Banmet', 'municipio' => 'playa', 'nombre' => 'Calle 42 y 31', 'codigo' => '244', 'direccion' => 'Calle 42 #1914 esq. Ave. 31', 'telefonos' => '7204-0389, 7204-0395, 7204-0393, 7203-0159'],

// Plaza de la Revolución
['banco' => 'Banmet', 'municipio' => 'plaza-de-la-revolucion', 'nombre' => '23 y 8', 'codigo' => '243', 'direccion' => 'Calle 23 esq. 8', 'telefonos' => '7830-3391, 7830-2205, 833-71-54, 7833-2131'],
['banco' => 'Banmet', 'municipio' => 'plaza-de-la-revolucion', 'nombre' => '26 y 32', 'codigo' => '245', 'direccion' => 'Calle 26 #967 esq. 32, Nuevo Vedado', 'telefonos' => null],
['banco' => 'Banmet', 'municipio' => 'plaza-de-la-revolucion', 'nombre' => 'MITRANS', 'codigo' => '246', 'direccion' => 'Ave. Carlos M. de Céspedes esq. Tulipán', 'telefonos' => '7881-5233, 7881-0766'],
['banco' => 'Banmet', 'municipio' => 'plaza-de-la-revolucion', 'nombre' => '23 y M. Sánchez', 'codigo' => '247', 'direccion' => 'Calle 23 #1054 esq. Montero Sánchez', 'telefonos' => '7830-0624, 7830-0602, 7830-0392, 7830-1469'],
['banco' => 'Banmet', 'municipio' => 'plaza-de-la-revolucion', 'nombre' => 'FOCSA', 'codigo' => '248', 'direccion' => 'Calle 17 #55 e/ M y N', 'telefonos' => '7832-5300, 7831-0110'],
['banco' => 'Banmet', 'municipio' => 'plaza-de-la-revolucion', 'nombre' => 'San Lázaro y N', 'codigo' => '249', 'direccion' => 'San Lázaro #1158 esq. N', 'telefonos' => '7879-2358'],
['banco' => 'Banmet', 'municipio' => 'plaza-de-la-revolucion', 'nombre' => 'Línea y Paseo', 'codigo' => '250', 'direccion' => 'Línea #705 e/ Paseo y A', 'telefonos' => '7831-3691, 7833-8596, 7832-1910, 7830-1762, 7834-4240'],
['banco' => 'Banmet', 'municipio' => 'plaza-de-la-revolucion', 'nombre' => '23 y J', 'codigo' => '251', 'direccion' => 'Calle 23 esq. J', 'telefonos' => '7836-8759, 7832-5125, 7832-5104, 7836-8758'],
['banco' => 'Banmet', 'municipio' => 'plaza-de-la-revolucion', 'nombre' => 'MICONS', 'codigo' => '252', 'direccion' => '35 e/ Carlos Manuel de Céspedes y A', 'telefonos' => '7881-4145, 7881-7912'],
['banco' => 'Banmet', 'municipio' => 'plaza-de-la-revolucion', 'nombre' => 'Terminal de Ómnibus', 'codigo' => '253', 'direccion' => 'Independencia #101 esq. 19 de Mayo', 'telefonos' => '7879-2074, 7879-4423, 7879-2877'],
['banco' => 'Banmet', 'municipio' => 'plaza-de-la-revolucion', 'nombre' => 'MINAGRI', 'codigo' => '235', 'direccion' => 'Marino s/n e/ Conill y Santa Ana', 'telefonos' => '7881-7688, 7881-8080, 7881-7466, 7881-8039'],
['banco' => 'Banmet', 'municipio' => 'plaza-de-la-revolucion', 'nombre' => 'Comunicaciones', 'codigo' => '256', 'direccion' => 'Boyeros e/ 19 de Mayo y Aranguren', 'telefonos' => '7885-4348'],
['banco' => 'Banmet', 'municipio' => 'plaza-de-la-revolucion', 'nombre' => 'La Rampa', 'codigo' => '264', 'direccion' => '23 #74 e/ P y Malecón, La Rampa', 'telefonos' => '7835-2820, 7836-3329, 7836-3432, 7835-7834'],
['banco' => 'Banmet', 'municipio' => 'plaza-de-la-revolucion', 'nombre' => 'Línea y M', 'codigo' => '281', 'direccion' => 'Línea y M', 'telefonos' => '7833-2061, 7838-3116, 7838-3117, 7834-4241'],
['banco' => 'Banmet', 'municipio' => 'plaza-de-la-revolucion', 'nombre' => 'MINREX', 'codigo' => '283', 'direccion' => 'Calzada #361 e/ G y H, Plaza', 'telefonos' => '7832-7226, 7836-4415, 7838-1570'],
['banco' => 'Banmet', 'municipio' => 'plaza-de-la-revolucion', 'nombre' => 'Consejo de Estado', 'codigo' => '260', 'direccion' => 'Carlos Manuel de Céspedes y Colón', 'telefonos' => '7881-6609'],

// Regla
['banco' => 'Banmet', 'municipio' => 'regla', 'nombre' => 'Regla', 'codigo' => '268', 'direccion' => 'Martí #457 % Agramonte y Céspedes', 'telefonos' => '7794-0870, 7794-1060'],
['banco' => 'Banmet', 'municipio' => 'regla', 'nombre' => 'Regla', 'codigo' => '270', 'direccion' => 'Martí #362 e/ Aranguren y 27 de Noviembre', 'telefonos' => '7797-6131, 7797-3031, 7797-6313'],

// San Miguel del Padrón
['banco' => 'Banmet', 'municipio' => 'san-miguel-del-padron', 'nombre' => 'San Francisco de Paula', 'codigo' => '282', 'direccion' => 'Calzada de Güines esq. Caraballo', 'telefonos' => '7691-5053, 7691-6708'],
['banco' => 'Banmet', 'municipio' => 'san-miguel-del-padron', 'nombre' => 'Los Jazmines', 'codigo' => '285', 'direccion' => 'Calzada de Güines #843 e/ Pepe Prieto y Miranda', 'telefonos' => '7692-0134'],
['banco' => 'Banmet', 'municipio' => 'san-miguel-del-padron', 'nombre' => 'San Miguel del Padrón', 'codigo' => '286', 'direccion' => 'Calzada de Güines No. 74-A e/ Matos y Perkin', 'telefonos' => '7691-6298, 7691-5064'],
['banco' => 'Banmet', 'municipio' => 'san-miguel-del-padron', 'nombre' => 'San Miguel del Padrón', 'codigo' => '288', 'direccion' => 'Calzada de Guanabacoa #144-A e/ Balear y C', 'telefonos' => '7692-8622, 7691-5953'],

        /*
        |--------------------------------------------------------------------------
        | MAYABEQUE
        |--------------------------------------------------------------------------
        */

        [
            'banco' => 'BPA',
            'municipio' => 'guines',
            'nombre' => 'Sucursal Güines - Avenida 91',
            'codigo' => null,
            'direccion' => 'Avenida 91 No. 8603 entre 86 y 90',
            'telefonos' => '47522657',
        ],
        [
            'banco' => 'BPA',
            'municipio' => 'guines',
            'nombre' => 'Sucursal Güines - Avenida 29',
            'codigo' => null,
            'direccion' => 'Avenida 29 No. 1605 entre 16 y 18',
            'telefonos' => '47599123',
        ],
        [
            'banco' => 'BPA',
            'municipio' => 'nueva-paz',
            'nombre' => 'Sucursal Nueva Paz',
            'codigo' => null,
            'direccion' => 'Avenida 27 y 10',
            'telefonos' => '47544489',
        ],
        [
            'banco' => 'BPA',
            'municipio' => 'melena-del-sur',
            'nombre' => 'Sucursal Melena del Sur',
            'codigo' => null,
            'direccion' => 'Calle 30 No. 3718',
            'telefonos' => '47505435',
        ],
        [
            'banco' => 'BPA',
            'municipio' => 'santa-cruz-del-norte',
            'nombre' => 'Sucursal Santa Cruz del Norte',
            'codigo' => null,
            'direccion' => 'Avenida 7ma No. 1609',
            'telefonos' => '47294158',
        ],
        [
            'banco' => 'BPA',
            'municipio' => 'san-jose-de-las-lajas',
            'nombre' => 'Sucursal San José de las Lajas',
            'codigo' => null,
            'direccion' => 'Calle 64 No. 4119',
            'telefonos' => '47862187',
        ],
        [
            'banco' => 'BPA',
            'municipio' => 'batabano',
            'nombre' => 'Sucursal Batabanó',
            'codigo' => null,
            'direccion' => 'Calle 66 No. 6506',
            'telefonos' => '47858420',
        ],
        [
            'banco' => 'BPA',
            'municipio' => 'quivican',
            'nombre' => 'Sucursal Quivicán',
            'codigo' => null,
            'direccion' => 'Calle 21 No. 1052',
            'telefonos' => '47425226',
        ],
        [
            'banco' => 'BPA',
            'municipio' => 'bejucal',
            'nombre' => 'Sucursal Bejucal',
            'codigo' => null,
            'direccion' => 'Calle 10 No. 1315',
            'telefonos' => '47681276',
        ],
        [
            'banco' => 'BPA',
            'municipio' => 'jaruco',
            'nombre' => 'Sucursal Jaruco',
            'codigo' => null,
            'direccion' => 'Avenida 29 No. 1616',
            'telefonos' => '47873207',
        ],
        [
            'banco' => 'BANDEC',
            'municipio' => 'guines',
            'nombre' => 'Sucursal Güines',
            'codigo' => '2101',
            'direccion' => 'Avenida 77 No. 8018 entre 80 y 86',
            'telefonos' => '47522602',
        ],
        [
            'banco' => 'BANDEC',
            'municipio' => 'san-jose-de-las-lajas',
            'nombre' => 'Sucursal San José de las Lajas',
            'codigo' => '1921',
            'direccion' => 'Avenida 47 No. 4813 e/ 48 y 54',
            'telefonos' => '47862346',
        ],
        [
            'banco' => 'BANDEC',
            'municipio' => 'batabano',
            'nombre' => 'Sucursal Batabanó',
            'codigo' => '2141',
            'direccion' => 'Calle 64 No. 6715',
            'telefonos' => '47588343',
        ],
        [
            'banco' => 'BANDEC',
            'municipio' => 'bejucal',
            'nombre' => 'Sucursal Bejucal',
            'codigo' => null,
            'direccion' => 'Calle 10 No. 1315 y 13',
            'telefonos' => '47681535',
        ],
        [
            'banco' => 'BANDEC',
            'municipio' => 'san-nicolas',
            'nombre' => 'Sucursal San Nicolás de Bari',
            'codigo' => null,
            'direccion' => 'Avenida 55 y 32, No. 3211',
            'telefonos' => '47562212',
        ],
        [
            'banco' => 'BANDEC',
            'municipio' => 'melena-del-sur',
            'nombre' => 'Sucursal Melena del Sur',
            'codigo' => null,
            'direccion' => 'Calle 30 y 37, No. 3718',
            'telefonos' => '47505380',
        ],
        [
            'banco' => 'BANDEC',
            'municipio' => 'madruga',
            'nombre' => 'Sucursal Madruga',
            'codigo' => null,
            'direccion' => 'Avenida 29 y 28, No. 2812',
            'telefonos' => '47815248',
        ],
        [
            'banco' => 'BANDEC',
            'municipio' => 'nueva-paz',
            'nombre' => 'Sucursal Nueva Paz',
            'codigo' => null,
            'direccion' => 'Calle 12 y 23, No. 2301',
            'telefonos' => '47544729',
        ],
        [
            'banco' => 'BANDEC',
            'municipio' => 'jaruco',
            'nombre' => 'Sucursal Jaruco',
            'codigo' => null,
            'direccion' => 'Calle 26 y 19, No. 1915',
            'telefonos' => '47873136',
        ],
        [
            'banco' => 'BANDEC',
            'municipio' => 'santa-cruz-del-norte',
            'nombre' => 'Sucursal Santa Cruz del Norte',
            'codigo' => null,
            'direccion' => 'Calle 4ta No. 918',
            'telefonos' => '47293285',
        ],

        /*
        |--------------------------------------------------------------------------
        | MATANZAS
        |--------------------------------------------------------------------------
        */

        [
            'banco' => 'BANDEC',
            'municipio' => 'cardenas',
            'nombre' => 'Sucursal BANDEC Varadero',
            'codigo' => null,
            'direccion' => 'Primera Avenida / Calle 35 y 36, Varadero',
            'telefonos' => '45612616',
        ],
        [
            'banco' => 'BPA',
            'municipio' => 'cardenas',
            'nombre' => 'Sucursal BPA Varadero',
            'codigo' => null,
            'direccion' => 'Calle 36 / Primera Avenida y Autopista Sur, Varadero',
            'telefonos' => '45614270',
        ],
        [
            'banco' => 'BFI',
            'municipio' => 'cardenas',
            'nombre' => 'Sucursal BFI Varadero',
            'codigo' => null,
            'direccion' => 'Primera Avenida / Calle 31 y 32, Varadero',
            'telefonos' => '45667002',
        ],

        /*
        |--------------------------------------------------------------------------
        | CIENFUEGOS
        |--------------------------------------------------------------------------
        */

        [
            'banco' => 'BPA',
            'municipio' => 'cienfuegos',
            'nombre' => 'Sucursal BPA Gazel',
            'codigo' => null,
            'direccion' => 'Calle 35 No. 5603 e/ 56 y 58',
            'telefonos' => '43515380',
        ],
        [
            'banco' => 'BPA',
            'municipio' => 'cienfuegos',
            'nombre' => 'Sucursal BPA Boulevard',
            'codigo' => null,
            'direccion' => 'Avenida 54 esquina a 33',
            'telefonos' => '43518355',
        ],
        [
            'banco' => 'BPA',
            'municipio' => 'cienfuegos',
            'nombre' => 'Sucursal BPA Calzada',
            'codigo' => null,
            'direccion' => 'Avenida 64 e/ 47 y 49',
            'telefonos' => '43520258',
        ],
        [
            'banco' => 'BANDEC',
            'municipio' => 'cienfuegos',
            'nombre' => 'Sucursal BANDEC Argüelles',
            'codigo' => null,
            'direccion' => 'Calle 31 e/ 50 y 52',
            'telefonos' => '43515353',
        ],
        [
            'banco' => 'BANDEC',
            'municipio' => 'cienfuegos',
            'nombre' => 'Sucursal BANDEC San Carlos',
            'codigo' => null,
            'direccion' => 'Avenida 56 e/ 29 y 31',
            'telefonos' => '43516535',
        ],
        [
            'banco' => 'BFI',
            'municipio' => 'cienfuegos',
            'nombre' => 'Sucursal BFI Cienfuegos',
            'codigo' => null,
            'direccion' => 'Avenida 54 esquina a 29',
            'telefonos' => '43551103',
        ],

        /*
        |--------------------------------------------------------------------------
        | SANCTI SPÍRITUS
        |--------------------------------------------------------------------------
        */

        [
            'banco' => 'BANDEC',
            'municipio' => 'trinidad',
            'nombre' => 'Sucursal BANDEC Trinidad',
            'codigo' => null,
            'direccion' => 'José Martí (Jesús María), entre Francisco Javier Zerquera y Colón',
            'telefonos' => '41992392',
        ],
        [
            'banco' => 'BFI',
            'municipio' => 'trinidad',
            'nombre' => 'Sucursal BFI Trinidad',
            'codigo' => null,
            'direccion' => 'Camilo Cienfuegos esquina a José Martí',
            'telefonos' => '41996107',
        ],
        [
            'banco' => 'BPA',
            'municipio' => 'trinidad',
            'nombre' => 'Sucursal BPA Trinidad',
            'codigo' => null,
            'direccion' => 'Colón No. 287, entre Francisco Pettersen y Frank País',
            'telefonos' => '41992578',
        ],

        /*
        |--------------------------------------------------------------------------
        | CIEGO DE ÁVILA
        |--------------------------------------------------------------------------
        */

        [
            'banco' => 'BPA',
            'municipio' => 'ciego-de-avila',
            'nombre' => 'Sucursal BPA Ciego de Ávila - Libertad',
            'codigo' => null,
            'direccion' => 'Calle Libertad No. 10 e/ Honorato del Castillo y Marcial Gómez',
            'telefonos' => '33223308',
        ],
        [
            'banco' => 'BPA',
            'municipio' => 'ciego-de-avila',
            'nombre' => 'Sucursal BPA Ciego de Ávila - Independencia',
            'codigo' => null,
            'direccion' => 'Calle Independencia No. 128 e/ Simón Reyes y Maceo',
            'telefonos' => '33223170',
        ],
        [
            'banco' => 'BANDEC',
            'municipio' => 'ciego-de-avila',
            'nombre' => 'Sucursal BANDEC Ciego de Ávila',
            'codigo' => null,
            'direccion' => 'Calle Independencia No. 152 e/ Honorato del Castillo y Simón Reyes',
            'telefonos' => '33223109',
        ],
        [
            'banco' => 'BANDEC',
            'municipio' => 'moron',
            'nombre' => 'Sucursal BANDEC Morón',
            'codigo' => null,
            'direccion' => 'Calle Martí No. 330 e/ Serafín Sánchez y Gonzalo Arena',
            'telefonos' => '33505162',
        ],
        [
            'banco' => 'BPA',
            'municipio' => 'moron',
            'nombre' => 'Sucursal BPA Morón',
            'codigo' => null,
            'direccion' => 'Calle Martí No. 256 esquina Callejas',
            'telefonos' => '33504415',
        ],
        [
            'banco' => 'BFI',
            'municipio' => 'moron',
            'nombre' => 'Sucursal BFI Cayo Coco',
            'codigo' => null,
            'direccion' => 'Carretera a Cayo Guillermo, Servicentro La Rotonda, Cayo Coco',
            'telefonos' => '33301251',
        ],

        /*
        |--------------------------------------------------------------------------
        | CAMAGÜEY
        |--------------------------------------------------------------------------
        */

        [
            'banco' => 'BPA',
            'municipio' => 'carlos-m-de-cespedes',
            'nombre' => 'Sucursal BPA Céspedes',
            'codigo' => '5652',
            'direccion' => 'Calle 21 No. 101',
            'telefonos' => '32569480',
        ],
        [
            'banco' => 'BANDEC',
            'municipio' => 'carlos-m-de-cespedes',
            'nombre' => 'Sucursal BANDEC Céspedes',
            'codigo' => '5661',
            'direccion' => 'Calle 21 No. 117 e/ 16 y 18',
            'telefonos' => '32569596',
        ],
        [
            'banco' => 'BPA',
            'municipio' => 'esmeralda',
            'nombre' => 'Sucursal BPA Esmeralda',
            'codigo' => '5692',
            'direccion' => 'Jorge Fernández Bello No. 18',
            'telefonos' => '32673298',
        ],
        [
            'banco' => 'BANDEC',
            'municipio' => 'esmeralda',
            'nombre' => 'Sucursal BANDEC Esmeralda',
            'codigo' => '5701',
            'direccion' => 'Martí No. 121',
            'telefonos' => '32673127',
        ],
        [
            'banco' => 'BPA',
            'municipio' => 'sierra-de-cubitas',
            'nombre' => 'Sucursal BPA Sola',
            'codigo' => '5722',
            'direccion' => 'Avenida Mario Herrero Toscano No. 121',
            'telefonos' => '32615339',
        ],
        [
            'banco' => 'BANDEC',
            'municipio' => 'sierra-de-cubitas',
            'nombre' => 'Sucursal BANDEC Sola',
            'codigo' => '5731',
            'direccion' => 'José Martí No. 109 e/ Mario Herrero Toscano y Funcia',
            'telefonos' => '32615181',
        ],
        [
            'banco' => 'BPA',
            'municipio' => 'minas',
            'nombre' => 'Sucursal BPA Lugareño',
            'codigo' => '5742',
            'direccion' => 'Calle 12 No. 22 e/ F y G',
            'telefonos' => '32698137',
        ],
        [
            'banco' => 'BPA',
            'municipio' => 'minas',
            'nombre' => 'Sucursal BPA Minas',
            'codigo' => '5752',
            'direccion' => 'Inglesito No. 7 e/ Cisneros y Carlos Manuel de Céspedes',
            'telefonos' => '32696418',
        ],
        [
            'banco' => 'BANDEC',
            'municipio' => 'jimaguayu',
            'nombre' => 'Sucursal BANDEC Jimaguayú',
            'codigo' => '6101',
            'direccion' => 'Edificio No. 9',
            'telefonos' => '32399214',
        ],
        [
            'banco' => 'BANDEC',
            'municipio' => 'najasa',
            'nombre' => 'Sucursal BANDEC Najasa',
            'codigo' => '6121',
            'direccion' => 'Carretera Cubanacán No. 2 e/ Avenida Principal y Calle A',
            'telefonos' => '32864138',
        ],
        [
            'banco' => 'BPA',
            'municipio' => 'santa-cruz-del-sur',
            'nombre' => 'Sucursal BPA Santa Cruz del Sur',
            'codigo' => '6142',
            'direccion' => 'Central No. 96 e/ B y C',
            'telefonos' => '32853222',
        ],
        [
            'banco' => 'BANDEC',
            'municipio' => 'santa-cruz-del-sur',
            'nombre' => 'Sucursal BANDEC Santa Cruz del Sur',
            'codigo' => '6161',
            'direccion' => 'D No. 95 e/ Carretera y Central',
            'telefonos' => '32852084',
        ],

        /*
        |--------------------------------------------------------------------------
        | LAS TUNAS
        |--------------------------------------------------------------------------
        */

        [
            'banco' => 'BFI',
            'municipio' => 'las-tunas',
            'nombre' => 'Sucursal BFI Las Tunas',
            'codigo' => null,
            'direccion' => 'Vicente García esquina 24 de Febrero',
            'telefonos' => '31346202',
        ],
        [
            'banco' => 'BANDEC',
            'municipio' => 'las-tunas',
            'nombre' => 'Sucursal BANDEC 6461',
            'codigo' => '6461',
            'direccion' => 'Vicente García No. 69 e/ 24 de Febrero y 13 de Octubre',
            'telefonos' => '31348139',
        ],
        [
            'banco' => 'BANDEC',
            'municipio' => 'las-tunas',
            'nombre' => 'Sucursal BANDEC 6441',
            'codigo' => '6441',
            'direccion' => '30 de Noviembre e/ Francisco Varona y 2 de Diciembre',
            'telefonos' => '31349592',
        ],
        [
            'banco' => 'BANDEC',
            'municipio' => 'las-tunas',
            'nombre' => 'Sucursal BANDEC 6631',
            'codigo' => '6631',
            'direccion' => 'Frank País entre Tony Alomá y J. Grimau',
            'telefonos' => '31349179',
        ],
        [
            'banco' => 'BANDEC',
            'municipio' => 'las-tunas',
            'nombre' => 'Sucursal BANDEC 6301',
            'codigo' => '6301',
            'direccion' => '24 de Febrero No. 41 e/ Vicente García y Juan G.',
            'telefonos' => null,
        ],
        [
            'banco' => 'BPA',
            'municipio' => 'las-tunas',
            'nombre' => 'Sucursal BPA Las Tunas - Vicente García 54',
            'codigo' => null,
            'direccion' => 'Vicente García No. 54 esquina Ramón Ortuño',
            'telefonos' => '31347457',
        ],
        [
            'banco' => 'BPA',
            'municipio' => 'las-tunas',
            'nombre' => 'Sucursal BPA Las Tunas - Vicente García 14',
            'codigo' => null,
            'direccion' => 'Vicente García No. 14 esquina Francisco Vega',
            'telefonos' => '31345236',
        ],
        [
            'banco' => 'BPA',
            'municipio' => 'puerto-padre',
            'nombre' => 'Sucursal BPA Puerto Padre',
            'codigo' => null,
            'direccion' => 'Carlos Manuel de Céspedes No. 74',
            'telefonos' => '31515112',
        ],

        /*
        |--------------------------------------------------------------------------
        | GRANMA
        |--------------------------------------------------------------------------
        */

        [
            'banco' => 'BANDEC',
            'municipio' => 'bayamo',
            'nombre' => 'Sucursal BANDEC Bayamo - General García',
            'codigo' => null,
            'direccion' => 'General García No. 101 e/ Saco y Figueredo',
            'telefonos' => '23426340',
        ],
        [
            'banco' => 'BANDEC',
            'municipio' => 'bayamo',
            'nombre' => 'Sucursal BANDEC Bayamo - Amado Estévez',
            'codigo' => null,
            'direccion' => 'Amado Estévez, Plaza de la Patria y Jesús Menéndez',
            'telefonos' => '23426395',
        ],
        [
            'banco' => 'BANDEC',
            'municipio' => 'manzanillo',
            'nombre' => 'Sucursal BANDEC Manzanillo',
            'codigo' => null,
            'direccion' => 'Calle Merchán No. 262 esquina a Saco',
            'telefonos' => '23577523',
        ],
        [
            'banco' => 'BANDEC',
            'municipio' => 'bartolome-maso',
            'nombre' => 'Sucursal BANDEC Bartolomé Masó',
            'codigo' => null,
            'direccion' => 'Avenida Bartolomé Masó No. 14',
            'telefonos' => '23565147',
        ],
        [
            'banco' => 'BANDEC',
            'municipio' => 'niquero',
            'nombre' => 'Sucursal BANDEC Niquero',
            'codigo' => null,
            'direccion' => 'Calle José Martí No. 103',
            'telefonos' => '23592327',
        ],
        [
            'banco' => 'BANDEC',
            'municipio' => 'pilon',
            'nombre' => 'Sucursal BANDEC Pilón',
            'codigo' => null,
            'direccion' => 'Calle Antonio Guiteras',
            'telefonos' => '23594231',
        ],
        [
            'banco' => 'BPA',
            'municipio' => 'bayamo',
            'nombre' => 'Sucursal BPA Bayamo - General García',
            'codigo' => null,
            'direccion' => 'General García No. 70',
            'telefonos' => '23426613',
        ],
        [
            'banco' => 'BPA',
            'municipio' => 'bayamo',
            'nombre' => 'Sucursal BPA Bayamo - Frank País',
            'codigo' => null,
            'direccion' => 'Avenida Frank País e/ 2da y Jesús Menéndez',
            'telefonos' => '23427371',
        ],
        [
            'banco' => 'BPA',
            'municipio' => 'manzanillo',
            'nombre' => 'Sucursal BPA Manzanillo',
            'codigo' => null,
            'direccion' => 'José Martí No. 121 esquina Batería',
            'telefonos' => '23577115',
        ],

        /*
        |--------------------------------------------------------------------------
        | HOLGUÍN
        |--------------------------------------------------------------------------
        */

        [
            'banco' => 'BFI',
            'municipio' => 'holguin',
            'nombre' => 'Sucursal BFI Holguín',
            'codigo' => null,
            'direccion' => 'Libertad No. 165 / Frexes y Aguilera',
            'telefonos' => '24468034',
        ],
        [
            'banco' => 'BFI',
            'municipio' => 'banes',
            'nombre' => 'Sucursal BFI Guardalavaca',
            'codigo' => null,
            'direccion' => 'Los Framboyanes, Guardalavaca',
            'telefonos' => '24430217',
        ],
        [
            'banco' => 'BFI',
            'municipio' => 'moa',
            'nombre' => 'Sucursal BFI Moa',
            'codigo' => null,
            'direccion' => 'Complejo Comercial / M. Grajales, Las Coloradas',
            'telefonos' => '24604048',
        ],

        /*
        |--------------------------------------------------------------------------
        | SANTIAGO DE CUBA
        |--------------------------------------------------------------------------
        */

        [
            'banco' => 'BANDEC',
            'municipio' => 'santiago-de-cuba',
            'nombre' => 'Sucursal BANDEC 8391',
            'codigo' => '8391',
            'direccion' => 'Félix Pena No. 614',
            'telefonos' => '22623316',
        ],
        [
            'banco' => 'BANDEC',
            'municipio' => 'santiago-de-cuba',
            'nombre' => 'Sucursal BANDEC 8321',
            'codigo' => '8321',
            'direccion' => 'General Lacret y Aguilera',
            'telefonos' => '22627581',
        ],
        [
            'banco' => 'BFI',
            'municipio' => 'santiago-de-cuba',
            'nombre' => 'Sucursal BFI Santiago de Cuba',
            'codigo' => null,
            'direccion' => 'Avenida Las Américas esquina Calle I',
            'telefonos' => '22646873',
        ],
        [
            'banco' => 'BFI',
            'municipio' => 'contramaestre',
            'nombre' => 'Sucursal BFI Contramaestre',
            'codigo' => null,
            'direccion' => 'Carretera Central No. 125-A entre 19 y 21',
            'telefonos' => '22587139',
        ],

        /*
        |--------------------------------------------------------------------------
        | GUANTÁNAMO
        |--------------------------------------------------------------------------
        */

        [
            'banco' => 'BPA',
            'municipio' => 'guantanamo',
            'nombre' => 'Sucursal BPA Guantánamo - Los Maceo',
            'codigo' => null,
            'direccion' => 'Los Maceo e/ Emilio Giró y Carretera',
            'telefonos' => '21327340',
        ],
        [
            'banco' => 'BPA',
            'municipio' => 'guantanamo',
            'nombre' => 'Sucursal BPA Guantánamo - Hospital',
            'codigo' => null,
            'direccion' => '5 Oeste y 10 Norte, Hospital General Docente',
            'telefonos' => null,
        ],
        [
            'banco' => 'BPA',
            'municipio' => 'baracoa',
            'nombre' => 'Sucursal BPA Baracoa',
            'codigo' => null,
            'direccion' => 'Calle Martí No. 166 e/ Ciro Frías y Céspedes',
            'telefonos' => '21642286, 21643843',
        ],
        [
            'banco' => 'BANDEC',
            'municipio' => 'guantanamo',
            'nombre' => 'Sucursal BANDEC Guantánamo - Reparto Rubén López',
            'codigo' => null,
            'direccion' => 'Calle 4 e/ 9 y 11, Reparto Rubén López Sabariego',
            'telefonos' => '21382211',
        ],
        [
            'banco' => 'BANDEC',
            'municipio' => 'guantanamo',
            'nombre' => 'Sucursal BANDEC Guantánamo - Reparto Caribe',
            'codigo' => null,
            'direccion' => 'Calle 11 Norte esquina Avenida 13 de Junio, Reparto Caribe',
            'telefonos' => '21381530',
        ],
        [
            'banco' => 'BANDEC',
            'municipio' => 'guantanamo',
            'nombre' => 'Sucursal BANDEC Guantánamo - Calixto García',
            'codigo' => null,
            'direccion' => 'Calle Calixto García No. 952 esquina Emilio Giró',
            'telefonos' => '21327321',
        ],
        [
            'banco' => 'BANDEC',
            'municipio' => 'baracoa',
            'nombre' => 'Sucursal BANDEC Baracoa',
            'codigo' => null,
            'direccion' => 'Calle Maceo No. 99 e/ 10 de Octubre y 24 de Febrero',
            'telefonos' => '21642003, 21642218, 21642771',
        ],
    ];

    public function run(): void
    {
        DB::transaction(function (): void {
            foreach (self::SUCURSALES as $datos) {
                $banco = Banco::where('alias', $datos['banco'])
                    ->first();

                if (!$banco) {
                    throw new RuntimeException(
                        "No existe el banco con alias: {$datos['banco']}. Ejecuta BancoSeeder primero."
                    );
                }

                $municipio = Municipio::where('slug', $datos['municipio'])
                    ->first();

                if (!$municipio) {
                    throw new RuntimeException(
                        "No existe el municipio con slug: {$datos['municipio']}."
                    );
                }

                $codigo = $datos['codigo'];

                // Si hay código oficial, es la identidad de la sucursal.
                // Si no lo hay, usamos municipio, nombre y dirección.
                $identificador = $codigo !== null
                    ? [
                        'banco_id' => $banco->id,
                        'codigo' => $codigo,
                    ]
                    : [
                        'banco_id' => $banco->id,
                        'municipio_id' => $municipio->id,
                        'nombre' => $datos['nombre'],
                        // 'direccion' => $datos['direccion'],
                    ];

                BancoSucursal::updateOrCreate(
                    $identificador,
                    [
                        'banco_id' => $banco->id,
                        'municipio_id' => $municipio->id,
                        'nombre' => $datos['nombre'],
                        'codigo' => $codigo,
                        'direccion' => $datos['direccion'],
                        'telefonos' => $datos['telefonos'],
                        'activa' => true,
                    ]
                );
            }
        });
    }
}
