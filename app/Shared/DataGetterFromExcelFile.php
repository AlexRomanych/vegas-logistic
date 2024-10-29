<?php

namespace App\Shared;


use App\Contracts\VegasDataGetContract;
use Illuminate\Support\Facades\Storage;

final class DataGetterFromExcelFile implements VegasDataGetContract
{

    public function getDataFromFile(string $fileName): array
    {
        return [];
    }
}
