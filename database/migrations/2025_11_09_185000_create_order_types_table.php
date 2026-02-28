<?php

use App\Traits\AddCommonColumnsInTableTrait;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    use AddCommonColumnsInTableTrait;

    private const TABLE_NAME = 'order_types';

    public function up(): void
    {
        Schema::create(self::TABLE_NAME, function (Blueprint $table) {
            $table->id()->from(1);
            $table->string('name')->unique()->nullable(false)->comment('Название');
            $table->string('display_name')->unique()->nullable(false)->comment('Отображаемое название');
            $table->string('type_index')->nullable(false)->comment('Индекс после точки');
            // $table->string('color', 7)->default('#64748B')->comment('Цвет рендера');
        });

        $this->addCommonColumns(self::TABLE_NAME);

        DB::table(self::TABLE_NAME)->insert([
            'id'           => 0,
            'name'         => 'Не определено',
            'display_name' => 'н/д',
            'type_index'   => '.00',
            'description'  => 'Тип заявки не определен',
            'color'        => '#64748B'
        ]);

        DB::table(self::TABLE_NAME)->insert([
            'name'         => 'Плановая заявка',
            'display_name' => 'плановая заявка',
            'type_index'   => AVERAGE_TYPE_INDEX,
            'description'  => 'Плановая заявка',
            'color'        => '#C0C0C0'
        ]);

        DB::table(self::TABLE_NAME)->insert([
            'name'         => 'Основная заявка',
            'display_name' => 'основная заявка',
            'type_index'   => '_',
            'description'  => 'Основная заявка',
            'color'        => '#22C55E'
        ]);

        DB::table(self::TABLE_NAME)->insert([
            'name'         => 'Дополнительная заявка клиентов',
            'display_name' => 'доп. заявка',
            'type_index'   => '.1-10; .20-...',
            'description'  => 'Номера, присваиваемые дополнительным заявкам клиентов',
            'color'        => '#3B82F6'
        ]);

        DB::table(self::TABLE_NAME)->insert([
            'name'         => 'Гарантийный ремонт',
            'display_name' => 'гар. ремонт',
            'type_index'   => '.11',
            'description'  => 'Номер присваивается заявке с изделиями для гарантийного ремонта',
            'color'        => '#EAB308'
        ]);

        DB::table(self::TABLE_NAME)->insert([
            'name'         => 'Изделия для',
            'display_name' => 'изделия для',
            'type_index'   => '.12',
            'description'  => 'Номер присваивается заявке с изделиями для'
        ]);

        DB::table(self::TABLE_NAME)->insert([
            'name'         => 'Подлежащие маркировке',
            'display_name' => 'для маркировки',
            'type_index'   => '.14',
            'description'  => 'Номер присваивается заявкам на аксессуарам, подлежащим маркировке Электронным/Честным знаком и не имеющим кода GTIN'
        ]);

        DB::table(self::TABLE_NAME)->insert([
            'name'         => 'Постановочная партия',
            'display_name' => 'постановочная партия',
            'type_index'   => '.15',
            'description'  => 'Номер присваивается заявкам с постановочными партиями изделий'
        ]);

        DB::table(self::TABLE_NAME)->insert([
            'name'         => 'Акцизная маркировка аксессуаров',
            'display_name' => 'акцизная маркировка аксессуаров',
            'type_index'   => '.16',
            'description'  => 'Номер присваивается заявке на аксессуары, которые требуют маркировку специальным акцизом'
        ]);

        DB::table(self::TABLE_NAME)->insert([
            'name'         => 'Контроль КД и проверка БТК',
            'display_name' => 'контроль КД и БТК',
            'type_index'   => '.17',
            'description'  => 'Номер присваивается заявкам изделия, производство которых требует тщательного контроля и соблюдения КД, проверку БТК'
        ]);

        DB::table(self::TABLE_NAME)->insert([
            'name'         => 'Образцы, которые производят ПО без ОРП',
            'display_name' => 'образцы без ОРП',
            'type_index'   => '.18',
            'description'  => 'Номер присваивается заявкам на образцы, которые производят ПО без ОРП и отгружают клиенту'
        ]);

        DB::table(self::TABLE_NAME)->insert([
            'name'         => 'Партийная заявка (от 5 шт.)',
            'display_name' => 'партийная заявка',
            'type_index'   => '.19',
            'description'  => 'Номер, присваиваемый партийной заявке (заказ каждой номенклатуры от 5 шт.)'
        ]);

        DB::table(self::TABLE_NAME)->insert([
            'name'         => 'Матрасы коллекции 100_Vegas Taftting',
            'display_name' => 'коллекция Taftting',
            'type_index'   => '.21',
            'description'  => 'Номер, присваиваемый заявке на матрасы коллекции "100_Vegas Taftting" со сроком производства 10 дней (со дня загрузки заявки в 1С)'
        ]);

        DB::table(self::TABLE_NAME)->insert([
            'name'         => 'ЛММ на оптовый склад',
            'display_name' => 'ЛММ на оптовый склад',
            'type_index'   => '.1',
            'description'  => 'Номер, присваиваемый заявкам ЛММ, которые отгружаются на оптовый склад (загружаются в машину после «.2»)',
            'comment'      => 'Исключение для заявок ЛММ с ноября 2024'
        ]);

        DB::table(self::TABLE_NAME)->insert([
            'name'         => 'ЛММ на розничный склад',
            'display_name' => 'ЛММ на розничный склад',
            'type_index'   => '.2',
            'description'  => 'Номер, присваиваемый заявкам ЛММ, которые отгружаются на розничный склад (загружаются в машину в первую очередь)',
            'comment'      => 'Исключение для заявок ЛММ с ноября 2024'
        ]);

        DB::table(self::TABLE_NAME)->insert([
            'name'         => 'ЛММ партийная на оптовый склад',
            'display_name' => 'ЛММ партийная на оптовый склад',
            'type_index'   => '.119',
            'description'  => 'Номер, присваиваемый партийным заявкам ЛММ, которые отгружаются на оптовый склад (загружаются в машину после «.2»)',
            'comment'      => 'Исключение для заявок ЛММ с ноября 2024'
        ]);

        DB::table(self::TABLE_NAME)->insert([
            'name'         => 'ЛММ партийная на розничный склад и контроль КД и БТК',
            'display_name' => 'ЛММ партийная на розничный склад и контроль КД и БТК',
            'type_index'   => '.217',
            'description'  => 'Номер, присваиваемый партийным заявкам ЛММ, которые отгружаются на розничный склад и требуют тщательного контроля и соблюдения КД, проверку БТК',
            'comment'      => 'Исключение для заявок ЛММ с ноября 2024'
        ]);

        DB::table(self::TABLE_NAME)->insert([
            'name'         => 'ЛММ партийная на розничный склад',
            'display_name' => 'ЛММ партийная на розничный склад',
            'type_index'   => '.219',
            'description'  => 'Номер, присваиваемый партийным заявкам ЛММ, которые отгружаются на розничный склад (загружаются в машину в первую очередь)',
            'comment'      => 'Исключение для заявок ЛММ с ноября 2024'
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
