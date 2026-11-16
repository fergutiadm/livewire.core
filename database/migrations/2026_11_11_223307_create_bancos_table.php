
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bancos', function (Blueprint $table) {
            $table->id();

            $table->string('nombre', 150);

            $table->string('alias', 50)->nullable();

            $table->string('codigo_swift', 20);

            $table->string('telefonos', 255)->nullable();

            $table->boolean('activo')->default(true);

            $table->timestamps();

            $table->index(
                'codigo_swift',
                'banco_codigo_swift_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bancos');
    }
};
