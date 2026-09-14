<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class SyncModuleChecksums extends Command
{
    protected $signature = 'module:sync-checksums';

    protected $description =
        'Generate baseline checksums for existing module files';

    public function handle(): void
    {
        $checksumFile = storage_path(
            'app/module-generator/checksums.json'
        );

        File::ensureDirectoryExists(
            dirname($checksumFile)
        );

        $checksums = [];

        /*
        |--------------------------------------------------------------------------
        | SCAN MODULES
        |--------------------------------------------------------------------------
        */

        $paths = [
            app_path('Modules'),
            resource_path('views/modules'),
        ];

        foreach ($paths as $basePath) {

            if (!File::exists($basePath)) {
                continue;
            }

            $files = File::allFiles($basePath);

            foreach ($files as $file) {

                $realPath = $file->getRealPath();

                $relativePath = str_replace(
                    base_path() . DIRECTORY_SEPARATOR,
                    '',
                    $realPath
                );

                $checksums[$relativePath] = md5_file($realPath);

                $this->components->info(
                    "SYNCED  {$relativePath}"
                );
            }
        }

        File::put(
            $checksumFile,
            json_encode($checksums, JSON_PRETTY_PRINT)
        );

        $this->newLine();

        $this->components->info(
            'Checksums synchronized successfully.'
        );
    }
}