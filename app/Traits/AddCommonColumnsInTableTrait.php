<?php

namespace App\Traits;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

trait AddCommonColumnsInTableTrait
{
    /**
     * Создание простой таблицы
     * @param string $table_name
     * @return void
     */
    protected function addCommonColumns(string $table_name): void
    {
        Schema::table($table_name, function (Blueprint $table) {

            $table->boolean('active')->nullable(false)->default(true)->comment('Актуальность');
            $table->unsignedSmallInteger('status')->nullable()->comment('Статус');
            $table->string('description')->nullable()->comment('Описание');
            $table->string('comment')->nullable()->comment('Комментарий');
            $table->string('note')->nullable()->comment('Примечание');
            $table->json('meta')->nullable()->comment('Метаданные');
            $table->string('color', 7)->default('#64748B')->comment('Цвет рендера');


            $table->timestamps();
        });
    }
}
