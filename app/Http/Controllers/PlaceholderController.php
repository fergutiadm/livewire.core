<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use App\Models\Categoria;
use App\Models\Producto;

class PlaceholderController extends Controller
{
    private function generarPlaceholder($id, $nombre)
    {
        $icono = $this->detectarIcono($nombre);

        $color = substr(md5($id), 0, 6);

        return <<<SVG
    <svg xmlns="http://www.w3.org/2000/svg" width="400" height="400">
        <rect width="100%" height="100%" rx="24" fill="#{$color}" />
        <text x="50%" y="52%" dominant-baseline="middle" text-anchor="middle"
            font-size="140"
            font-family="Segoe UI Emoji, Apple Color Emoji, sans-serif">
            {$icono}
        </text>
    </svg>
    SVG;
    }

    public function categoria($id)
    {
        $categoria = Categoria::find($id);

        $nombre = $categoria?->nombre ?? 'NA';

        $svg = $this->generarPlaceholder($id, $nombre);

        return response($svg)->header('Content-Type', 'image/svg+xml');
    }

    public function producto($id)
    {
        $producto = Producto::find($id);

        $nombre = $producto?->nombre ?? 'NA';

        $svg = $this->generarPlaceholder($id, $nombre);

        return response($svg)->header('Content-Type', 'image/svg+xml');
    }


    private function detectarIcono($nombre)
    {
        $original = $nombre;

        $nombre = strtolower($nombre);
        $nombre = iconv('UTF-8', 'ASCII//TRANSLIT', $nombre);

        file_put_contents(
            storage_path('logs/categorias_debug.log'),
            "ORIGINAL: {$original} | NORMALIZADO: {$nombre}\n",
            FILE_APPEND
        );

        return match (true) {
            str_contains($nombre, 'telefono') => '📱',
            str_contains($nombre, 'pantalla') => '🖥',
            str_contains($nombre, 'memoria') => '💾',
            str_contains($nombre, 'usb') => '🔌',
            str_contains($nombre, 'tenis') => '👟',
            str_contains($nombre, 'ropa') => '👕',
            str_contains($nombre, 'cuidado') => '🧴',
            str_contains($nombre, 'hogar') => '🏠',
            str_contains($nombre, 'cocina') => '🍳',
            str_contains($nombre, 'videojuego') => '🎮',
            str_contains($nombre, 'juguete') => '🧸',
            str_contains($nombre, 'mueble') => '🪑',
            str_contains($nombre, 'herramienta') => '🔧',
            str_contains($nombre, 'audio') => '🎧',
            str_contains($nombre, 'reloj') => '⌚',
            str_contains($nombre, 'decoracion') => '🖼',
            str_contains($nombre, 'electrodomestico') => '⚡',
            default => strtoupper(substr($nombre, 0, 2))
        };
    }

}
