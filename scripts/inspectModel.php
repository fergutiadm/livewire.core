#!/usr/bin/env php
<?php

/**
 * InspectModel
 *
 * Uso:
 *
 *   ./scripts/inspectModel Producto
 *
 *   ./scripts/inspectModel Producto --fillable
 *   ./scripts/inspectModel Producto --methods
 *   ./scripts/inspectModel Producto --relations
 *
 * También permite combinar:
 *
 *   ./scripts/inspectModel Producto --fillable --methods --relations
 *
 * Si no se especifica ninguna opción, muestra las tres.
 */

declare(strict_types=1);

$project = dirname(__DIR__);

if ($argc < 2) {
    echo "Uso:\n";
    echo "  ./scripts/inspectModel <Entidad> [opciones]\n\n";

    echo "Opciones:\n";
    echo "  --fillable     Mostrar $fillable\n";
    echo "  --methods      Mostrar métodos\n";
    echo "  --relations    Mostrar relaciones\n";
    echo "  --all          Mostrar todo\n\n";

    echo "Ejemplos:\n";
    echo "  ./scripts/inspectModel Producto\n";
    echo "  ./scripts/inspectModel Producto --fillable\n";
    echo "  ./scripts/inspectModel Producto --methods\n";
    echo "  ./scripts/inspectModel Producto --relations\n";
    echo "  ./scripts/inspectModel Producto --fillable --relations\n";

    exit(1);
}

$entity = $argv[1];

$options = array_slice($argv, 2);

$showFillable = in_array('--fillable', $options, true);
$showMethods = in_array('--methods', $options, true);
$showRelations = in_array('--relations', $options, true);
$showAll = in_array('--all', $options, true);

if (!$showFillable && !$showMethods && !$showRelations) {
    $showFillable = true;
    $showMethods = true;
    $showRelations = true;
}

if ($showAll) {
    $showFillable = true;
    $showMethods = true;
    $showRelations = true;
}

/*
|--------------------------------------------------------------------------
| Buscar archivo del modelo
|--------------------------------------------------------------------------
*/

$modelFiles = [];

$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator(
        $project . DIRECTORY_SEPARATOR . 'app',
        FilesystemIterator::SKIP_DOTS
    )
);

foreach ($iterator as $file) {
    if (!$file->isFile()) {
        continue;
    }

    if ($file->getExtension() !== 'php') {
        continue;
    }

    $path = $file->getPathname();
    $contents = file_get_contents($path);

    if ($contents === false) {
        continue;
    }

    /*
     * Buscamos:
     *
     * class Producto extends Model
     * class Producto extends BaseModel
     *
     * Pero exigimos que exista "extends ...Model" o que use
     * Illuminate\Database\Eloquent\Model.
     */

    if (
        preg_match(
            '/\bclass\s+' . preg_quote($entity, '/') . '\s+extends\s+[A-Za-z_][A-Za-z0-9_]*\s*(?:implements[^{]+)?\{/s',
            $contents
        )
    ) {
        if (
            str_contains($contents, 'Illuminate\Database\Eloquent\Model')
            || preg_match('/\bextends\s+[A-Za-z_][A-Za-z0-9_]*Model\b/', $contents)
            || preg_match('/\bextends\s+Model\b/', $contents)
        ) {
            $modelFiles[] = $path;
        }
    }
}

if (count($modelFiles) === 0) {
    echo "ERROR: No se encontró el modelo '{$entity}'.\n";
    exit(1);
}

if (count($modelFiles) > 1) {
    echo "ERROR: Se encontraron varios modelos con el nombre '{$entity}':\n\n";

    foreach ($modelFiles as $path) {
        echo '  ' . str_replace($project . DIRECTORY_SEPARATOR, '', $path) . PHP_EOL;
    }

    echo "\nEspecifica una entidad cuyo nombre sea único.\n";
    exit(1);
}

$file = $modelFiles[0];
$source = file_get_contents($file);

if ($source === false) {
    echo "ERROR: No se pudo leer el archivo.\n";
    exit(1);
}

$relativePath = str_replace(
    $project . DIRECTORY_SEPARATOR,
    '',
    $file
);

echo PHP_EOL;
echo "============================================================\n";
echo "Modelo: {$entity}\n";
echo "Archivo: {$relativePath}\n";
echo "============================================================\n";

/*
|--------------------------------------------------------------------------
| Tokenizar
|--------------------------------------------------------------------------
*/

$tokens = token_get_all($source);

/*
|--------------------------------------------------------------------------
| Namespace
|--------------------------------------------------------------------------
*/

$namespace = '';

