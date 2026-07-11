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
use App\Services\LogicalService;
use App\Services\Manufacture\BlocksService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
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
                    'to'   => (int)$to,
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
            $all       = $request->all();
            $validated = $request->validate([
                // 1. Проверяем, что 'ids' присутствует, это массив и он не пустой
                'ids'   => 'required|array|min:1',

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
     *  ___ Получаем Оптимизированнй массив переналадки Коллекции Блоков
     * @param Request $request
     * @return false|string
     * @noinspection DuplicatedCode
     * @noinspection PhpUndefinedFieldInspection
     */
    public function getBlockCollectionsTuningTimeOptimized(Request $request)
    {
        try {
            //$all       = $request->all();
            $validated = $request->validate([
                // 1. Проверяем, что 'ids' присутствует, это массив и он не пустой
                'ids'   => 'required|array|min:1',

                // 2. Проверяем каждый элемент внутри массива 'ids.*'
                // Он должен быть целым числом и существовать в таблице 'block_collections' в колонке 'id'
                'ids.*' => 'integer|exists:block_collections,id',

                // 3. Проверяем start: он должен быть числом, минимум 0,
                // и входить в массив, состоящий из 0 и всех пришедших ids
                'start' => [
                    'required',
                    'integer',
                    'min:0',
                    'in:0,' . implode(',', $request->input('ids', [])),
                ],
            ]);

            // __ Если валидация прошла, получаем чистый массив проверенных ID
            $ids = $validated['ids'];

            // 1. Делаем выборку из пивот-таблицы (замени 'block_collection_pivot' на реальное имя таблицы)
            $pivots = DB::table('block_tuning_times')
                ->whereIn('collection_from', $ids)
                ->whereIn('collection_to', $ids)
                ->get(['collection_from', 'collection_to', 'tuning_time']);

            $getListKey = fn(int $index1, int $index2): string => $index1 . '-' . $index2;

            // Чтобы не перебирать коллекцию $pivots вложенными циклами (что превратит код в $O(N^2)),
            // мы превратим её в удобную карту, где ключом будет строка "из_id-в_id"
            $tuningMap = [];
            $errors    = [];   // Массив ошибок
            $minTime   = 0;    // Максимальное время переналадки (для сравнения)

            foreach ($pivots as $pivot) {
                $key = $getListKey($pivot->collection_from, $pivot->collection_to);
                //$key             = "{$pivot->collection_from}-{$pivot->collection_to}";
                $tuningMap[$key] = $pivot->tuning_time;
            }

            $matrix = [];
            foreach ($ids as $fromId) {
                $matrix[$fromId] = []; // Создаем строку для текущей коллекции

                foreach ($ids as $toId) {
                    $mapKey = $getListKey($fromId, $toId);
                    //$mapKey = "{$fromId}-{$toId}";
                    $matrix[$fromId][$toId] = 0;

                    if ($fromId !== $toId) {
                        if (isset($tuningMap[$mapKey])) {
                            $matrix[$fromId][$toId] = $tuningMap[$mapKey];
                            $minTime                += $tuningMap[$mapKey];
                        } else {
                            $matrix[$fromId][$toId] = 0;
                            $collectionFrom = BlocksService::getBlockCollectionById($fromId);
                            $collectionTo = BlocksService::getBlockCollectionById($toId);
                            $errors[]               = $collectionFrom->name . ' --> ' . $collectionTo->name;
                        }

                        // Если связь есть в базе — берем её значение, иначе — 0
                        //$matrix[$fromId][$toId] = $tuningMap[$mapKey] ?? 0;
                    }
                }
            }

            // __ Сами расчеты
            $tuningTimes = [];
            $startTime   = microtime(true);

            // Тут можно сделать алгоритм на 1 итерацию меньше, если не включать $lastRoll в $uniquePics
            $matrixKeys = array_keys($matrix);
            // $picKeys = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]; // Тестовый массив. 10 рисунков - 5 секунд. 11 - 55 сек.

            $startId = 0;           // Первый рисунок (переходящий рисунок)
            //$startPicId = $lastRoll ? $picture['id'] : 0; // Первый рисунок (переходящий рисунок)
            // $startPicId = $lastRoll ? $picture['id'] : $picKeys[0]; // Первый рисунок (переходящий рисунок)

            // __ Оставляем закомментированными для уменьшения мспользования памяти
            $permutations = [];     // Все перестановки рисунков
            // $permutations = $lastRoll ? [$picture['id']] : [];     // Все перестановки рисунков
            $minTimes = [];         // Время для каждой перестановки
            // $permutations = LogicalService::getPermutations($picKeys);

            $permutationMin = $matrixKeys;

            foreach (LogicalService::getPermutationsGenerator($matrixKeys) as $permutation) {
                // Для оптимизации нужно убрать эту строку
                if ($startId !== 0 && $permutation[0] !== $startId) {
                    continue;
                }  // Пропускаем перестановку, если задан начальный рисунок

                $permutationsTime = 0;

                for ($i = 0; $i < count($permutation) - 1; $i++) {
                    $permutationsTime += $matrix[$permutation[$i]][$permutation[$i + 1]];
                }

                // Тут могут быть и несколько перестановок с одинаковым временем
                if ($permutationsTime < $minTime) { // Первая найденная перестановка, $permutationsTime <= $minTime - последняя
                    $minTime        = $permutationsTime;
                    $permutationMin = $permutation;
                }

                // __ Оставляем закомментированными для уменьшения использования памяти
                // $permutations[] = $permutation;
                // $minTimes[] = $permutationsTime;
            }

            $endTime       = microtime(true);
            $executionTime = $endTime - $startTime;

            $outData['data'] = [
                'minTime'     => $minTime,
                'permutation' => $permutationMin,
                'errors'      => $errors,
            ];

            $statistic = true;
            if ($statistic) {
                $outData['statistic'] = [
                    'matrix'            => $matrix,
                    'uniqueCollections' => $ids,
                    'keys'              => $matrixKeys,
                    'times'             => $tuningTimes,
                    'executionTime'     => $executionTime . ', sec.',
                    'memory'            => memory_get_peak_usage(true) / 1024 / 1024 . ', MB',
                    // 'matrix' => $tuningTimesMatrix,
                    // 'minTimes' => $minTimes,            // Оставляем закомментированными для уменьшения использования памяти
                    // 'permutations' => $permutations,    // Оставляем закомментированными для уменьшения использования памяти
                ];
            }

            //return json_encode(['data' => $outData]);
            return json_encode($outData);
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
