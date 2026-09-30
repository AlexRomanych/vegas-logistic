<?php

namespace App\Http\Controllers\Api\V1\Cells\Blocks;

use App\Classes\EndPointStaticRequestAnswer;
use App\Http\Controllers\Controller;
use App\Http\Resources\Manufacture\Cells\Blocks\References\BlockResource;
use App\Models\Manufacture\Cells\Block\Block;
use App\Services\Manufacture\BlocksService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Validator;

class BlockController extends Controller
{
    /**
     * ___ Получаем Список Блоков
     * @return AnonymousResourceCollection|string
     */
    public function getBlocks()
    {
        try {
            $blocks = Block::query()->get();

            return BlockResource::collection($blocks);
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }


    /**
     * ___ Получаем Блок по id
     * @param string $id
     * @return BlockResource|string
     */
    public function getBlockById(string $id)
    {
        try {
            $validator = Validator::make(
                [
                    'id' => $id
                ],
                [
                    'id' => 'required|integer|exists:blocks,id'
                ]
            );
            $validated = $validator->validate();

            $block = Block::query()->with(['substitutionBlock'])->findOrFail($validated['id']);

            return new BlockResource($block);
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }


    /**
     * ___ Создаем Блок
     * @param Request $request
     * @return string
     */
    public function createBlock(Request $request)
    {
        try {
            //$all = $request->all();

            $data = $request->validate([
                'name'                      => 'required|unique:blocks,name',
                'code_1c'                   => 'required|string|size:9|unique:blocks,code_1c',
                'collection'                => 'required|string|size:9|exists:block_collections,code_1c',
                'description'               => 'present|nullable|string',
                'active'                    => 'required|boolean',
                'width'                     => 'required|integer',
                'length'                    => 'required|integer',
                'shown'                     => 'required|boolean',

                // __ Валидация объекту substitution
                'substitution'              => 'nullable|array',
                'substitution.substitution' => 'required_with:substitution|boolean',
                'substitution.code_1c'      => 'required_with:substitution|string|size:9|exists:blocks,code_1c',
            ]);

            $block = Block::query()->create([
                'name'                       => $data['name'],
                'code_1c'                    => $data['code_1c'],
                'collection'                 => $data['collection'],
                'description'                => $data['description'],
                'active'                     => $data['active'],
                'width'                      => $data['width'],
                'length'                     => $data['length'],
                'shown'                      => $data['shown'],

                // __ Безопасно извлекаем значения подмены, если объект передан
                'substitution'               => $data['substitution']['substitution'] ?? false,
                'substitution_block_code_1c' => $data['substitution']['code_1c'] ?? null,
            ]);

            if (!$block) {
                throw new Exception('Error creating block spring');
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
    public function updateBlock(Request $request)
    {
        try {
            //$all = $request->all();

            $data = $request->validate([
                'id'                        => 'required|numeric|exists:blocks,id',
                'name'                      => 'required|string',
                'code_1c'                   => 'required|string|size:9',
                'collection'                => 'required|string|size:9|exists:block_collections,code_1c',
                'description'               => 'present|nullable|string',
                'active'                    => 'required|boolean',
                'width'                     => 'required|integer',
                'length'                    => 'required|integer',
                'shown'                     => 'required|boolean',

                // __ Валидация вложенного объекта substitution
                'substitution'              => 'nullable|array',
                'substitution.substitution' => 'required_with:substitution|boolean',
                'substitution.code_1c'      => 'required_with:substitution|string|size:9|exists:blocks,code_1c',
            ]);


            $block = Block::query()->findOrFail($data['id']);

            $block->update([
                'name'                       => $data['name'],
                'code_1c'                    => $data['code_1c'],
                'collection'                 => $data['collection'],
                'description'                => $data['description'],
                'active'                     => $data['active'],
                'width'                      => $data['width'],
                'length'                     => $data['length'],
                'shown'                      => $data['shown'],

                // __ Маппинг объекта substitution в плоские колонки таблицы
                'substitution'               => $data['substitution']['substitution'] ?? false,
                'substitution_block_code_1c' => $data['substitution']['code_1c'] ?? null,
                //'substitution_block_code_1c' => isset($data['substitution'])
                //    ? ($data['substitution']['code_1c'] ?? false)
                //    : $block->substitution_block_code_1c,
            ]);

            return EndPointStaticRequestAnswer::ok('Успешно обновлено');
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }


    public function test()
    {
        $result = BlocksService::test();


        return $result;
    }
}
