<?php

use App\Models\Model;
use App\Models\Order\AssemblyPart;
use App\Models\Order\CuttingPart;
use App\Models\Order\SewingPart;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('lines', function (Blueprint $table) {
            $table->id();

            $table->timestamp('load_1C')->nullable(false)->comment('загрузка данных в 1С');
            $table->string('size', 10)->nullable(false)->default('0x0x0')->comment('размер матраса');
            $table->integer('amount')->nullable(false)->default(0)->comment('количество');
            $table->string('name')->nullable(false)->comment('оригинальное имя модели в заказе'); // для вывода информации типа: Матрас (Хит 1)
            $table->string('textile')->nullable()->comment('ткань матраса');
            $table->string('composition')->nullable()->comment('состав');
            $table->string('comment')->nullable()->comment('комментарий');
            $table->string('describe_1')->nullable()->comment('описание 1');
            $table->string('describe_2')->nullable()->comment('описание 2');
            $table->string('describe_3')->nullable()->comment('описание 3');

/*
            $table->boolean('check_assembly')->nullable(false)->default(false)->comment('отметка сборки');
            $table->timestamp('moment_assembly')->nullable()->comment('дата и время сборки');

            $table->boolean('check_sewing')->nullable(false)->default(false)->comment('отметка швейки');
            $table->timestamp('moment_sewing')->nullable()->comment('дата и время швейки');

            $table->boolean('check_cutting')->nullable(false)->default(false)->comment('отметка раскройки');
            $table->timestamp('moment_cutting')->nullable()->comment('дата и время раскройки');

            // Привязываем модель матраса
            $table->string('model_code1C')->nullable();
            $table->foreign('model_code1C')
                ->references('code1C')
                ->on('models')
                ->onDelete('set null');
//                ->nullOnDelete();

//            $table->foreignIdFor(Model::class)
//                ->constrained()
//                ->nullOnDelete();

            // Привязываем к сборке
            $table->foreignIdFor(AssemblyPart::class)
                ->nullable()
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            // Привязываем к швейке
            $table->foreignIdFor(SewingPart::class)
                ->nullable()
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            // Привязываем к закройке
            $table->foreignIdFor(CuttingPart::class)
                ->nullable()
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
*/
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lines');
    }
};
