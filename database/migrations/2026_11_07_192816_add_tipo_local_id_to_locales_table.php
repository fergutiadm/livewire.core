<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tipoOperativoId = DB::table('tipos_locales')
            ->where('codigo', 'operativo')
            ->value('id');

        if (!$tipoOperativoId) {
            throw new RuntimeException(
                'No existe el tipo de local operativo.'
            );
        }

        Schema::table('locales', function (Blueprint $table) {
            $table->foreignId('tipo_local_id')
                ->nullable()
                ->after('id')
                ->constrained('tipos_locales')
                ->restrictOnDelete();
        });

        // Los locales existentes se consideran operativos inicialmente.
        DB::table('locales')->update([
            'tipo_local_id' => $tipoOperativoId,
        ]);
    }

    public function down(): void
    {
        Schema::table('locales', function (Blueprint $table) {
            $table->dropConstrainedForeignId('tipo_local_id');
        });
    }
};