<?php

use App\Models\Client;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    private const TABLE_NAME = 'clients';
    private const COLUMN_NAME = CODE_1C;

    public function up(): void
    {
        Schema::table(self::TABLE_NAME, function (Blueprint $table) {
            $table->string(self::COLUMN_NAME)->nullable()->comment('Код клиента из 1С');
        });

        Client::query()->where('id', 21)->update([CODE_1C => '003454',]);
        Client::query()->where('id', 32)->update([CODE_1C => '001033',]);
        Client::query()->where('id', 46)->update([CODE_1C => '002801',]);
        Client::query()->where('id', 63)->update([CODE_1C => '004033',]);
        Client::query()->where('id', 98)->update([CODE_1C => 'БП0002291',]);
        Client::query()->where('id', 99)->update([CODE_1C => 'БП0002483',]);
        Client::query()->where('id', 100)->update([CODE_1C => 'БП0002482',]);
        Client::query()->where('id', 101)->update([CODE_1C => 'БП0001471',]);
        Client::query()->where('id', 102)->update([CODE_1C => 'БП0002542',]);


        // 1-й массив: данные
        // 2-й массив: уникальный ключ для поиска (id)
        // 3-й массив: какие поля обновить, если запись найдена
        // Если запись не найдена, она будет создана
        // Client::query()->upsert([
        //     ['id' => 21, CODE_1C => '003454',],
        //     ['id' => 46, CODE_1C => '002801',],
        //     ['id' => 63, CODE_1C => '004033',],
        //     ['id' => 98, CODE_1C => 'БП0002291',],
        //     ['id' => 99, CODE_1C => 'БП0002483',],
        //     ['id' => 100, CODE_1C => 'БП0002482',],
        //     ['id' => 101, CODE_1C => 'БП0001471',],
        //     ['id' => 102, CODE_1C => 'БП0002542',],
        // ], ['id'], [CODE_1C]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table(self::TABLE_NAME, function (Blueprint $table) {
            // Проверяем, существует ли столбец перед удалением
            if (Schema::hasColumn(self::TABLE_NAME, self::COLUMN_NAME)) {
                $table->dropColumn(self::COLUMN_NAME);
            }
        });
    }
};
