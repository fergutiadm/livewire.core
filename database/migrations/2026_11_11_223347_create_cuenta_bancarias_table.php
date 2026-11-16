
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cuentas_bancarias', function (Blueprint $table) {
            $table->id();

            $table->foreignId('sucursal_id')
                ->constrained('banco_sucursales')
                ->restrictOnDelete();

            $table->foreignId('moneda_id')
                ->constrained('monedas')
                ->restrictOnDelete();

            $table->string('numero_cuenta', 150);

            $table->string('titular', 150);

            $table->string('descripcion', 255)->nullable();

            $table->boolean('activa')->default(true)->index();

            $table->unique(
                ['sucursal_id', 'numero_cuenta'],
                'cuenta_sucursal_numero_unique'
            );

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cuentas_bancarias');
    }
};
