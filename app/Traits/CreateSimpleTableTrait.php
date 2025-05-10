<?php

namespace App\Traits;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

trait CreateSimpleTableTrait
{
    /**
     * Создание простой таблицы
     * @param string $table_name
     * @return void
     */
    protected function createSimpleTable(string $table_name): void
    {
        Schema::create($table_name, function (Blueprint $table) {
            $table->id()->from(1);

            $table->boolean('active')->nullable(false)->default(true)->comment('Актуальность');
            $table->unsignedSmallInteger('status')->nullable()->comment('Статус');
            $table->string('description')->nullable()->comment('Описание');
            $table->string('comment')->nullable()->comment('Комментарий');
            $table->string('note')->nullable()->comment('Примечание');
            $table->json('meta')->nullable()->comment('Метаданные');

            $table->timestamps();
        });
    }
}
