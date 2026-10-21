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
        Schema::table('denominaciones', function (Blueprint $table) {
            if(!Schema::hasColumn('denominaciones', 'color_bg')){
                $table->string('color_bg', 50)->nullable()->after('activa');
            }
            if(!Schema::hasColumn('denominaciones', 'color_text')){
                $table->string('color_text', 50)->nullable()->after('color_bg');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('denominaciones', function (Blueprint $table) {
            if(Schema::hasColumn('denominaciones', 'color_bg')){
                $table->dropColumn('color_bg');
            }
            if(Schema::hasColumn('denominaciones', 'color_text')){
                $table->dropColumn('color_text');
            }
        });
    }
};
