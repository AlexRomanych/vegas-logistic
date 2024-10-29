<?php

namespace App\Http\Controllers;

use App\Actions\UpdateClientsTableAction;
use App\Actions\UpdateGroupsAndCategoriesAndMaterialsTableAction;
use App\Actions\UpdateManagersTableAction;
use App\Actions\UpdateModelsAndCollectionsTableAction;
use App\Actions\UpdateProceduresTableAction;
use App\Contracts\VegasDataGetContract;
use App\Actions\UpdateSellersAndSellerMaterialsTableAction;

class UpdateData1CController extends Controller
{
    // Обновляем таблицы поставщиков + таблицу материалов поставщиков + таблицу отношений поставщиков и их материалов
    public function updateSellersAndSellerMaterials(UpdateSellersAndSellerMaterialsTableAction $action, VegasDataGetContract $getter)
    {
        $action->update($getter);
//        return view('welcome');
    }

    // Обновляем таблицы моделей + таблицу коллекций
    public function updateModelsAndCollections(UpdateModelsAndCollectionsTableAction $action, VegasDataGetContract $getter)
    {
        $action->update($getter);
    }

    // Обновляем таблицы групп, категорий и материалов
    public function updateGroupsAndCategoriesAndMaterials(UpdateGroupsAndCategoriesAndMaterialsTableAction $action, VegasDataGetContract $getter)
    {
        $action->update($getter);
    }

    // Обновляем таблицу процедур
    public function updateProcedures(UpdateProceduresTableAction $action, VegasDataGetContract $getter)
    {
        $action->update($getter);
    }

    // Обновляем таблицу менеджеров КС
    public function updateManagers(UpdateManagersTableAction $action, VegasDataGetContract $getter)
    {
        $action->update($getter);
    }

    // Обновляем таблицу клиентов
    public function updateClients(UpdateClientsTableAction $action, VegasDataGetContract $getter)
    {
        $action->update($getter);
    }

    // Обновляем все
    public function updateAll(VegasDataGetContract $getter,
                              UpdateSellersAndSellerMaterialsTableAction $sellerAction,
                              UpdateModelsAndCollectionsTableAction $modelAction,
                              UpdateGroupsAndCategoriesAndMaterialsTableAction $materialAction,
                              UpdateProceduresTableAction $procedureAction,
                              UpdateManagersTableAction $managerAction,
                              UpdateClientsTableAction $clientAction)
    {
        $this->updateModelsAndCollections($modelAction, $getter);
        $this->updateSellersAndSellerMaterials($sellerAction, $getter);
        $this->updateGroupsAndCategoriesAndMaterials($materialAction, $getter);
        $this->updateProcedures($procedureAction, $getter);
        $this->updateManagers($managerAction, $getter);
        $this->updateClients($clientAction, $getter);

        return view('welcome');
    }


}
