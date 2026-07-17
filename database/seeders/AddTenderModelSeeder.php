<?php

namespace Database\Seeders;

use App\Models\Models\Model;
use App\Models\Models\ModelManufactureStatus;
use App\Models\Models\ModelType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AddTenderModelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // __ 1. Статус
        $status = ModelManufactureStatus::query()->updateOrCreate(
            ['id' => 100],
            ['name' => 'Нестандартные']
        );

        // __ 2. Тип
        $type = ModelType::query()->updateOrCreate(
            [CODE_1C => NS_TENDER_CODE],
            ['name' => 'Нестандартная (тендерная) модель']
        );

        // __ 3. Модель (используем объекты, полученные выше)
        Model::query()->updateOrCreate(
            [CODE_1C => NS_TENDER_CODE],
            [
                'name' => 'Нестандартная (тендерная) модель',
                'model_manufacture_status_id' => $status->id,
                'model_type_code_1c' => $type->code_1c,
            ]
        );
    }
}
