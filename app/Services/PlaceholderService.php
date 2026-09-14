<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class PlaceholderService
{
    public static function generarCategoria(string $nombre, ?int $categoriaId = null): string
    {
        // Manager nuevo en v3
        $manager = new ImageManager(new Driver());

        // Color basado en ID o nombre
        $hash = dechex(crc32($categoriaId ?? $nombre));
        $color = '#' . substr($hash, 0, 6);

        $size = 400;

        // Crear imagen
        $img = $manager->create($size, $size)->fill($color);

        // Inicial
        $inicial = strtoupper(substr($nombre, 0, 1));

        // Texto SIN fuente externa (usa GD builtin)
        $img->text($inicial, $size / 2, $size / 2, function ($font) use ($size) {
            $font->size($size / 2);
            $font->color('#ffffff');
            $font->align('center');
            $font->valign('middle');
        });

        Storage::disk('public')->makeDirectory('categorias');

        $path = 'categorias/placeholder_' . ($categoriaId ?? uniqid()) . '.png';

        Storage::disk('public')->put(
            $path,
            $img->toPng()->toString()
        );

        return $path;
    }
}
