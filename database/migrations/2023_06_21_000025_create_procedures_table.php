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
        Schema::create('procedures', function (Blueprint $table) {
            $table->comment('Процедуры расчета');
            $table->string('code1C');
            $table->primary('code1C');

            $table->string('name')->nullable(false)->comment('Название процедуры');

            $table->text('text')->nullable()->comment('Текст процедуры');
            $table->text('conversion_text')->nullable()->comment('Конвертированный текст процедуры');

            $table->string('object_code1C')->nullable(false)->comment('Код типа объекта процедуры по 1С');
            $table->string('object_type')->nullable(false)->comment('Типа объекта процедуры по 1С');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('procedures');
    }
};
