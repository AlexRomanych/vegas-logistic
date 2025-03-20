<?php

use App\Models\Manufacture\Cells\Fabric\FabricMachine;
use App\Models\Manufacture\Cells\Fabric\FabricPicture;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    private const FABRIC_TABLE_NAME = 'fabrics';        // Название таблицы с тканями (эта таблица)
    private const TIME_LOSS_IN_PERCENTAGE = 5;          // Организационные потери во времени в процентах на изготовление ткани
    private const DEFAULT_TRANSLATE_RATE = 1.044;       // Коэффициент перевода в ткани
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create(self::FABRIC_TABLE_NAME, function (Blueprint $table) {
            $table->id()->from(1);
            $table->string('code_1C')->unique()->nullable(false)->comment('Код 1С');
            $table->string('name')->nullable(false)->unique()->comment('Название');
            $table->float('buffer_min')->nullable(false)->default(0.0)->comment('Минимальное количество в буфере, м.п.');
            $table->float('buffer_max')->nullable(false)->default(0.0)->comment('Максимальное количество в буфере, м.п.');
            $table->float('optimal_party')->nullable(false)->default(0.0)->comment('Оптимальная партия для запуска, м.п.');
            $table->unsignedSmallInteger('rolls_amount')->nullable(false)->default(1)->comment('Количество рулонов ткани, шт.');
            $table->float('average_roll_length')->nullable(false)->default(0.0)->comment('Средняя длина рулона, м.п.');
            $table->float('translate_rate')->nullable(false)->default(self::DEFAULT_TRANSLATE_RATE)->comment('Коэффициент перевода');   // Непонятно пока зачем, но он равен 1,044 примерно
            $table->float('productivity')->nullable(false)->default(0)->comment('Производительность, м.п./час ');
            $table->unsignedSmallInteger('load_roll_time')->nullable(false)->default(0)->comment('Время загрузки рулона, мин.');
            $table->unsignedSmallInteger('time_loss')->nullable(false)->default(self::TIME_LOSS_IN_PERCENTAGE)->comment('Организационные потери во времени в процентах на изготовление ПС');

            $table->boolean('active')->nullable(false)->default(true)->comment('Актуальность или архив');
            $table->boolean('rare')->nullable(false)->default(false)->comment('Редкий или нет');
            $table->enum('belong_to', ['vegas', 'other'])->nullable(false)->default('vegas')->comment('Для собственного производства или аутсорс');

            $table->string('description')->nullable()->comment('Описание');
            $table->string('comment')->nullable()->comment('Комментарий');
            $table->string('note')->nullable()->comment('Примечание');
            $table->timestamps();

//            $table->foreignIdFor(FabricMachine::class)->nullable(false)->default(1)->comment('Привязка ПС к стегальной машине')->constrained()->nullOnDelete();
            $table->foreignIdFor(FabricPicture::class)->nullable(false)->default(0)->comment('Привязка ПС к рисунку стежки')->constrained()->nullOnDelete();

//            $table->json('alternative_machines')->nullable()->comment('Альтернативные машины');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(self::FABRIC_TABLE_NAME);
    }
};
