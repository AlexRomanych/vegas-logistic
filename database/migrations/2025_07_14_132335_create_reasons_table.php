<?php

use App\Models\Manufacture\Reasons\ReasonCategory;
use App\Traits\AddCommonColumnsInTableTrait;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    use AddCommonColumnsInTableTrait;

    private const TABLE_NAME = 'reasons';

    public function up(): void
    {
        Schema::create(self::TABLE_NAME, function (Blueprint $table) {
            $table->id()->from(1);

            $table->string('name')
                ->nullable(false)
                ->unique()
                ->comment('Название причины');
            $table->string('display_name')
                ->nullable(false)
                ->unique()
                ->comment('Отображаемое название причины');

            // __ Связь с категорией
            $table->foreignIdFor(ReasonCategory::class)
                ->nullable(false)
                ->comment('Категория причины')
                ->constrained()
                ->cascadeOnDelete();
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
