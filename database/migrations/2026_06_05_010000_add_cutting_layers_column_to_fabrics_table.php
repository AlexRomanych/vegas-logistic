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
        Schema::table('fabrics', function (Blueprint $table) {
            $table->integer("cutting_layers")->nullable(false)->default(0)->comment("Количество слоев ПС на раскрое");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fabrics', function (Blueprint $table) {
            $table->dropColumn("cutting_layers");
        });
    }
};
