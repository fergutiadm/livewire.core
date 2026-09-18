<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('atributables_valores', function (Blueprint $table) {
            $table->boolean('activo')
                ->default(true)
                ->after('orden_visual');

            $table->index(
                [
                    'atributable_type',
                    'atributable_id',
                    'activo',
                ],
                'atributables_valores_activo_index'
            );
        });
    }

    public function down(): void
    {
        Schema::table('atributables_valores', function (Blueprint $table) {
            $table->dropIndex('atributables_valores_activo_index');

            $table->dropColumn('activo');
        });
    }
};