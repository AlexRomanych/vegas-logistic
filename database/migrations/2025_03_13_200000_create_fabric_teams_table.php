<?php

use App\Models\Manufacture\CellItem;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
//use App\Models\Worker\Worker;

return new class extends Migration
{
    private const TABLE_NAME = 'fabric_teams';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create(self::TABLE_NAME, function (Blueprint $table) {
            $table->id()->from(5);
            $table->unsignedTinyInteger('team_no')->nullable(false)->unique()->comment('Номер бригады');
            $table->boolean('active')->nullable(false)->default(true)->comment('Актуальность записи');

            $table->string('name')->nullable()->comment('Название бригады');

            $table->string('description')->nullable()->comment('Описание бригады');
            $table->string('comment')->nullable()->comment('Комментарий');
            $table->string('note')->nullable()->comment('Примечание');

            // attract: задаем руководителя бригады
            $table->ForeignId('leader_id')->nullable(false)->default(0)->comment('Идентификатор руководителя бригады')
                ->references('id')
                ->on('workers')
                ->nullOnDelete();

            // attract: У каждого бригады может быть привязка к ПЯ
            $table->foreignIdFor(CellItem::class)->nullable()->constrained()->nullOnDelete();    //Внешний ключ, указывающий на таблицу с ПЯ

            $table->timestamps();
        });

        DB::table(self::TABLE_NAME)->insert([
            ['id' => 1, 'team_no' => 1],
            ['id' => 2, 'team_no' => 2],
            ['id' => 3, 'team_no' => 3],
            ['id' => 4, 'team_no' => 4],
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
