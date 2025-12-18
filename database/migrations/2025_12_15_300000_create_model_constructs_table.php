<?php

use App\Traits\AddCommonColumnsInTableTrait;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    use AddCommonColumnsInTableTrait;
    private const TABLE_NAME = 'model_constructs';

    public function up(): void
    {
        Schema::create(self::TABLE_NAME, function (Blueprint $table) {

            $table->string(CODE_1C, CODE_1C_LENGTH)->primary()->comment('Код по 1С');

            // __ Название спецификации
            $table->string('name')->nullable(false)->comment('Название спецификации');

            // Relations: Связь с Моделью
            $table->string('model_code_1c',CODE_1C_LENGTH)
                ->nullable(false)
                ->comment('Модель');
            $table->foreign('model_code_1c')
                ->references(CODE_1C)
                ->on('models')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            // __ Копия кода 1С Модели (При обновлении Спецификаций и каскадном nullOnDelete, след Модели)
            // $table->string('model_code_1c_copy', CODE_1C_LENGTH)
            //     ->nullable()
            //     ->comment('Копия кода 1С Модели');

            // __ Название модели (избыточно, так как есть в модели, пока оставляем для информативности)
            $table->string('model_name')->nullable(false)->comment('Название модели в 1С');

            $table->string('type')->nullable()->comment('Тип спецификации (чехол, мэ и т.д.)');
        });

        $this->addCommonColumns(self::TABLE_NAME);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(self::TABLE_NAME);
    }
};
