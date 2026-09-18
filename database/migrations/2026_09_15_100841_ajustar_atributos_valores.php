<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /*
         * Esta migración tuvo una ejecución parcial anteriormente.
         * Por eso comprobamos la existencia de cada elemento antes
         * de modificar la tabla.
         */

        $hasValor = Schema::hasColumn(
            'atributos_valores',
            'valor'
        );

        $hasNombre = Schema::hasColumn(
            'atributos_valores',
            'nombre'
        );

        /*
         * MySQL de esta instalación no soporta la sintaxis
         * RENAME COLUMN utilizada por renameColumn().
         */
        if ($hasValor && !$hasNombre) {
            DB::statement(
                'ALTER TABLE `atributos_valores`
                 CHANGE COLUMN `valor` `nombre` VARCHAR(255) NOT NULL'
            );
        }

        /*
         * Agregar codigo si todavía no existe.
         */
        if (!Schema::hasColumn(
            'atributos_valores',
            'codigo'
        )) {
            Schema::table(
                'atributos_valores',
                function (Blueprint $table) {
                    $table->string('codigo', 100)
                        ->after('atributo_id');
                }
            );
        }

        /*
         * Agregar activo si todavía no existe.
         */
        if (!Schema::hasColumn(
            'atributos_valores',
            'activo'
        )) {
            Schema::table(
                'atributos_valores',
                function (Blueprint $table) {
                    $table->boolean('activo')
                        ->default(true)
                        ->after('orden_visual');
                }
            );
        }

        /*
         * descripcion deja de formar parte del modelo.
         */
        if (Schema::hasColumn(
            'atributos_valores',
            'descripcion'
        )) {
            Schema::table(
                'atributos_valores',
                function (Blueprint $table) {
                    $table->dropColumn('descripcion');
                }
            );
        }

        /*
         * Unique atributo_id + codigo.
         */
        if (!$this->indexExists(
            'atributos_valores',
            'atributos_valores_atributo_codigo_unique'
        )) {
            Schema::table(
                'atributos_valores',
                function (Blueprint $table) {
                    $table->unique(
                        ['atributo_id', 'codigo'],
                        'atributos_valores_atributo_codigo_unique'
                    );
                }
            );
        }

        /*
         * Índice atributo_id + activo.
         */
        if (!$this->indexExists(
            'atributos_valores',
            'atributos_valores_atributo_activo_index'
        )) {
            Schema::table(
                'atributos_valores',
                function (Blueprint $table) {
                    $table->index(
                        ['atributo_id', 'activo'],
                        'atributos_valores_atributo_activo_index'
                    );
                }
            );
        }
    }

    public function down(): void
    {
        /*
         * Eliminar índice unique si existe.
         */
        if ($this->indexExists(
            'atributos_valores',
            'atributos_valores_atributo_codigo_unique'
        )) {
            Schema::table(
                'atributos_valores',
                function (Blueprint $table) {
                    $table->dropUnique(
                        'atributos_valores_atributo_codigo_unique'
                    );
                }
            );
        }

        /*
         * Eliminar índice activo si existe.
         */
        if ($this->indexExists(
            'atributos_valores',
            'atributos_valores_atributo_activo_index'
        )) {
            Schema::table(
                'atributos_valores',
                function (Blueprint $table) {
                    $table->dropIndex(
                        'atributos_valores_atributo_activo_index'
                    );
                }
            );
        }

        /*
         * Eliminar columnas nuevas.
         */
        $columnsToDrop = [];

        if (Schema::hasColumn(
            'atributos_valores',
            'codigo'
        )) {
            $columnsToDrop[] = 'codigo';
        }

        if (Schema::hasColumn(
            'atributos_valores',
            'activo'
        )) {
            $columnsToDrop[] = 'activo';
        }

        if ($columnsToDrop !== []) {
            Schema::table(
                'atributos_valores',
                function (Blueprint $table) use ($columnsToDrop) {
                    $table->dropColumn($columnsToDrop);
                }
            );
        }

        /*
         * Restaurar valor.
         */
        $hasNombre = Schema::hasColumn(
            'atributos_valores',
            'nombre'
        );

        $hasValor = Schema::hasColumn(
            'atributos_valores',
            'valor'
        );

        if ($hasNombre && !$hasValor) {
            DB::statement(
                'ALTER TABLE `atributos_valores`
                 CHANGE COLUMN `nombre` `valor` VARCHAR(255) NOT NULL'
            );
        }

        /*
         * Restaurar descripcion.
         */
        if (!Schema::hasColumn(
            'atributos_valores',
            'descripcion'
        )) {
            Schema::table(
                'atributos_valores',
                function (Blueprint $table) {
                    $table->text('descripcion')
                        ->nullable()
                        ->after('valor');
                }
            );
        }
    }

    private function indexExists(
        string $table,
        string $index
    ): bool {
        $result = DB::selectOne(
            '
            SELECT COUNT(*) AS total
            FROM information_schema.statistics
            WHERE table_schema = DATABASE()
              AND table_name = ?
              AND index_name = ?
            ',
            [
                $table,
                $index,
            ]
        );

        return (int) $result->total > 0;
    }
};