<?php

namespace App\Actions;

use App\Contracts\VegasDataGetContract;
use App\Contracts\VegasDataUpdateContract;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

final class CreateSellerAndSellerMaterialRelationshipsTableAction implements VegasDataUpdateContract
{
    public function __construct(public VegasDataGetContract $getter)
    {
    }

    public function updateData(VegasDataGetContract $getter = null): void
    {
        $fileName = config('vegas.seller_seller_material_relationships_json_name');
        $relationships = !is_null($getter) ? $getter->getDataFromFile($fileName) : $this->getter->getDataFromFile($fileName);

        Schema::disableForeignKeyConstraints();

        DB::table('seller_seller_material')->truncate();

        foreach ($relationships as $relItem) {

            DB::table('seller_seller_material')->insert([

                'seller_code1C' => $relItem['sc'],
                'seller_material_code1C' => $relItem['mc'],

            ]);

        }

        Schema::enableForeignKeyConstraints();

    }
}
