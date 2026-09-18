<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('atributables_atributos', function (Blueprint $table) {
            $table->string('tipo', 20)
                ->default('informativo')
                ->after('atributable_id');

            $table->boolean('obligatorio')
                ->default(false)
                ->after('tipo');

            $table->boolean('activo')
                ->default(true)
                ->after('orden_visual');

            $table->index(
                [
                    'atributable_type',
                    'atributable_id',
                    'tipo',
                    'activo',
                ],
                'atributables_atributos_tipo_activo_index'
            );
        });
    }

    public function down(): void
    {
        Schema::table('atributables_atributos', function (Blueprint $table) {
            $table->dropIndex('atributables_atributos_tipo_activo_index');

            $table->dropColumn([
                'tipo',
                'obligatorio',
                'activo',
            ]);
        });
    }
};