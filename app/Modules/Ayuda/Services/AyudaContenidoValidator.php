<?php

namespace App\Modules\Ayuda\Services;

use Illuminate\Validation\ValidationException;

class AyudaContenidoValidator
{
    private const MAX_CARACTERES = 50000;

    public function validate(
        string $html,
        bool $requerido = true
    ): void {
        $texto = html_entity_decode(
            strip_tags($html),
            ENT_QUOTES | ENT_HTML5,
            'UTF-8'
        );

        $texto = preg_replace(
            '/^[\s\p{Z}\x{FEFF}]+|[\s\p{Z}\x{FEFF}]+$/u',
            '',
            $texto
        );

        if ($texto === null) {
            throw ValidationException::withMessages([
                'contenido' => 'No se pudo procesar el contenido.',
            ]);
        }

        if ($requerido && $texto === '') {
            throw ValidationException::withMessages([
                'contenido' => 'El artículo debe contener texto.',
            ]);
        }

        if (mb_strlen($texto, 'UTF-8') > self::MAX_CARACTERES) {
            throw ValidationException::withMessages([
                'contenido' => 'El contenido no puede superar los 50 000 caracteres.',
            ]);
        }
    }
}
