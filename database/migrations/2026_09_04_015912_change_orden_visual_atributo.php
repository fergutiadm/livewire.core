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
        Schema::table("atributos", function(Blueprint $table){
            $table->integer('orden_visual')->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table("atributos", function(Blueprint $table){
            $table->unsignedInteger('orden_visual')->nullable()->change();
        });
    }
};
