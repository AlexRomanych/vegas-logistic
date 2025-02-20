<?php

use App\Models\Manager;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $defaultManagerId = 100;    // менеджер по умолчанию (не определен)

        Schema::create('managers', function (Blueprint $table) use ($defaultManagerId) {
            $table->id('id')->from($defaultManagerId);
            $table->string('surname')->nullable(false)->comment('Фамилия');
            $table->string('first_name')->nullable(false)->comment('Имя');
            $table->string('second_name')->default('')->comment('Отчество');
            $table->string('description')->nullable()->comment('Описание');

            $table->timestamps();
        });

        DB::table('managers')->insert([
            'id' => $defaultManagerId,
            'surname' => Manager::DEFAULT_MANAGER_NAME,
            'first_name' => '',
            'second_name' => '',
            'description' => 'менеджер по умолчанию',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('managers');
    }
};
