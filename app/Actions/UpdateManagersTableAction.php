<?php

namespace App\Actions;


use App\Contracts\VegasDataGetContract;
use App\Http\Controllers\ManagerController;

final class UpdateManagersTableAction
{
    /**
     * Заполняет всю информацию по менеджерам КС
     * @param VegasDataGetContract $getter
     * @return void
     */
    public function update(VegasDataGetContract $getter): void
    {
        (new ManagerController())->fillManagersFromEPSReport($getter);      // заполняем менеджеров КС
    }
}
