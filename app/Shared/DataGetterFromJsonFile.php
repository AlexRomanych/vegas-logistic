<?php

namespace App\Shared;


use App\Contracts\VegasDataGetContract;
use Illuminate\Support\Facades\Storage;

final class DataGetterFromJsonFile implements VegasDataGetContract
{
    /**
     * Получаем данные из файла json
     * @param string $fileName
     * @return array
     */
    public function getDataFromFile(string $fileName): array
    {
        return  Storage::json(config('vegas.data_1C_folder') . '\\' . $fileName . '.json') ?? [];
    }
}
