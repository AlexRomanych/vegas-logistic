<?php

namespace App\Http\Controllers\Api\V1\Cells\Blocks;

use App\Classes\EndPointStaticRequestAnswer;
use App\Http\Controllers\Controller;
use App\Http\Resources\Manufacture\Cells\Blocks\BlockCollectionResource;
use App\Models\Manufacture\Cells\Block\BlockCollection;
use Exception;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class BlockCollectionController extends Controller
{

    /**
     * ___ Возвращаем коллекцию блоков
     * @return AnonymousResourceCollection|string
     */
    public function getBlockCollections()
    {
        try {
            $blockCollections = BlockCollection::query()
                ->with(['blocks', 'kdbDoc'])
                ->get();
            return BlockCollectionResource::collection($blockCollections);
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }


    /**
     * ___ Получаем Коллекцию Блоков по id
     * @param string $id
     * @return BlockCollectionResource|string
     */
    public function getBlockCollectionById(string $id)
    {
        try {
            $validator = Validator::make(
                [
                    'id' => $id
                ],
                [
                    'id' => 'required|integer|exists:block_collections,id'
                ]
            );
            $validated = $validator->validate();

            $blockCollection = BlockCollection::query()
                ->with(['blocks'])
                ->findOrFail($validated['id']);

            return new BlockCollectionResource($blockCollection);
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }


    /**
     * ___ Создаем Коллекцию Блоков
     * @param Request $request
     * @return string
     */
    public function createBlockCollection(Request $request)
    {
        try {
            //$all = $request->all();

            $data = $request->validate([
                'name'         => 'required|unique:block_collections,name',
                'description'  => 'present|nullable|string',
                'active'       => 'required|boolean',
                'code_1c'      => 'required|string|size:9|unique:block_collections,code_1c',
                //'code_1c'      => 'required|digits:9',
                'unit'         => 'required|string|in:' . BlockCollection::UNIT_PIC . ',' . BlockCollection::UNIT_METERS,
                'kdb'          => 'present|nullable|string',
                'line'         => 'required|integer|in:' . BlockCollection::LINE_1 . ',' . BlockCollection::LINE_2,
                'line_alt'     => 'present|nullable|integer|in:' . BlockCollection::LINE_1 . ',' . BlockCollection::LINE_2 . ',' . BlockCollection::LINE_0,
                'priority'     => 'required|integer',
                'height'       => 'required|integer',
                'length'       => 'required|integer',
                'productivity' => 'required|numeric',
                'own'          => 'required|boolean',
            ]);

            $blockCollection = BlockCollection::query()->create([
                'code_1c'      => $data['code_1c'],
                'name'         => $data['name'],
                'active'       => $data['active'],
                'own'          => $data['own'],
                'unit'         => $data['unit'],
                'kdb'          => $data['kdb'],
                'line'         => $data['line'],
                'line_alt'     => $data['line_alt'] !== BlockCollection::LINE_0 ? $data['line_alt'] : null,
                'priority'     => $data['priority'],
                'height'       => $data['height'],
                'length'       => $data['length'],
                'productivity' => $data['productivity'],
                'description'  => $data['description'],
            ]);

            if (!$blockCollection) {
                throw new Exception('Error creating block collection');
            }

            return EndPointStaticRequestAnswer::ok('Сохранено');
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }

    /**
     * ___ Обновляем Коллекцию Блоков
     * @param Request $request
     * @return string
     */
    public function updateBlockCollection(Request $request)
    {
        try {
            //$all = $request->all();

            $data = $request->validate([
                'id'           => 'required|numeric|exists:block_collections,id',
                'name'         => 'required|string',
                'description'  => 'present|nullable|string',
                'active'       => 'required|boolean',
                'code_1c'      => 'required|string|size:9',
                //'code_1c'      => 'required|digits:9',
                'unit'         => 'required|string|in:' . BlockCollection::UNIT_PIC . ',' . BlockCollection::UNIT_METERS,
                'kdb'          => 'present|nullable|string',
                'line'         => 'required|integer|in:' . BlockCollection::LINE_1 . ',' . BlockCollection::LINE_2,
                'line_alt'     => 'present|nullable|integer|in:' . BlockCollection::LINE_1 . ',' . BlockCollection::LINE_2 . ',' . BlockCollection::LINE_0,
                'priority'     => 'required|integer',
                'height'       => 'required|integer',
                'length'       => 'required|integer',
                'productivity' => 'required|numeric',
                'own'          => 'required|boolean',
            ]);

            $blockCollection = BlockCollection::query()->findOrFail($data['id']);
            $updates         = $request->only([
                'name',
                'description',
                'active',
                'code_1c',
                'unit',
                'kdb',
                'line',
                'line_alt',
                'priority',
                'height',
                'length',
                'productivity',
                'own',
            ]);

            $blockCollection->update($updates);

            return EndPointStaticRequestAnswer::ok('Успешно обновлено');
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }


}
