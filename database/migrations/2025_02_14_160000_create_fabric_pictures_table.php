<?php

use App\Models\FabricPictureSchema;
use App\Models\Manufacture\Cells\Fabric\FabricMachine;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const FABRIC_PICTURES_TABLE = 'fabric_pictures';                // Таблица с рисунками (эта таблица)
    private const FABRIC_MACHINES_TABLE = 'fabric_machines';                // Таблица со стегальными машинами
    private const FABRIC_PICTURES_SCHEMA_TABLE = 'fabric_picture_schemas';  // Таблица со стегальными машинами

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create(self::FABRIC_PICTURES_TABLE, function (Blueprint $table) {
            $table->id()->from(0);
            $table->string('name')->nullable(false)->unique()->comment('Название рисунка');
            $table->boolean('active')->nullable(false)->default(true)->comment('Актуальность рисунка');
//            $table->string('rows')->nullable()->comment('Количество задействованных рядов');  // '1' - 1 ряд, '1-2' - 1, 2 ряд, '1-3' - 1, 3 ряд
            $table->float('stitch_length')->nullable(false)->default(0)->comment('Длина стежка в мм'); // Длина стежка по КД, мм
            $table->integer('stitch_speed')->nullable(false)->default(0)->comment('Скорость стежков, шт./мин.'); // Скорость стежков шт./мин.
            $table->integer('moment_speed')->nullable(false)->default(0)->comment('Мгновенная скорость, м/ч'); // Мгновенная скорость, м/час
            $table->integer('shuttle_amount')->nullable()->comment('Количество челноков для Корейца'); // Количество челноков для Корейца

            $table->string('description')->nullable()->comment('Описание рисунка');
            $table->string('comment')->nullable()->comment('Комментарий к рисунку');
            $table->binary('image')->nullable()->comment('Рисунок стежки');


            // attract: привязка идет парами: стегальная машина и схема расположения игл для данной стегальной машины

            // Основная стегальная машина
            //hr-------------------------------------------------------------------------
            $table->foreignIdFor(FabricMachine::class)->nullable(false)->default(0)->comment('Привязка к основной стегальной машине')
                ->constrained()
                ->nullOnDelete();

            $table->foreignIdFor(FabricPictureSchema::class)->nullable(false)->default(0)->comment('Привязка к схеме расположения игл для основной стегальной машины')
                ->constrained()
                ->nullOnDelete();


            // Альтернативные машины в порядке приоритета
            //hr-------------------------------------------------------------------------
            $table->foreignId('alt_machine_1_id')->nullable(false)->default(0)->comment('Привязка к альтернативной стегальной машине №1')
                ->references('id')
                ->on(self::FABRIC_MACHINES_TABLE)
                ->nullOnDelete();

            $table->foreignId('alt_machine_1_schema_id')->nullable(false)->default(0)->comment('Привязка к схеме расположения игл для альтернативной стегальной машины №1')
                ->references('id')
                ->on(self::FABRIC_PICTURES_SCHEMA_TABLE)
                ->nullOnDelete();


            $table->foreignId('alt_machine_2_id')->nullable(false)->default(0)->comment('Привязка к альтернативной стегальной машине №2')
                ->references('id')
                ->on(self::FABRIC_MACHINES_TABLE)
                ->nullOnDelete();

            $table->foreignId('alt_machine_2_schema_id')->nullable(false)->default(0)->comment('Привязка к схеме расположения игл для альтернативной стегальной машины №2')
                ->references('id')
                ->on(self::FABRIC_PICTURES_SCHEMA_TABLE)
                ->nullOnDelete();


            $table->foreignId('alt_machine_3_id')->nullable(false)->default(0)->comment('Привязка к альтернативной стегальной машине №3')
                ->references('id')
                ->on(self::FABRIC_MACHINES_TABLE)
                ->nullOnDelete();

            $table->foreignId('alt_machine_3_schema_id')->nullable(false)->default(0)->comment('Привязка к схеме расположения игл для альтернативной стегальной машины №3')
                ->references('id')
                ->on(self::FABRIC_PICTURES_SCHEMA_TABLE)
                ->nullOnDelete();


            $table->timestamps();
        });


        DB::table(self::FABRIC_PICTURES_TABLE)->insert([
            ['id' => 0, 'name' => 'Рисунок не определен'],
        ]);

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(self::FABRIC_PICTURES_TABLE);
    }
};
