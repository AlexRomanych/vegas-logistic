<?php

use App\Traits\AddCommonColumnsInTableTrait;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    use AddCommonColumnsInTableTrait;

    private const TABLE_NAME = 'cutting_procedures';
    public function up(): void
    {
        Schema::create(self::TABLE_NAME, function (Blueprint $table) {
            $table->id()->from(1);
            $table->string('name')->nullable(false)->unique()->comment('Название процедуры');
            $table->text('text')->nullable()->comment('Текст процедуры');
            $table->string('object_name')->nullable()->comment('Вид объекта, для которого создается процедура');
            $table->text('text_meta')->nullable()->comment('Текст процедуры дополнительный');
        });

        $this->addCommonColumns(self::TABLE_NAME);

        DB::table(self::TABLE_NAME)->insert(
            [
                'id' => 0,
                'name' => 'Без процедуры',
            ]
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(self::TABLE_NAME);
    }
};
