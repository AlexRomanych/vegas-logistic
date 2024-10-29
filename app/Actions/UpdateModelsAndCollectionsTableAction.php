<?php

namespace App\Actions;


use App\Contracts\VegasDataGetContract;
use App\Http\Controllers\CollectionController;
use App\Http\Controllers\ModelController;

final class UpdateModelsAndCollectionsTableAction
{

    /**
     * Заполняет всю информацию по поставщикам (поставщики + материалы поставщиков + отношения поставщиков и их материалов)
     * @param VegasDataGetContract $getter
     * @return void
     */
    public function update(VegasDataGetContract $getter): void
    {
        // Важен порядок, потому, что есть внешний ключ
        (new CollectionController())->fillCollectionsFrom1CReport($getter);            // заполняем коллекции
        (new ModelController())->fillModelsFrom1CReport($getter);                      // заполняем модели
    }
}
