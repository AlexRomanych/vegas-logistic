<?php

use App\Models\Manager;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    private const TABLE_NAME = 'clients';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create(self::TABLE_NAME, function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable(false)->comment('Название клиента');
            $table->string('add_name')->default('')->comment('Дополнительный идентификатор клиента');
            $table->string('short_name')->nullable(false)->comment('Отображаемое название клиента');
            $table->string('region')->nullable()->comment('Регион');
            $table->string('country')->nullable()->comment('Страна регистрации');
            $table->string('address')->nullable()->comment('Адрес');
            $table->decimal('longitude', 8,6,true)->nullable()->default(0)->comment('Долгота');
            $table->decimal('latitude', 8,6,true)->nullable()->default(0)->comment('Широта');

//            $table->index(['name', 'add_name', 'short_name']);
            $table->timestamps();

            $table->foreignIdFor(Manager::class)->nullable(false)->default(100)->constrained();

            $table->boolean('active')->nullable(false)->default(true)->comment('Активный или нет');
//            $table->unsignedTinyInteger('active')->nullable(false)->default(0)->comment('Активный или нет');
            $table->string('description')->nullable()->comment('Описание');
            $table->string('comment')->nullable()->comment('Комментарий');
            $table->string('note')->nullable()->comment('Примечание');


            $table->json('meta')->nullable()->comment('Meta-информация');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(self::TABLE_NAME);
    }
};
