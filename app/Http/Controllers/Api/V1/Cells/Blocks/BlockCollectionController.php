<?php

namespace App\Http\Controllers\Api\V1\Cells\Blocks;

use App\Classes\EndPointStaticRequestAnswer;
use App\Http\Controllers\Controller;
use App\Http\Resources\Manufacture\Cells\Blocks\BlockCollectionResource;
use App\Models\Manufacture\Cells\Block\BlockCollection;
use Exception;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

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
            ->with(['blocks'])
            ->get();
            return BlockCollectionResource::collection($blockCollections);
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }
}
