<?php

namespace App\Actions;

use App\Contracts\VegasDataGetContract;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\MaterialController;

final class UpdateGroupsAndCategoriesAndMaterialsTableAction
{

    /**
     * Заполняет всю информацию по поставщикам (поставщики + материалы поставщиков + отношения поставщиков и их материалов)
     * @param VegasDataGetContract $getter
     * @return void
     */
    public function update(VegasDataGetContract $getter): void
    {
        // Важен порядок, потому, что есть внешний ключ
        (new GroupController())->fillGroupsFrom1CReport($getter);               // заполняем группы
        (new CategoryController())->fillCategoriesFrom1CReport($getter);        // заполняем категории
        (new MaterialController())->fillMaterialsFrom1CReport($getter);         // заполняем материалы
    }
}
