<?php

use App\Models\Manufacture\CellsGroup;
use Illuminate\Database\Migrations\Migration;
//use Illuminate\Database\Schema\Blueprint;
//use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // ___ Добавляем группу по умолчанию (общую)

    private const NULL_ID = 0;

    public function up(): void
    {
        CellsGroup::query()->firstOrCreate(
            ['id' => self::NULL_ID, 'name' => 'Общая', 'created_at' => now(), 'updated_at' => now(),],
        );
    }


    public function down(): void
    {
        CellsGroup::query()->where('id', self::NULL_ID)->delete();
    }
};
