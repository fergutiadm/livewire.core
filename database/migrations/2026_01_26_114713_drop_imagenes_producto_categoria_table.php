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
        Schema::table('productos', function(Blueprint $table){
            if(Schema::hasColumn('productos', 'imagenes')){
                $table->dropColumn('imagenes');
            }
        });
        Schema::table('categorias', function(Blueprint $table){
            if(Schema::hasColumn('productos', 'imagenes')){
                $table->dropColumn('imagenes');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            if(!Schema::hasColumn('productos', 'imagenes')){
                $table->json('imagenes')->nullable()->after('descripcion');
            }
        });
        Schema::table('categorias', function (Blueprint $table) {
            if(!Schema::hasColumn('productos', 'imagenes')){
                $table->json('imagenes')->nullable()->after('descripcion');
            }
        });
    }
};
