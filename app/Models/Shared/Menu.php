<?php

namespace App\Models\Shared;


class Menu
{
    private const ROUTE_DEFAULT = '#';                          // Маршрут по умолчанию

    private array $menuList;

    /*
     *   'СЗ сборка'  => ['manufactures.om_assembly', false, true]
     *   первое "false" - активный элемент меню или нет
     *   второе "true" - disabled или enabled
       */
    public function __construct()
    {
        $this->menuList = [
            new MenuItem(
                'Производство', route('manufactures'), 'icon-cog',
                [
                    'СЗ сборка'  => ['manufactures.om_assembly', false, true],
                    'СЗ швейка'  => ['manufactures.om_sewing', false, false],
                    'СЗ раскрой' => ['manufactures.om_cutting', false, false],
                ]
            ),
            new MenuItem(
                'План', route('plan'), 'icon-item',
                [
                    'Сборки'    => ['plan.' . ASSEMBLY_PLAN_TYPE, false, true],
                    'Швейки'    => ['plan.' . SEWING_PLAN_TYPE, false, true],
                    'Раскройки' => ['plan.' . CUTTING_PLAN_TYPE, false, true],
                    'Отгрузок'  => ['plan.' . LOADS_PLAN_TYPE, false, true],
                ]
            ),
            new MenuItem(
                'Справочники', route('reference'), 'icon-book',
                [
                    'Модели'            => ['reference.models', false, false],
                    'Спецификации'      => ['reference.specifications', false, false],
                    'Процедуры расчета' => ['reference.procedures', false, false],
                    'Клиенты'           => ['reference.clients', false, false],
                    'Трудозатраты'      => ['reference.labors', false, false],
                ]
            ),
            new MenuItem(
                'Заявки', route('orders.index'), 'icon-order',
                [
                ]
            ),
            new MenuItem(
                'Отчёты', route('home'), 'icon-chart',
                [
                    'Статистика' => [ROUTE_DEFAULT, false, false],
                ]
            ),
        ];
    }

    public function getMenu(): array
    {
        return $this->menuList;
    }

}
