<?php

namespace App\Models\Shared\Sellers;


use App\Contracts\Data1CHandlerContract;
use App\Models\Seller;
use Illuminate\Support\Facades\Storage;

class JSON1CDataHandler implements Data1CHandlerContract
{
    public function handler (string $filename = ''): void
    {
        $data = $this->getData($filename);
        $this->fillData($data);
    }

    public function getData(string $filename = ''): array
    {
        return  Storage::json(config('vegas.data_1C_folder') . '\\' . config('vegas.sellers_1C_json_name')) ?? [];
    }

    public function fillData(array $data = []): void
    {
        $sellersList = $this->getDataFromFile();

        foreach ($sellersList as $sellerItem) {

            $result = Seller::query()->updateOrCreate(
                ['code1C' => $sellerItem['sc']],
                ['full_name' => $sellerItem['nf'],
                 'short_name' => $sellerItem['ns'],
                 'country' => $sellerItem['c']]
            );

        }
    }
}
