<?php

namespace App\Models\Shared\Facades;

use App\Models\Procedure;

final class ProcedureFacade
{
    private array $procedures = [];

    /**
     * Кэшируем Процедуры
     * @return void
     */
    private function cacheProcedures(): void
    {
        if (count($this->procedures) === 0) {
            $procedures = Procedure::all();

            $proceduresByNameIndex = [];

            foreach ($procedures as $procedure) {
                $proceduresByNameIndex[strtolower($procedure->name)] = $procedure;
            }

            $this->procedures = $proceduresByNameIndex;
        }
    }
}
