<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categorias', function (Blueprint $table) {

            $table->string('icono', 30)
                ->nullable()
                ->after('imagen_minimalista');

            $table->string('color_bg', 7)
                ->nullable()
                ->after('icono');

            $table->string('color_text', 7)
                ->nullable()
                ->after('color_bg');

        });
    }

    public function down(): void
    {
        Schema::table('categorias', function (Blueprint $table) {

            $table->dropColumn([
                'icono',
                'color_bg',
                'color_text'
            ]);

        });
    }
};

