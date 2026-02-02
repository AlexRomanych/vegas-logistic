<?php

use App\Traits\AddCommonColumnsInTableTrait;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    use AddCommonColumnsInTableTrait;

    const TABLE_NAME = 'sewing_operation_schemas';

    public function up(): void
    {
        Schema::create(self::TABLE_NAME, function (Blueprint $table) {
            $table->id()->from(1);
            $table->string('name')->unique()->nullable(false)->comment('Название схемы');

            $table->string('additional')->nullable()->comment('Для дополнительной информации');
        });

        $this->addCommonColumns(self::TABLE_NAME);

        DB::table(self::TABLE_NAME)->insert(
            [
                'id' => 0,
                'name' => 'Без схемы',
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