for ($i = 0, $count = count($tokens); $i < $count; $i++) {
    if (
        is_array($tokens[$i]) &&
        $tokens[$i][0] === T_NAMESPACE
    ) {
        $namespaceParts = [];

        for ($j = $i + 1; $j < $count; $j++) {
            if (is_array($tokens[$j])) {
                if (
                    in_array(
                        $tokens[$j][0],
                        [T_STRING, T_NAME_QUALIFIED, T_NAME_FULLY_QUALIFIED]
                    )
                ) {
                    $namespaceParts[] = $tokens[$j][1];
                }
            }

            if ($tokens[$j] === ';') {
                break;
            }
        }

        $namespace = implode('', $namespaceParts);
        break;
    }
}

/*
|--------------------------------------------------------------------------
| Fillable
|--------------------------------------------------------------------------
*/

$fillable = [];

if ($showFillable) {
    $fillableMatch = [];

    if (
        preg_match(
            '/protected\s+\$fillable\s*=\s*\[(.*?)\];/s',
            $source,
            $fillableMatch
        )
    ) {
        preg_match_all(
            "/['\"]([^'\"]+)['\"]/",
            $fillableMatch[1],
            $values
        );

        $fillable = $values[1] ?? [];
    }

    echo PHP_EOL;
    echo "FILLABLE\n";
    echo "--------\n";

    if (empty($fillable)) {
        echo "(No encontrado o vacío)\n";
    } else {
        foreach ($fillable as $field) {
            echo "  - {$field}\n";
        }
    }
}

/*
|--------------------------------------------------------------------------
| Métodos y relaciones
|--------------------------------------------------------------------------
*/

$methods = [];
$relations = [];

$relationTypes = [
    'belongsTo',
    'hasMany',
    'hasOne',
    'hasManyThrough',
    'hasOneThrough',
    'belongsToMany',
    'morphOne',
    'morphMany',
    'morphTo',
    'morphToMany',
    'morphedByMany',
    'hasManyThrough',
    'hasOneThrough',
];

$tokenCount = count($tokens);

for ($i = 0; $i < $tokenCount; $i++) {
    if (!is_array($tokens[$i])) {
        continue;
    }

    if ($tokens[$i][0] !== T_FUNCTION) {
        continue;
    }

    /*
     * Buscar el nombre del método después de "function".
     */

    $methodName = null;
    $methodStart = $i;

    for ($j = $i + 1; $j < $tokenCount; $j++) {
        if (
            is_array($tokens[$j]) &&
            $tokens[$j][0] === T_STRING
        ) {
            $methodName = $tokens[$j][1];
            break;
        }

        if ($tokens[$j] === '(') {
            break;
        }
    }

    if ($methodName === null) {
        continue;
    }

    /*
     * Encontrar el inicio del cuerpo "{"
     */

    $bodyStart = null;

    for ($j = $i; $j < $tokenCount; $j++) {
        if ($tokens[$j] === '{') {
            $bodyStart = $j;
            break;
        }

        if ($tokens[$j] === ';') {
            break;
        }
    }

    if ($bodyStart === null) {
        continue;
    }

    /*
     * Extraer cuerpo respetando llaves anidadas.
     */

    $depth = 0;
    $body = '';

    for ($j = $bodyStart; $j < $tokenCount; $j++) {
        $token = $tokens[$j];

        if ($token === '{') {
            $depth++;
        }

        if ($token === '}') {
            $depth--;

            if ($depth === 0) {
                break;
            }
        }

        $body .= is_array($token)
            ? $token[1]
            : $token;
    }

    $methods[$methodName] = true;

    /*
     * Detectar relación.
     *
     * Ejemplo:
     *
     * return $this->belongsTo(Categoria::class);
     *
     * También:
     *
     * return $this->hasMany(
     *     Producto::class
     * );
     */

    foreach ($relationTypes as $relationType) {
        if (
            preg_match(
                '/\$\s*this\s*->\s*' . preg_quote($relationType, '/') . '\s*\(/',
                $body
            )
        ) {
            $relations[$methodName] = $relationType;
            break;
        }
    }
}

/*
|--------------------------------------------------------------------------
| Mostrar métodos
|--------------------------------------------------------------------------
*/

if ($showMethods) {
    echo PHP_EOL;
    echo "MÉTODOS\n";
    echo "-------\n";

    if (empty($methods)) {
        echo "(No encontrados)\n";
    } else {
        foreach (array_keys($methods) as $method) {
            echo "  - {$method}()\n";
        }
    }
}

/*
|--------------------------------------------------------------------------
| Mostrar relaciones
|--------------------------------------------------------------------------
*/

if ($showRelations) {
    echo PHP_EOL;
    echo "RELACIONES\n";
    echo "----------\n";

    if (empty($relations)) {
        echo "(No encontradas)\n";
    } else {
        foreach ($relations as $method => $type) {
            echo "  - {$method}() → {$type}\n";
        }
    }
}

echo PHP_EOL;