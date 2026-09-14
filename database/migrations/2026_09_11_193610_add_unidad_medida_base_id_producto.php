<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->foreignId('unidad_medida_base_id')
                ->nullable()
                ->after('id')
                ->constrained('unidades_medida')
                ->restrictOnDelete();

            $table->index('unidad_medida_base_id');
        });
    }

    public function down(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->dropForeign(['unidad_medida_base_id']);
            $table->dropIndex(['unidad_medida_base_id']);
            $table->dropColumn('unidad_medida_base_id');
        });
    }
};