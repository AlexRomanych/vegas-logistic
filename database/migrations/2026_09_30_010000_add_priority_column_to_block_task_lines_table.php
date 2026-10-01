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
        Schema::table('block_task_lines', function (Blueprint $table) {
            $table->integer('priority_manual')->nullable()->comment('Приоритет выполнения, выставленный вручную');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('block_task_lines', function (Blueprint $table) {
            $table->dropColumn('priority_manual');
        });
    }
};
