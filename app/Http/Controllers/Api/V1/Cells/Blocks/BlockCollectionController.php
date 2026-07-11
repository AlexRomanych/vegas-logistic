<?php

namespace App\Http\Controllers\Api\V1\Cells\Blocks;

use App\Classes\EndPointStaticRequestAnswer;
use App\Http\Controllers\Controller;
use App\Http\Requests\Manufacture\Blocks\TuningTime\DeleteTuningTimeRequest;
use App\Http\Requests\Manufacture\Blocks\TuningTime\StoreTuningTimeRequest;
use App\Http\Resources\Manufacture\Cells\Blocks\References\BlockCollectionResource;
use App\Http\Resources\Manufacture\Cells\Blocks\Tuning\BlockCollectionTuningTimeResource;
use App\Models\Manufacture\Cells\Block\BlockCollection;
use App\Models\Manufacture\Cells\Block\BlockTuningTime;
use Exception;
use Illuminate\Http\Request;
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
                'priority_2'   => 'required|integer',
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
                'priority_2'   => $data['priority_2'],
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
                'priority_2'   => 'required|integer',
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
                'priority_2',
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

    /**
     * ___ Получение времени переналадки рисунков между 2 рисунками
     * @param string $from
     * @param string $to
     * @return string
     */
    public static function getBlockBetweenTuningTime(string $from, string $to): string
    {
        try {

            $tuningTime = BlockTuningTime::query()
                ->where('collection_from', $from)
                ->where('collection_to', $to)
                ->first();

            $returnData = [
                'data' => [
                    'from' => (int)$from,
                    'to' => (int)$to,
                    'time' => '',
                ]
            ];

            if ($tuningTime) {
                $returnData['data']['time'] = $tuningTime->tuning_time;
            } else {
                $returnData['data']['time'] = null;
            }

            return json_encode($returnData);
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail(response()->json($e));
        }
    }

    /**
     * ___ Получаем время переналадки Коллекции Блоков
     * @param Request $request
     * @return AnonymousResourceCollection|string
     */
    public function getBlockCollectionsTuningTime(Request $request)
    {
        try {
            $tuningTime = BlockCollection::query()
                ->actual()
                ->with([
                    'collectionsTo',
                ])
                ->get();

            return BlockCollectionTuningTimeResource::collection($tuningTime);
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail(response()->json($e));
        }
    }

    /**
     * ___ Получаем время переналадки Коллекции Блоков
     * @param Request $request
     * @return AnonymousResourceCollection|string
     */
    public function getBlockCollectionsTuningTimeList(Request $request)
    {
        try {
            $all = $request->all();
            $validated = $request->validate([
                // 1. Проверяем, что 'ids' присутствует, это массив и он не пустой
                'ids' => 'required|array|min:1',

                // 2. Проверяем каждый элемент внутри массива 'ids.*'
                // Он должен быть целым числом и существовать в таблице 'block_collections' в колонке 'id'
                'ids.*' => 'integer|exists:block_collections,id',
            ]);

            // Если валидация прошла, получаем чистый массив проверенных ID
            $ids = $validated['ids'];

            $tuningTime = BlockCollection::query()
                ->actual()
                ->whereIn('id', $ids)
                ->with([
                    'collectionsTo',
                ])
                ->get();

            return BlockCollectionTuningTimeResource::collection($tuningTime);
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail(response()->json($e));
        }
    }

    /**
     * ___ Обновление/создание времени переналадки рисунков
     * @param StoreTuningTimeRequest $request
     * @return string
     */
    public function setBlockTuningTime(StoreTuningTimeRequest $request)
    {
        try {
            $tuningTime = BlockTuningTime::query()
                ->updateOrCreate([
                    'collection_from' => $request->input('from'),
                    'collection_to'   => $request->input('to'),
                ], [
                    'tuning_time' => $request->input('time'),
                ]);

            if (!$tuningTime) {
                throw new Exception('Tuning time not found');
            }

            return EndPointStaticRequestAnswer::ok('Успешно обновлено');
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail(response()->json($e));
        }
    }

    /**
     * ___ Удаление времени переналадки рисунков
     * @param DeleteTuningTimeRequest $request
     * @return string
     */
    public function deleteBlockTuningTime(DeleteTuningTimeRequest $request)
    {
        try {
            $tuningTime = BlockTuningTime::query()
                ->where('collection_from', $request->input('from'))
                ->where('collection_to', $request->input('to'))
                ->first();

            if ($tuningTime) {
                $tuningTime->delete();
            } /*else {
                throw new Exception('Tuning time not found');
            }*/

            return EndPointStaticRequestAnswer::ok('Успешно удалено');
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail(response()->json($e));
        }
    }

}
