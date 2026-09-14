<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionClass;

class ListModels extends Command
{
    protected $signature = 'models:list';

    protected $description = 'Lista todos los modelos Eloquent del proyecto';

    public function handle(): int
    {
        $basePath = app_path();

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(
                $basePath,
                RecursiveDirectoryIterator::SKIP_DOTS
            )
        );

        $modelos = [];

        foreach ($iterator as $file) {
            if (!$file->isFile() || $file->getExtension() !== 'php') {
                continue;
            }

            $path = $file->getPathname();

            if (!str_ends_with($path, '.php')) {
                continue;
            }

            $relative = str_replace(
                [$basePath . DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR],
                ['', '\\'],
                $path
            );

            $class = 'App\\' . str_replace(
                '.php',
                '',
                $relative
            );

            if (!class_exists($class)) {
                continue;
            }

            try {
                $reflection = new ReflectionClass($class);

                if (
                    !$reflection->isAbstract()
                    && $reflection->isSubclassOf(Model::class)
                ) {
                    $modelos[] = $class;
                }
            } catch (\Throwable) {
                continue;
            }
        }

        sort($modelos);

        if (empty($modelos)) {
            $this->warn('No se encontraron modelos Eloquent.');
            return self::SUCCESS;
        }

        $this->info('Modelos Eloquent encontrados:');
        $this->newLine();

        foreach ($modelos as $modelo) {
            $this->line("  {$modelo}");
        }

        $this->newLine();
        $this->info('Total: ' . count($modelos));

        return self::SUCCESS;
    }
}