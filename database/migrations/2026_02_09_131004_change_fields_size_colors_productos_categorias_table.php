<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('categorias', function (Blueprint $table) {
            $table->string('color_bg', 20)->nullable()->change();
            $table->string('color_text', 20)->nullable()->change();
        });
        Schema::table('productos', function (Blueprint $table) {
            $table->string('color_bg', 20)->nullable()->change();
            $table->string('color_text', 20)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categorias', function (Blueprint $table) {
            $table->string('color_bg', 7)->nullable()->change();
            $table->string('color_text', 7)->nullable()->change();
        });
        Schema::table('productos', function (Blueprint $table) {
            $table->string('color_bg', 7)->nullable()->change();
            $table->string('color_text', 7)->nullable()->change();
        });
    }
};
