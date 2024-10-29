<?php

namespace App\Actions;


use App\Contracts\VegasDataGetContract;
use App\Http\Controllers\SellerController;
use App\Http\Controllers\SellerMaterialController;

final class UpdateSellersAndSellerMaterialsTableAction
{

    /**
     * Заполняет всю информацию по поставщикам (поставщики + материалы поставщиков + отношения поставщиков и их материалов)
     * @param VegasDataGetContract $getter
     * @return void
     */
    public function update(VegasDataGetContract $getter): void
    {
        (new SellerController())->fillSellersFrom1CReport($getter);                             // заполняем поставщиков
        (new SellerMaterialController())->fillSellersMaterialsFrom1CReport($getter);            // заполняем материалы поставщиков
        (new CreateSellerAndSellerMaterialRelationshipsTableAction($getter))->updateData();     // заполняем отношения поставщиков <--> материалы поставщиков
    }
}
