<?php

namespace App\Http\Controllers\Api\V1\Cells\Blocks;

use App\Classes\EndPointStaticRequestAnswer;
use App\Http\Controllers\Controller;
use App\Http\Resources\Manufacture\Cells\Blocks\BlockResource;
use App\Models\Manufacture\Cells\Block\Block;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class BlockController extends Controller
{
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

            $block = Block::query()->findOrFail($validated['id']);

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
                'name'        => 'required|unique:blocks,name',
                'code_1c'     => 'required|string|size:9|unique:blocks,code_1c',
                'collection'  => 'required|string|size:9|exists:block_collections,code_1c',
                'description' => 'present|nullable|string',
                'active'      => 'required|boolean',
                'width'       => 'required|integer',
                'length'      => 'required|integer',
            ]);

            $block = Block::query()->create([
                'name'        => $data['name'],
                'code_1c'     => $data['code_1c'],
                'collection'  => $data['collection'],
                'description' => $data['description'],
                'active'      => $data['active'],
                'width'       => $data['width'],
                'length'      => $data['length'],
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
                'id'          => 'required|numeric|exists:blocks,id',
                'name'        => 'required|string',
                'code_1c'     => 'required|string|size:9',
                'collection'  => 'required|string|size:9|exists:block_collections,code_1c',
                'description' => 'present|nullable|string',
                'active'      => 'required|boolean',
                'width'       => 'required|integer',
                'length'      => 'required|integer',
            ]);


            $block = Block::query()->findOrFail($data['id']);
            $updates         = $request->only([
                'id',
                'name',
                'code_1c',
                'collection',
                'description',
                'active',
                'width',
                'length',
            ]);

            $block->update($updates);

            return EndPointStaticRequestAnswer::ok('Успешно обновлено');
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }


}
