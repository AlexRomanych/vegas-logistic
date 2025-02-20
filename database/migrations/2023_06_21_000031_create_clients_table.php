<?php

use App\Models\Manager;
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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable(false)->comment('Название клиента');
            $table->string('add_name')->default('')->comment('Дополнительный идентификатор клиента');
            $table->string('short_name')->nullable(false)->comment('Отображаемое название клиента');
            $table->string('description')->nullable()->comment('Дополнительная информация');
            $table->string('region')->nullable()->comment('Регион');
            $table->string('country')->nullable()->comment('Страна регистрации');
            $table->string('address')->nullable()->comment('Адрес');
            $table->decimal('longitude', 8,6,true)->nullable()->default(0)->comment('Долгота');
            $table->decimal('latitude', 8,6,true)->nullable()->default(0)->comment('Широта');

            $table->unsignedTinyInteger('active')->nullable(false)->default(0)->comment('Активный или нет');

//            $table->index(['name', 'add_name', 'short_name']);
            $table->timestamps();

            $table->foreignIdFor(Manager::class)->nullable(false)->default(100)->constrained();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
