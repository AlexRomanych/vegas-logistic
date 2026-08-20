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
        Schema::table('assembly_task_lines', function (Blueprint $table) {
            $table->string('assembly_line')->nullable()->comment('Линия сборки: Lamit / Стол');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assembly_task_lines', function (Blueprint $table) {
            $table->dropColumn('assembly_line');
        });
    }
};
