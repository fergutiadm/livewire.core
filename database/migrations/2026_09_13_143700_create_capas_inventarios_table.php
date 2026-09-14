<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('capas_inventario', function (Blueprint $table) {
            $table->id();

            $table->foreignId('producto_id')
                ->constrained('productos')
                ->restrictOnDelete();

            $table->foreignId('local_id')
                ->constrained('locales')
                ->restrictOnDelete();

            $table->foreignId('movimiento_inventario_detalle_id')
                ->constrained('movimientos_inventarios_detalles')
                ->restrictOnDelete();

            $table->foreignId('capa_origen_id')
                ->nullable()
                ->constrained('capas_inventario')
                ->restrictOnDelete();

            $table->decimal('cantidad_inicial', 20, 10);
            $table->decimal('cantidad_disponible', 20, 10);

            $table->decimal('costo_unitario', 20, 10);
            $table->decimal('costo_total', 20, 10);

            $table->timestamp('fecha');

            $table->timestamps();

            $table->index([
                'producto_id',
                'local_id',
                'cantidad_disponible',
            ]);

            $table->index([
                'producto_id',
                'local_id',
                'fecha',
                'id',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('capas_inventario');
    }
};