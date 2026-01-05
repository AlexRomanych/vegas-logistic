<?php

use App\Traits\AddCommonColumnsInTableTrait;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    use AddCommonColumnsInTableTrait;

    private const TABLE_NAME = 'sewing_task_statuses';
    public function up(): void
    {
        Schema::create(self::TABLE_NAME, function (Blueprint $table) {
            $table->id()->from(1);

            // __ Название статуса
            $table->string('name')
                ->nullable(false)
                ->unique()
                ->comment('Название статуса');

            // __ Позиция (порядок) статуса
            $table->integer('position')
                ->nullable(false)
                ->default(0)
                ->comment('Позиция (порядок) статуса');
        });

        $this->addCommonColumns(self::TABLE_NAME);

        DB::table(self::TABLE_NAME)->insert([
            ['name' => 'Создано', 'position' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Готово к выполнению', 'position' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Выполняется', 'position' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Выполнено', 'position' => 4, 'created_at' => now(), 'updated_at' => now()],
        ]);

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(self::TABLE_NAME);
    }
};
