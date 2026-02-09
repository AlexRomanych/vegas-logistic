<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    const TABLE_NAME = 'workers';

    /** @noinspection DuplicatedCode */
    public function up(): void
    {
        Schema::table(self::TABLE_NAME, function (Blueprint $table) {
            $table->boolean('active')->nullable(false)->default(true)->comment('Актуальность');
            $table->string('description')->nullable()->comment('Описание');
            $table->string('comment')->nullable()->comment('Комментарий');
            $table->string('note')->nullable()->comment('Примечание');
            $table->json('meta')->nullable()->comment('Метаданные');
            $table->string('color', 7)->default('#64748B')->comment('Цвет рендера');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table(self::TABLE_NAME, function (Blueprint $table) {

            // __ Удаляем колонки в порядке, обратном созданию
            $table->dropColumn([
                'active',
                'description',
                'comment',
                'note',
                'meta',
                'color',
            ]);
        });
    }
};
