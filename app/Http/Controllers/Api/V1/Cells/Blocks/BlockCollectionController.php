<?php

namespace App\Http\Controllers\Api\V1\Cells\Blocks;

use App\Classes\EndPointStaticRequestAnswer;
use App\Http\Controllers\Controller;
use App\Http\Resources\Manufacture\Cells\Blocks\BlockCollectionResource;
use App\Models\Manufacture\Cells\Block\BlockCollection;
use Exception;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Validator;

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
}
