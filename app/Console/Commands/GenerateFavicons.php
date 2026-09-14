<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GenerateFavicons extends Command
{
    protected $signature = 'favicons:generate';
    protected $description = 'Genera favicons desde ferguti-logo-1.png';

    public function handle()
    {
        $source = public_path('img/ferguti-logo-1.png');
        $dest = public_path('img/');

        if (!file_exists($source)) {
            $this->error("Archivo $source no encontrado!");
            return 1;
        }

        $sizes = [
            'favicon-512.png' => 512,
            'favicon-192.png' => 192,
            'apple-touch-icon.png' => 180,
        ];

        foreach ($sizes as $file => $size) {
            $this->resizePng($source, $dest . $file, $size, $size);
            $this->info("Generado $file ($size x $size)");
        }

        // Generar favicon.ico (16x16 y 32x32 combinados)
        $ico16 = $dest . 'favicon-16.png';
        $ico32 = $dest . 'favicon-32.png';
        $this->resizePng($source, $ico16, 16, 16);
        $this->resizePng($source, $ico32, 32, 32);

        // Crear .ico
        $this->createIco([$ico16, $ico32], $dest . 'favicon.ico');
        unlink($ico16);
        unlink($ico32);
        $this->info("Generado favicon.ico");

        $this->info("Todos los favicons generados correctamente!");
        return 0;
    }

    private function resizePng($src, $dest, $width, $height)
    {
        $srcImg = imagecreatefrompng($src);
        $dstImg = imagecreatetruecolor($width, $height);
        imagesavealpha($dstImg, true);
        $trans_color = imagecolorallocatealpha($dstImg, 0, 0, 0, 127);
        imagefill($dstImg, 0, 0, $trans_color);
        imagecopyresampled($dstImg, $srcImg, 0, 0, 0, 0, $width, $height, imagesx($srcImg), imagesy($srcImg));
        imagepng($dstImg, $dest);
        imagedestroy($srcImg);
        imagedestroy($dstImg);
    }

    private function createIco(array $pngFiles, $destIco)
    {
        // Requiere la librería PHP `icoutils` o una librería externa para .ico
        // Para simplificar, podemos usar la función "exec" si tienes ImageMagick:
        $files = implode(' ', array_map('escapeshellarg', $pngFiles));
        exec("convert $files -colors 256 $destIco");
    }
}
