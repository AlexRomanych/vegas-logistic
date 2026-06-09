<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    private const TABLE = 'block_design_documents';

    public function up(): void
    {
        Schema::create(self::TABLE, function (Blueprint $table) {
            $table->id();
            $table->string('kdb')->unique()->nullable(false)->comment('Название КДБ'); // Наш код "11033"
            $table->string('file_path')->nullable()->comment('Путь к файлу');       // Путь к файлу в storage
            $table->text('description')->nullable()->comment('Описание');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(self::TABLE);
    }
};
