<?php

namespace App\Actions;


use App\Contracts\VegasDataGetContract;
use App\Http\Controllers\ProcedureController;

final class UpdateProceduresTableAction
{

    /**
     * Заполняет всю информацию по поставщикам (поставщики + материалы поставщиков + отношения поставщиков и их материалов)
     * @param VegasDataGetContract $getter
     * @return void
     */
    public function update(VegasDataGetContract $getter): void
    {
        (new ProcedureController())->fillProceduresFrom1CReport($getter);                   // заполняем процедуры
    }
}
