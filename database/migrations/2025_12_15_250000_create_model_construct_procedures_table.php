<?php

use App\Traits\AddCommonColumnsInTableTrait;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    use AddCommonColumnsInTableTrait;

    private const TABLE_NAME = 'model_construct_procedures';
    public function up(): void
    {
        Schema::create(self::TABLE_NAME, function (Blueprint $table) {
            $table->string(CODE_1C, CODE_1C_LENGTH)->primary()->comment('Код 1C');
            $table->string('name')->comment('Название процедуры');
            $table->text('text')->nullable()->comment('Текст процедуры');
            $table->string('object_code_1c', CODE_1C_LENGTH)->nullable()->comment('Вид объекта.Код');
            $table->string('object_name')->nullable()->comment('Вид объекта.Наименование');
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
