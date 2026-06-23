<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::table('order_lines', function (Blueprint $table) {

            // __ Название Спецификации для каждой Модели
            $table->string('construct_name')->nullable()->comment('Название Спецификации из 1С, по которому происходит расчет сырья');

            // __ Код Дополнительной Спецификации для каждой Ммодели
            $table->string('construct_add_code_1c')->nullable()->comment('Код Дополнительной Спецификации для каждой Модели из 1С, по которому происходит расчет сырья');

            // __ Название Дополнительной Спецификации для каждой Ммодели
            $table->string('construct_add_name')->nullable()->comment('Название Дополнительной Спецификации из 1С, по которому происходит расчет сырья');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_lines', function (Blueprint $table) {
            $table->dropColumn(['construct_name','construct_add_code_1c','construct_add_name']);
        });
    }

};
