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
        Schema::table('order_line_material_pivot', function (Blueprint $table) {
            $table->boolean('active')->default(true)->comment('Учитывать эту запись или нет');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_line_material_pivot', function (Blueprint $table) {
            $table->dropColumn('active');
        });
    }
};
