<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('atributos', function (Blueprint $table) {
            $table->string('codigo', 100)
                ->after('id');

            $table->boolean('activo')
                ->default(true)
                ->after('orden_visual');

            $table->unique(
                'codigo',
                'atributos_codigo_unique'
            );

            $table->index(
                'activo',
                'atributos_activo_index'
            );
        });
    }

    public function down(): void
    {
        Schema::table('atributos', function (Blueprint $table) {
            $table->dropUnique('atributos_codigo_unique');
            $table->dropIndex('atributos_activo_index');

            $table->dropColumn([
                'codigo',
                'activo',
            ]);
        });
    }
};