<?php

//use App\Models\Manufacture\Cells\Cutting\CuttingOperation;
use App\Models\Manufacture\Cells\Cutting\CuttingOperation;
use App\Traits\AddCommonColumnsInTableTrait;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    use AddCommonColumnsInTableTrait;

    const TABLE_NAME = 'cutting_operations';

    public function up(): void
    {
        Schema::create(self::TABLE_NAME, function (Blueprint $table) {
            $table->id();

            // __ Название операции
            $table->string('name')->nullable(false)->comment('Название операции');
            $table->string('machine')->nullable()->comment('Оборудование');
            $table->string('type')->nullable()->comment('Тип операции (статическая, динамическая и т.д.)');
            $table->double('time')->nullable(false)->default(0)->comment('Время операции, сек.');

            $table->string('detail')->nullable()->comment('Определения того, к какому виду детали относится операция, например, крышка или бурлет');
            $table->string('cover_type')->nullable()->comment('Тип чехла (м. Авт, УШМ и т.д.)');
            $table->string('table')->nullable()->comment('Стол, к которому относится операция');
            $table->string('additional')->nullable()->comment('Для дополнительной информации');

            $table->unique(['name']);
        });

        $this->addCommonColumns(self::TABLE_NAME);

        DB::table(self::TABLE_NAME)->insert([
            ['name'       => 'Раскрой панелей 100С (м. Авт)',
             'cover_type' => 'м. Авт',
             'table'      => '2 и 3',
             'time'       => 10.6,
             'type'       => CuttingOperation::DYNAMIC_TYPE,
             'detail'     => CuttingOperation::DETAIL_PANEL
            ],
            ['name'       => 'Раскрой панелей 200С (м. Авт)',
             'cover_type' => 'м. Авт',
             'table'      => '2 и 3',
             'time'       => 23.7,
             'type'       => CuttingOperation::DYNAMIC_TYPE,
             'detail'     => CuttingOperation::DETAIL_PANEL
            ],
            ['name'       => 'Раскрой панелей 300С (м. Авт)',
             'cover_type' => 'м. Авт',
             'table'      => '2 и 3',
             'time'       => 23.7,
             'type'       => CuttingOperation::DYNAMIC_TYPE,
             'detail'     => CuttingOperation::DETAIL_PANEL
            ],
            ['name'       => 'Раскрой панелей с торцевой вставкой',
             'cover_type' => 'м. Авт',
             'table'      => '2 и 3',
             'time'       => 20.5,
             'type'       => CuttingOperation::DYNAMIC_TYPE,
             'detail'     => CuttingOperation::DETAIL_PANEL
            ],
            ['name'       => 'Раскрой панелей 100С (УШМ)',
             'cover_type' => 'УШМ',
             'table'      => '2 и 3',
             'time'       => 10.6,
             'type'       => CuttingOperation::DYNAMIC_TYPE,
             'detail'     => CuttingOperation::DETAIL_PANEL
            ],
            ['name'       => 'Раскрой панелей 200С (УШМ)',
             'cover_type' => 'УШМ',
             'table'      => '2 и 3',
             'time'       => 10.6,
             'type'       => CuttingOperation::DYNAMIC_TYPE,
             'detail'     => CuttingOperation::DETAIL_PANEL
            ],
            ['name'       => 'Раскрой панелей 300С (УШМ)',
             'cover_type' => 'УШМ',
             'table'      => '2 и 3',
             'time'       => 17.8,
             'type'       => CuttingOperation::DYNAMIC_TYPE,
             'detail'     => CuttingOperation::DETAIL_PANEL
            ],
            ['name'       => 'Раскрой панелей нестеганых',
             'cover_type' => 'УШМ',
             'table'      => '2 и 3',
             'time'       => 13.0,
             'type'       => CuttingOperation::DYNAMIC_TYPE,
             'detail'     => CuttingOperation::DETAIL_PANEL
            ],
            ['name'       => 'Раскрой 1 наполнителя',
             'cover_type' => 'УШМ',
             'table'      => '2 и 3',
             'time'       => 10.0,
             'type'       => CuttingOperation::DYNAMIC_TYPE,
             'detail'     => CuttingOperation::DETAIL_PANEL
            ],
            ['name'       => 'Раскрой 2-х наполнителей',
             'cover_type' => 'УШМ',
             'table'      => '2 и 3',
             'time'       => 20.0,
             'type'       => CuttingOperation::DYNAMIC_TYPE,
             'detail'     => CuttingOperation::DETAIL_PANEL
            ],
            ['name'       => 'Раскрой 5-и наполнителей',
             'cover_type' => 'УШМ',
             'table'      => '2 и 3',
             'time'       => 50.0,
             'type'       => CuttingOperation::DYNAMIC_TYPE,
             'detail'     => CuttingOperation::DETAIL_PANEL
            ],
            ['name'       => 'Раскрой детали боковина цельная',
             'cover_type' => 'УШМ',
             'table'      => '1',
             'time'       => 6.6,
             'type'       => CuttingOperation::DYNAMIC_TYPE,
             'detail'     => CuttingOperation::DETAIL_SIDE
            ],
            ['name'       => 'Раскрой детали боковина',
             'cover_type' => '2 детали	УШМ',
             'table'      => '1',
             'time'       => 10.6,
             'type'       => CuttingOperation::DYNAMIC_TYPE,
             'detail'     => CuttingOperation::DETAIL_SIDE
            ],
            ['name'       => 'Раскрой детали боковина цельная не стеганая',
             'cover_type' => 'УШМ',
             'table'      => '1',
             'time'       => 4.5,
             'type'       => CuttingOperation::DYNAMIC_TYPE,
             'detail'     => CuttingOperation::DETAIL_SIDE
            ],
            ['name'       => 'Раскрой детали боковина 3D сетка',
             'cover_type' => 'УШМ',
             'table'      => '1',
             'time'       => 7.0,
             'type'       => CuttingOperation::DYNAMIC_TYPE,
             'detail'     => CuttingOperation::DETAIL_SIDE
            ],
            ['name'       => 'Раскрой детали боковины с пилоутопером',
             'cover_type' => 'УШМ',
             'table'      => '1',
             'time'       => 42.7,
             'type'       => CuttingOperation::DYNAMIC_TYPE,
             'detail'     => CuttingOperation::DETAIL_SIDE
            ],
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
