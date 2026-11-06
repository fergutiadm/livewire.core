<?php

namespace App\Modules\Ayuda\Services;

use HTMLPurifier;
use HTMLPurifier_Config;

class AyudaHtmlSanitizer
{
    public function sanitize(?string $html): string
    {
        if (blank($html)) {
            return '';
        }

        $config = HTMLPurifier_Config::createDefault();

        $config->set('Core.Encoding', 'UTF-8');

        $config->set(
            'HTML.Allowed',
            'p,br,hr,h1,h2,h3,h4,h5,h6,' .
            'strong,b,em,i,s,' .
            'ul,ol,li,blockquote,' .
            'code,pre,' .
            'a[href|title]'
        );

        $config->set('URI.AllowedSchemes', [
            'http' => true,
            'https' => true,
            'mailto' => true,
        ]);

        $config->set('Attr.EnableID', false);

        $cachePath = storage_path('framework/cache/htmlpurifier');

        if (! is_dir($cachePath)) {
            if (! mkdir($cachePath, 0755, true) && ! is_dir($cachePath)) {
                throw new \RuntimeException(
                    "No se pudo crear el directorio de caché HTMLPurifier: {$cachePath}"
                );
            }
        }

        $config->set('Cache.SerializerPath', $cachePath);

        return (new HTMLPurifier($config))->purify($html);
    }
}