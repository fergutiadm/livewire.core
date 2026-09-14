<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('categorias', function (Blueprint $table) {
            $table->string('icono_secundario', 50)->nullable()->after('icono');
            $table->string('imagen_representativa')->nullable()->after('imagen_minimalista');
            $table->string('color_badge', 20)->nullable()->after('color_text');
        });
    }

    public function down()
    {
        Schema::table('categorias', function (Blueprint $table) {
            $table->dropColumn(['icono_secundario', 'imagen_representativa', 'color_badge']);
        });
    }
};
