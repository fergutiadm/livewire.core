<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /*
        |--------------------------------------------------------------------------
        | tipos_gastos
        |--------------------------------------------------------------------------
        |
        | Necesitamos una clave única compuesta (id, local_id)
        | para poder referenciar ambas columnas desde gastos.
        |
        */
        Schema::table('tipos_gastos', function (Blueprint $table) {
            $table->unique(
                ['id', 'local_id'],
                'tipos_gastos_id_local_unique'
            );
        });

        /*
        |--------------------------------------------------------------------------
        | gastos
        |--------------------------------------------------------------------------
        |
        | Eliminamos la FK anterior:
        |
        | tipo_gasto_id -> tipos_gastos.id
        |
        | y la sustituimos por:
        |
        | (tipo_gasto_id, local_id)
        |          ↓
        | (id, local_id)
        |
        | Esto garantiza que el TipoGasto pertenece al mismo Local
        | que el Gasto.
        |
        */
        Schema::table('gastos', function (Blueprint $table) {
            $table->dropForeign([
                'tipo_gasto_id',
            ]);

            $table->foreign(
                ['tipo_gasto_id', 'local_id'],
                'gastos_tipo_local_foreign'
            )->references(
                ['id', 'local_id']
            )->on('tipos_gastos')
             ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Restaurar FK original de gastos
        |--------------------------------------------------------------------------
        */
        Schema::table('gastos', function (Blueprint $table) {
            $table->dropForeign(
                'gastos_tipo_local_foreign'
            );

            $table->foreign(
                'tipo_gasto_id'
            )->references(
                'id'
            )->on('tipos_gastos')
             ->restrictOnDelete();
        });

        /*
        |--------------------------------------------------------------------------
        | Eliminar clave única auxiliar
        |--------------------------------------------------------------------------
        */
        Schema::table('tipos_gastos', function (Blueprint $table) {
            $table->dropUnique(
                'tipos_gastos_id_local_unique'
            );
        });
    }
};