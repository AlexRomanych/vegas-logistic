<?php

namespace App\Actions;


use App\Contracts\VegasDataGetContract;
use App\Http\Controllers\ClientController;

final class UpdateClientsTableAction
{
    /**
     * Заполняет всю информацию по клиентам
     * @param VegasDataGetContract $getter
     * @return void
     */
    public function update(VegasDataGetContract $getter): void
    {
        (new ClientController())->fillClientsFromEPSReport($getter);      // заполняем клиентов
    }
}
