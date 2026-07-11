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
        Schema::table('block_collections', function (Blueprint $table) {
            $table->integer('priority_2')->nullable(false)->default(0)->comment('Приоритет выполнения на линии 2');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('block_collections', function (Blueprint $table) {
            $table->dropColumn('priority_2');
        });
    }
};
