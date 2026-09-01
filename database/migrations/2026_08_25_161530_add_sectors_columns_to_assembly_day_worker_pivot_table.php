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
        Schema::table('assembly_day_worker_pivot', function (Blueprint $table) {
            $table->jsonb('sectors')->nullable()->comment('Участки Сборки, если один на нескольких участках');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assembly_day_worker_pivot', function (Blueprint $table) {
            $table->dropColumn(['sectors']);
        });
    }
};
