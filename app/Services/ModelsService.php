<?php

namespace App\Services;

use App\Contracts\VegasDataGetContract;
use App\Contracts\VegasDataUpdateContract;
use App\Models\Model;

final class ModelsService implements VegasDataUpdateContract
{
    public function __construct(public VegasDataGetContract $getter)
    {
    }

    public function updateData(VegasDataGetContract $getter = null): void
    {
        $fileName = config('vegas.models_1C_json_name');
        $modelsList = !is_null($getter) ? $getter->getDataFromFile($fileName) : $this->getter->getDataFromFile($fileName);

        Model::query()->update(['active' => 0]);

        foreach ($modelsList as $modelItem) {
            Model::query()->updateOrCreate(
                [
                    'code1C' => $modelItem['cd']
                ],
                [
                    'type' => $modelItem['tp'],
                    'serial' => $modelItem['sl'],
                    'name' => $modelItem['nr'],
                    'name_1C' => $modelItem['nm'],
                    'base_height' => $modelItem['bh'],
                    'cover_height' => $modelItem['ch'],
                    'textile' => $modelItem['tl'],
                    'textile_combination' => $modelItem['tc'],
                    'cover_type' => $modelItem['ct'],
                    'zipper' => $modelItem['zp'],
                    'spacer' => $modelItem['sr'],
                    'stitch_pattern' => $modelItem['sp'],
                    'pack_type' => $modelItem['pk'],
                    'base_composition' => $modelItem['bn'],
                    'side_foam' => $modelItem['sf'],
                    'base_block' => $modelItem['bb'],
                    'load' => $modelItem['ld'],
                    'guarantee' => $modelItem['gt'],
                    'life' => $modelItem['lf'],
                    'cover_mark' => $modelItem['cm'],
                    'model_mark' => $modelItem['bm'],
                    'group' => $modelItem['gn'],
                    'owner' => $modelItem['or'],
                    'line' => $modelItem['ln'],
                    'sewing_machine' => $modelItem['sm'],
                    'pack_density' => $modelItem['dp'],
                    'side_height' => $modelItem['sh'],
                    'pack_weight_rb' => $modelItem['wr'],
                    'pack_weight_ex' => $modelItem['we'],
                    'manufacture_type' => $modelItem['mt'],
                    'weight' => $modelItem['wg'],
                    'barcode' => $modelItem['bc'],
                    'collection_id' => $modelItem['ci'],
                    'base' => json_encode($modelItem['bs'], JSON_UNESCAPED_UNICODE),
                    'cover' => json_encode($modelItem['cv'], JSON_UNESCAPED_UNICODE),
                    'active' => 1,
                ]
            );
        }

        $modelItem = $this->createFakeModel();
        Model::query()->updateOrCreate(['code1C' => $modelItem->code1C], $modelItem->getAttributes());

        $modelItem = $this->createFakeModel([
            'code1C'  => '000000001',
            'name'    => 'Наматрасник',
            'owner'   => 'Наматрасники /с наполнителем/',
            'type'    => 'Наматрасник модифицирующий',
            'serial'  => 'НП',
        ]);
        Model::query()->updateOrCreate(['code1C' => $modelItem->code1C], $modelItem->getAttributes());

        $modelItem = $this->createFakeModel([
            'code1C'  => '000000002',
            'name'    => 'Чехол',
            'type'    => 'Чехол',
            'owner'   => 'Чехол',
            'serial'  => '',
        ]);
        Model::query()->updateOrCreate(['code1C' => $modelItem->code1C], $modelItem->getAttributes());

        $modelItem = $this->createFakeModel([
            'code1C'  => '000000003',
            'name'    => Model::AVERAGE_MODEL_NAME,
            'type'    => 'Статистическая модель',
            'owner'   => 'Статистическая модель',
            'serial'  => '',
        ]);
        Model::query()->updateOrCreate(['code1C' => $modelItem->code1C], $modelItem->getAttributes());

    }

    // Возвращаем фейковую модель
    public function createFakeModel(array $attributes = []): Model
    {
        $model = new Model();
        $modelAttributes = $model->getAttributes();
        $modelAttributes = array_merge($modelAttributes, $attributes);

        if ($modelAttributes['name'] !== 'Чехол') {
            $modelAttributes['name_1C'] = $modelAttributes['serial'] . '.' . $modelAttributes['name'] . '.';     // составной атрибут
        } else {
            $modelAttributes['name_1C'] = 'Чехол';
        }
        return new Model($modelAttributes);
    }

}
