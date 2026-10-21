<?php

declare(strict_types=1);

/**
 * Lista los módulos existentes en app/Modules.
 *
 * Uso:
 * php list_modules.php
 */

$project = dirname(__DIR__);
$modulesPath = $project . DIRECTORY_SEPARATOR . 'app'
    . DIRECTORY_SEPARATOR . 'Modules';

if (!is_dir($modulesPath)) {
    fwrite(STDERR, "Error: no existe el directorio {$modulesPath}" . PHP_EOL);
    exit(1);
}

$directories = [];

$iterator = new DirectoryIterator($modulesPath);

foreach ($iterator as $item) {
    if ($item->isDot() || !$item->isDir()) {
        continue;
    }

    $directories[] = $item->getFilename();
}

sort($directories, SORT_NATURAL | SORT_FLAG_CASE);

echo "Módulos encontrados en app/Modules:" . PHP_EOL;
echo str_repeat('-', 45) . PHP_EOL;

if ($directories === []) {
    echo "No se encontraron módulos." . PHP_EOL;
    exit(0);
}

foreach ($directories as $directory) {
    echo "- {$directory}" . PHP_EOL;
}

echo PHP_EOL . "Total: " . count($directories) . " módulos." . PHP_EOL;