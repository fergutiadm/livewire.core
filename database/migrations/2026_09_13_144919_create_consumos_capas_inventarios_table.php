<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consumos_capas_inventario', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('movimiento_inventario_detalle_id');

            $table->unsignedBigInteger('capa_inventario_id');

            $table->decimal('cantidad_base', 20, 10);

            $table->decimal('costo_unitario', 20, 10);

            $table->decimal('costo_total', 20, 10);

            $table->timestamps();

            $table->foreign(
                'movimiento_inventario_detalle_id',
                'consumos_capas_mov_detalle_fk'
            )
                ->references('id')
                ->on('movimientos_inventarios_detalles')
                ->restrictOnDelete();

            $table->foreign(
                'capa_inventario_id',
                'consumos_capas_capa_fk'
            )
                ->references('id')
                ->on('capas_inventario')
                ->restrictOnDelete();

            $table->unique([
                'movimiento_inventario_detalle_id',
                'capa_inventario_id',
            ], 'consumos_capas_detalle_capa_unique');

            $table->index(
                'capa_inventario_id',
                'consumos_capas_capa_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consumos_capas_inventario');
    }
};