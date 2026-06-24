<?php

namespace App\Http\Controllers\Api\V1\Cells\Cutting;

use App\Classes\EndPointStaticRequestAnswer;
use App\Http\Controllers\Controller;
use App\Http\Requests\Manufacture\Cutting\Sync\SyncCuttingTasksRequest;
use App\Http\Resources\Manufacture\Cells\Cutting\CuttingTaskExecute\CuttingTaskLineResource;
use App\Http\Resources\Manufacture\Cells\Cutting\CuttingTaskManage\CuttingTaskResource;
use App\Models\Manufacture\Cells\Cutting\CuttingDay;
use App\Models\Manufacture\Cells\Cutting\CuttingTask;
use App\Models\Manufacture\Cells\Cutting\CuttingTaskLine;
use App\Models\Manufacture\Cells\Cutting\CuttingTaskStatus;
use App\Services\DefaultsService;
use App\Services\Manufacture\CuttingService;
use App\Services\ModelsService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;

// use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Throwable;

class CellCuttingTaskController extends Controller
{

    // ___ Получаем СЗ на Раскрой
    public function getCuttingTasks(Request $request)
    {
        try {
            $validated = $request->validate([
                'period'       => 'nullable|array',
                'period.start' => 'required_if:period,*,!null|date',        // условная валидация
                'period.end'   => 'required_if:period,*,!null|date',
            ]);

            if (isset($validated['period'])) {
                $start = Carbon::parse($validated['period']['start']);
                $end   = Carbon::parse($validated['period']['end']);
            } else {
                $period = DefaultsService::getDefaultPeriodPlanLoads();
                $start  = Carbon::parse($period->getStart());
                $end    = Carbon::parse($period->getEnd());
            }

            $cuttingTasks = CuttingTask::query()
                ->whereBetween('action_at', [
                    $start->startOfDay(),
                    $end->endOfDay()
                ])
                // ->whereDate('action_at', '>=', $start)     // Используем такую конструкцию, потому что
                // ->whereDate('action_at', '<=', $end)       // ->whereBetween() не включает периоды
                //->with([
                //    'order.client', // Оптимизация: берем только нужные поля
                //    'order.orderType',
                //    'statuses',
                //
                //    // Основные строки раскроя
                //    'cuttingLines.orderLine.model' => function($query) {
                //        // Загружаем модель только с необходимыми для вывода полями
                //        $query->with(['cover', 'base']);
                //    },
                //
                //    // Детали (боковины) — грузим только саму модель без ухода в рекурсию чехлов
                //    'cuttingLines.details.orderLine.model'
                //])

                //->with([
                //    'order.client:id,name',
                //    'order.orderType:id,name',
                //    'statuses',
                //
                //    // 1. Основные строки задания
                //    'cuttingLines' => function($query) {
                //        $query->orderBy('position');
                //    },
                //    'cuttingLines.orderLine.model' => function($query) {
                //        // Грузим модель со всеми нужными связями,
                //        // но БЕЗ ухода в бесконечные HasOne/BelongsTo
                //        $query->with(['cover', 'base']);
                //    },
                //
                //    // 2. Оптимизация деталей (боковин)
                //    // Для боковин НЕ подгружаем .cover и .base, так как у них
                //    // либо нет чехла, либо именно здесь Eloquent ломается на рекурсии
                //    'cuttingLines.details' => function($query) {
                //        $query->orderBy('position');
                //    },
                //    'cuttingLines.details.orderLine.model'
                //])

                ->with([
                    'order',
                    'order.client',
                    'order.orderType',
                    'statuses',
                    // Главные строки задания
                    'cuttingLines.orderLine',
                    'cuttingLines.orderLine.model',
                    'cuttingLines.orderLine.model.kdchDoc',
                    'cuttingLines.orderLine.model.cover.kdchDoc',
                    'cuttingLines.orderLine.model.base.kdchDoc',
                    //'cuttingLines.orderLine.model.constructs', // <-- Сюда
                    // !! ОПТИМИЗАЦИЯ: Глубокая жадная загрузка для вложенных деталей (боковин)

                    // !!! Убираем эту привязку. Изначально все разбиваем на детали
                    'cuttingLines.details.orderLine.model',
                    'cuttingLines.details.orderLine.model.cover.kdchDoc',
                    'cuttingLines.details.orderLine.model.base.kdchDoc',

                    //'cuttingLines.details.orderLine.model.constructs', // <-- И сюда
                ])




                //->with([
                //    'order.client',
                //    'order.orderType',
                //    'statuses',
                //    'cuttingLines.orderLine.model.cover',
                //    'cuttingLines.orderLine.model.base',
                //    'cuttingLines.details', // __ Детальки (боковины)
                //
                //])
                // ->with(['cuttingLines', 'cuttingLines.orderLine','cuttingLines.orderLine.model','cuttingLines.orderLine.model.cover', 'statuses'])
                ->orderBy('action_at')
                ->get();


            // !!!!!!!!!!!!!!!!!!!!!
            // !!! __ TODO: Тут, если есть не выполенные задания за предыдущие дни,
            // !!! __ То автоматом переносить на следующий день
            // !!! __ Отдельная функция - Реализовано через Middleware
            // !!!!!!!!!!!!!!!!!!!!!


            // Собираем коды с основных строк
            $mainCodes = $cuttingTasks->flatMap(function ($task) {
                return $task->cuttingLines->map(fn($line) => $line->orderLine?->model_code_1c);
            });

            // Собираем коды из вложенных деталей (боковин)
            $detailCodes = $cuttingTasks->flatMap(function ($task) {
                return $task->cuttingLines->flatMap(function ($line) {
                    return $line->details->map(fn($detail) => $detail->orderLine?->model_code_1c);
                });
            });

            // Объединяем, убираем дубликаты, null, пустые строки и приводя к массиву строк
            $allCodes = collect([])
                ->concat($mainCodes)
                ->concat($detailCodes)
                ->map(fn($code) => trim((string)$code))
                ->filter(fn($code) => $code !== '')
                ->unique()
                ->values()
                ->toArray();


            // 4. Прогреваем статический кэш сервиса перед передачей в ресурс
            ModelsService::warmUpCacheByCodes($allCodes);

            return CuttingTaskResource::collection($cuttingTasks);
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }


    // ___ Получаем СЗ на Раскрой

    /** @noinspection DuplicatedCode */
    public function getCuttingTasksByOrderId(string $orderId)
    {
        try {
            $validated = Validator::make([
                'id' => $orderId
            ], [
                'id' => 'required|exists:orders,id'
            ])
                ->validate();

            $cuttingTasks = CuttingTask::query()
                ->where('order_id', $validated['id'])
                ->with([
                    // 'order.client',
                    // 'order.orderType',
                    'cuttingLines.orderLine.model',
                    'cuttingLines.orderLine.model.kdchDoc',
                    'cuttingLines.orderLine.model.modelType',
                    'cuttingLines.orderLine.model.cover',
                    'cuttingLines.orderLine.model.base',
                    'statuses',
                ])
                // ->with(['cuttingLines', 'cuttingLines.orderLine','cuttingLines.orderLine.model','cuttingLines.orderLine.model.cover', 'statuses'])
                ->orderBy('action_at')
                ->get();


            // 2. Красиво собираем коды через pluck() в одну строку без лишних вложенных циклов
            $allCodes = $cuttingTasks->pluck('cuttingLines.*.orderLine.model_code_1c')
                ->flatten()
                ->map(fn($code) => trim((string)$code))
                ->filter() // Автоматически уберет null, false и пустые строки
                ->unique()
                ->values()
                ->toArray();

            // 3. Прогреваем кэш сервиса только по основным кодам
            ModelsService::warmUpCacheByCodes($allCodes);


            // 4. Прогреваем статический кэш сервиса перед передачей в ресурс
            ModelsService::warmUpCacheByCodes($allCodes);

            // !!!!!!!!!!!!!!!!!!!!!
            // !!! __ TODO: Тут, если есть не выполенные задания за предыдущие дни,
            // !!! __ То автоматом переносить на следующий день
            // !!! __ Отдельная функция
            // !!!!!!!!!!!!!!!!!!!!!


            return CuttingTaskResource::collection($cuttingTasks);
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }

    // ___ Добавляем СЗ на Раскрой
    public function addCuttingTasksByOrderId(Request $request)
    {
        try {
            $validated = $request->validate([
                'id' => 'required|exists:orders,id'
            ]);

            CuttingService::createCuttingTaskFromOrderId($validated['id']);

            return EndPointStaticRequestAnswer::ok('СЗ успешно создано');
        } catch (Exception|Throwable $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }


    // ___ Удаляем СЗ на Раскрой
    public function deleteCuttingTasksByOrderId(Request $request)
    {
        try {
            $validated = $request->validate([
                'id' => 'required|exists:orders,id'
            ]);

            DB::transaction(function () use ($validated) {
                // __ Меняем позиции СЗ в днях, где удаляем
                $deletedTasks = CuttingTask::query()
                    ->select(['id', 'action_at'])
                    ->where('order_id', $validated['id'])
                    ->get();

                // __ Удаляем здесь
                CuttingTask::query()
                    ->where('order_id', $validated['id'])
                    ->delete();

                //$deletedTasksArray = $deletedTasks->toArray();
                //$a = 0;

                // $tasksToUpdate[] = [
                //     'id'        => taskId,
                //     'action_at' => new_action_at ?? null,
                //     'position'  => new_position ?? null,
                // ];

                foreach ($deletedTasks as $deletedTask) {
                    $tasksToUpdate = [];
                    $pos           = 1;
                    $existTasks    = CuttingTask::query()
                        ->select(['id', 'action_at', 'position'])
                        ->where('action_at', '>=', $deletedTask->action_at->startOfDay())
                        ->where('action_at', '<=', $deletedTask->action_at->endOfDay())
                        ->orderBy('position')
                        ->get();

                    foreach ($existTasks as $existTask) {
                        $tasksToUpdate[] = [
                            'id'        => $existTask->id,
                            'action_at' => null,
                            'position'  => $pos++,
                        ];
                    }

                    CuttingService::bulkUpdateTasks($tasksToUpdate);
                }
            });

            return EndPointStaticRequestAnswer::ok('СЗ успешно удалено');
        } catch (Exception|Throwable $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }


    /**
     * ___ Получаем СЗ на Раскрой по статусам
     * @param Request $request
     * @return AnonymousResourceCollection|string
     */
    public function getCuttingTasksByStatus(Request $request)
    {
        try {
            $validated = $request->validate([
                // __ Проверяем, что 'statuses' — это массив
                'statuses'   => 'nullable|array',
                // __ Проверяем каждый элемент массива: должен быть числом и существовать в БД
                'statuses.*' => 'integer|exists:cutting_task_statuses,id',
            ]);

            $data         = $validated['statuses'] ?? null;
            $cuttingTasks = CuttingTask::query()
                ->byStatus($data)
                // ->whereBetween('action_at', [
                //     $start->startOfDay(),
                //     $end->endOfDay()
                // ])
                // ->whereDate('action_at', '>=', $start)     // Используем такую конструкцию, потому что
                // ->whereDate('action_at', '<=', $end)       // ->whereBetween() не включает периоды
                ->with([
                    'order.client',
                    'order.orderType',
                    'cuttingLines.orderLine.model.cover',
                    'cuttingLines.orderLine.model.base',
                    'statuses',
                ])
                ->orderBy('action_at')
                ->get();


            // !!!!!!!!!!!!!!!!!!!!!
            // !!! __ TODO: Тут, если есть не выполенные задания за предыдущие дни,
            // !!! __ То автоматом переносить на следующий день
            // !!! __ Отдельная функция
            // !!!!!!!!!!!!!!!!!!!!!


            return CuttingTaskResource::collection($cuttingTasks);
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }


    /**
     * ___ Получаем СЗ на Раскрой по статусам и периоду
     * @param Request $request
     * @return AnonymousResourceCollection|string
     */
    public function getCuttingTasksByStatusAndPeriod(Request $request)
    {
        try {
            //$all = $request->all();
            $validated = $request->validate([
                // __ Проверяем, что 'statuses' — это массив
                'statuses'     => 'nullable|array',
                // __ Проверяем каждый элемент массива: должен быть числом и существовать в БД
                'statuses.*'   => 'integer|exists:cutting_task_statuses,id',
                //'status'       => 'nullable|numeric|in:1,2,3,4,5',
                'period'       => 'nullable|array',
                'period.start' => 'required_if:period,*,!null|date',        // условная валидация
                'period.end'   => 'required_if:period,*,!null|date',
            ]);

            if (isset($validated['period'])) {
                $start = Carbon::parse($validated['period']['start']);
                $end   = Carbon::parse($validated['period']['end']);
            } else {
                $period = DefaultsService::getDefaultPeriodCuttingTaskArchive();
                $start  = Carbon::parse($period->getStart());
                $end    = Carbon::parse($period->getEnd());
            }

            $data         = $validated['statuses'] ?? null;
            $cuttingTasks = CuttingTask::query()
                ->byStatus($data)
                ->whereDate('action_at', '>=', $start)     // Используем такую конструкцию, потому что
                ->whereDate('action_at', '<=', $end)      // ->whereBetween() не включает периоды
                                                          // ->whereBetween('action_at', [
                                                          //     $start->startOfDay(),
                                                          //     $end->endOfDay()
                                                          // ])
                ->with([
                    'order.client',
                    'order.orderType',
                    'cuttingLines.orderLine.model.cover',
                    'cuttingLines.orderLine.model.base',
                    'statuses',
                ])
                ->orderBy('action_at')
                ->get();


            // !!!!!!!!!!!!!!!!!!!!!
            // !!! __ TODO: Тут, если есть не выполенные задания за предыдущие дни,
            // !!! __ То автоматом переносить на следующий день
            // !!! __ Отдельная функция
            // !!!!!!!!!!!!!!!!!!!!!


            return CuttingTaskResource::collection($cuttingTasks);
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }


    /**
     * __ Получаем СЗ на Раскрой по статусам до определенной даты
     * @param Request $request
     * @return AnonymousResourceCollection|JsonResponse
     */
    public function getCuttingTasksByStatusBeforeDate(Request $request)
    {
        try {
            $validated = $request->validate([
                // __ Проверяем, что 'date' — это дата
                'date'       => 'required|date_format:Y-m-d',
                // __ Проверяем, что 'statuses' — это массив
                'statuses'   => 'nullable|array',
                // __ Проверяем каждый элемент массива: должен быть числом и существовать в БД
                'statuses.*' => 'integer|exists:cutting_task_statuses,id',
            ]);

            $data        = $validated['statuses'] ?? null;
            $action_date = Carbon::parse($validated['date'])->startOfDay();

            $cuttingTasks = CuttingTask::query()
                ->whereDate('action_at', '<', $action_date)
                ->byStatus($data)
                // ->whereBetween('action_at', [
                //     $start->startOfDay(),
                //     $end->endOfDay()
                // ])
                ->with([
                    'order.client',
                    'order.orderType',
                    'cuttingLines.orderLine.model.cover',
                    'cuttingLines.orderLine.model.base',
                    'statuses',
                ])
                ->orderBy('action_at')
                ->get();


            // !!!!!!!!!!!!!!!!!!!!!
            // !!! __ TODO: Тут, если есть не выполенные задания за предыдущие дни,
            // !!! __ То автоматом переносить на следующий день
            // !!! __ Отдельная функция
            // !!!!!!!!!!!!!!!!!!!!!


            return CuttingTaskResource::collection($cuttingTasks);
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }


    /**
     * __ Получаем СЗ на Раскрой по статусам в определенную дату
     * @param Request $request
     * @return AnonymousResourceCollection|JsonResponse
     */
    public function getCuttingTasksByStatusOnDate(Request $request)
    {
        try {
            $validated = $request->validate([
                // __ Проверяем, что 'date' — это дата
                'date'       => 'required|date_format:Y-m-d',
                // __ Проверяем, что 'statuses' — это массив
                'statuses'   => 'nullable|array',
                // __ Проверяем каждый элемент массива: должен быть числом и существовать в БД
                'statuses.*' => 'integer|exists:cutting_task_statuses,id',
            ]);

            $data        = $validated['statuses'] ?? null;
            $action_date = Carbon::parse($validated['date'])->startOfDay();

            $cuttingTasks = CuttingTask::query()
                ->whereDate('action_at', $action_date)
                ->byStatus($data)
                // ->whereBetween('action_at', [
                //     $start->startOfDay(),
                //     $end->endOfDay()
                // ])
                ->with([
                    'order.client',
                    'order.orderType',
                    'cuttingLines.orderLine.model.cover',
                    'cuttingLines.orderLine.model.base',
                    'statuses',
                ])
                ->orderBy('action_at')
                ->get();


            // !!!!!!!!!!!!!!!!!!!!!
            // !!! __ TODO: Тут, если есть не выполенные задания за предыдущие дни,
            // !!! __ То автоматом переносить на следующий день
            // !!! __ Отдельная функция
            // !!!!!!!!!!!!!!!!!!!!!


            return CuttingTaskResource::collection($cuttingTasks);
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }


    /**
     * __ Проверяем наличие СЗ на Раскрой по статусам в определенную дату
     * @param Request $request
     * @return bool[]|string
     */
    public function checkCuttingTasksByStatusOnDate(Request $request)
    {
        try {
            $validated = $request->validate([
                // __ Проверяем, что 'date' — это дата
                'date'       => 'required|date_format:Y-m-d',
                // __ Проверяем, что 'statuses' — это массив
                'statuses'   => 'nullable|array',
                // __ Проверяем каждый элемент массива: должен быть числом и существовать в БД
                'statuses.*' => 'integer|exists:cutting_task_statuses,id',
            ]);

            $data        = $validated['statuses'] ?? null;
            $action_date = Carbon::parse($validated['date'])->startOfDay();

            $cuttingTasks = CuttingTask::query()
                ->whereDate('action_at', $action_date)
                ->byStatus($data)
                ->get();

            // !!!!!!!!!!!!!!!!!!!!!
            // !!! __ TODO: Тут, если есть не выполенные задания за предыдущие дни,
            // !!! __ То автоматом переносить на следующий день
            // !!! __ Отдельная функция
            // !!!!!!!!!!!!!!!!!!!!!

            return ['data' => !$cuttingTasks->isEmpty()];
            //return CuttingTaskResource::collection($cuttingTasks);
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }


    // TODO: Union into Textile!!!

    /**
     * ___ Обновляем СЗ на Раскрой
     * @param SyncCuttingTasksRequest $request
     * @return mixed|string
     */
    public function updateCuttingTasks(SyncCuttingTasksRequest $request)
    {
        // !!! TODO: SyncCuttingTasksRequest
        try {
            $idMap = []; // __ Для возврата соответствия temp_id => real_id

            return DB::transaction(function () use ($request, &$idMap) {
                $tasksToUpdate = [];

                // __ Сортируем именно в таком порядке, удаляем в самом конце
                $diffs = $request->validated()['diffs'];
                usort($diffs, function ($a, $b) {
                    // Назначаем приоритеты: чем меньше число, тем выше элемент в списке
                    $priorities = fn($type) => match ($type) {
                        'ADDED'   => 1,
                        'UPDATED' => 2,
                        'DELETED' => 3,
                        default   => 4,
                    };

                    return $priorities($a['type']) <=> $priorities($b['type']);
                });


                foreach ($diffs as $diff) {
                    $currentTaskId = null;  // __ Маяк созданного СЗ (Если флаг в lines - ADDED и в tasks - ADDED)
                    $updatedTaskId = null;  // __ Маяк обновляемого СЗ (Если флаг в lines - ADDED, но эти lines приходят из DELETED task и в tasks - UPDATED)

                    // --- 1. ОБРАБОТКА ЗАДАЧИ ---

                    switch ($diff['type']) {
                        case 'ADDED':

                            // __ Создаем новую задачу
                            $newTask = CuttingTask::query()
                                ->findOrFail($diff['taskIdRef'])
                                ->replicate();

                            // __ Устанавливаем позицию в отрицательную зону
                            // __ танцы "с бубнами" из-за ограничения пар ключей - устанавливаем уникальную позицию
                            $newTask->position *= -1;
                            $newTask->save();
                            $newTask->position = $newTask->id * (-1);
                            $newTask->save();

                            // __ Создаем запись в Статусе: Создано
                            $newTask->statuses()->attach([
                                CuttingTaskStatus::CUTTING_STATUS_CREATED_ID => [
                                    'set_at'     => now(),
                                    'created_by' => auth()->id(),
                                ]
                            ]);

                            $tasksToUpdate[] = [
                                'id'        => $newTask->id,
                                'action_at' => $diff['taskChanges']['action_at']['new'] ?? null,
                                'position'  => $diff['taskChanges']['position']['new'] ?? null,
                            ];

                            $idMap[$diff['taskId']] = $newTask->id;
                            $currentTaskId          = $newTask->id;

                            break;

                        case 'UPDATED':

                            // $task = CuttingTask::query()->findOrFail($diff['taskId']);

                            if (!empty($diff['taskChanges'])) {
                                $tasksToUpdate[] = [
                                    'id'        => $diff['taskId'],
                                    'action_at' => $diff['taskChanges']['action_at']['new'] ?? null,
                                    'position'  => $diff['taskChanges']['position']['new'] ?? null,
                                ];
                            }

                            $currentTaskId = $diff['taskId'];
                            // $updatedTaskId = $diff['taskId'];
                            break;

                        case 'DELETED':
                            CuttingTask::destroy($diff['taskId']);
                            break;
                    }


                    // --- 2. ОБРАБОТКА СТРОК (LINES) ---
                    if (!empty($diff['lineChanges'])) {
                        $linesToUpdate = [];

                        // __ Сортируем именно в таком порядке, удаляем в самом конце
                        $lineDiffs = $diff['lineChanges'];
                        usort($diffs, function ($a, $b) {
                            // __ Назначаем приоритеты: чем меньше число, тем выше элемент в списке
                            $priorities = fn($type) => match ($type) {
                                'ADDED'   => 1,
                                'UPDATED' => 2,
                                'DELETED' => 3,
                                default   => 4,
                            };

                            return $priorities($a['type']) <=> $priorities($b['type']);
                        });


                        foreach ($lineDiffs as $lineDiff) {
                            switch ($lineDiff['type']) {
                                case 'ADDED':

                                    $newLine = CuttingTaskLine::query()
                                        ->findOrFail($lineDiff['lineIdRef'])
                                        ->replicate();

                                    // __ Ситуация, когда новые строки появились в новом СЗ
                                    // __ Связываем с новым СЗ
                                    if ($currentTaskId) {
                                        $newLine->cutting_task_id = $currentTaskId;
                                    }

                                    // __ Устанавливаем позицию в отрицательную зону
                                    // __ танцы "с бубнами" из-за ограничения пар ключей - устанавливаем уникальную позицию
                                    $newLine->position *= -1;
                                    $newLine->save();
                                    $newLine->position = $newLine->id * (-1);
                                    $newLine->save();

                                    $linesToUpdate[] = [
                                        'id'       => $newLine->id,
                                        'amount'   => $lineDiff['amount']['new'] ?? null,
                                        'position' => $lineDiff['position']['new'] ?? null,
                                    ];

                                    $idMap[$lineDiff['lineId']] = $newLine->id;
                                    break;

                                case 'UPDATED':

                                    $linesToUpdate[] = [
                                        'id'       => $lineDiff['lineId'],
                                        'amount'   => $lineDiff['amount']['new'] ?? null,
                                        'position' => $lineDiff['position']['new'] ?? null,
                                        // 'cutting_task_id' => $updatedTaskId ?? null,
                                    ];
                                    break;

                                case 'DELETED':
                                    CuttingTaskLine::destroy($lineDiff['lineId']);
                                    break;
                            }
                        }
                    }

                    // __ Выполняем массовое обновление строк, если они есть
                    if (!empty($linesToUpdate)) {
                        $this->bulkUpdateLines($linesToUpdate);
                    }
                }

                // __ Выполняем массовое обновление СЗ, если они есть
                if (!empty($tasksToUpdate)) {
                    $this->bulkUpdateTasks($tasksToUpdate);
                }

                // __ Смотрим, если еще прилетел статус, который нужно установить для СЗ,
                // __ то устанавливаем его
                foreach ($diffs as $diff) {
                    if (isset($diff['taskChanges']) && /*!is_null($diff['taskChanges']) &&*/ !is_null($diff['taskChanges']['status'])) {
                        // __ Пропускаем тот случай, кагда с фронта прилетает создание нового СЗ (ADDED)
                        // __ Это обрабатываем выше
                        if ($diff['taskId'] !== 0) {
                            // __ Меняем статус только в случае, если нужно установить статус "Выполняется"
                            // __ Случай, когда перетасктваем СЗ в день, гже есть статус "Выполняется"
                            // __ Остальное не трогаем, потому что начинаются проблемы, такой костыль получился
                            if ($diff['taskChanges']['status']['new'] === CuttingTaskStatus::CUTTING_STATUS_RUNNING_ID) {
                                $setTask = CuttingTask::query()->findOrFail($diff['taskId']);
                                $setTask->statuses()->attach([
                                    $diff['taskChanges']['status']['new'] => [
                                        'set_at'     => Carbon::now(),
                                        'created_by' => auth()->id(),
                                    ]
                                ]);
                            }
                        }
                    }
                }

                return EndPointStaticRequestAnswer::ok();

                // TODO: Разобраться с ответом
                // return response()->json([
                //     'status' => 'success',
                //     'idMap'  => $idMap
                // ]);
            });
        } catch (Throwable $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }

    /**
     * ___ Массовое обновление СЗ
     * @param array $rows
     * @return void
     * @throws Throwable
     */
    private function bulkUpdateTasks(array $rows)
    {
        // __ Получаем имя таблицы
        $table = (new CuttingTask)->getTable();

        // __ 1. Находим только те ID, у которых действительно меняется позиция (чтобы не уводить в минус лишнее)
        $idsForMinus = array_column(array_filter($rows, fn($r) => isset($r['position'])), 'id');

        // __ 2. Находим все ID, которые участвуют в обновлении (хоть позиция, хоть amount)
        $allIds = array_column($rows, 'id');

        DB::transaction(function () use ($table, $rows, $idsForMinus, $allIds) {
            // Warning!!! Не работает. Уводит position в минус.
            // !!! В чем проблема сейчас?
            // !!! Твой «ШАГ 1» уводит записи в минус на текущей дате (action_at). Если на новой дате, куда ты хочешь перенести задачу,
            // !!!уже есть запись с такой же позицией, ты все равно получишь Unique violation.
            // !!! Решение: Полное «обнуление» конфликта
            // !!! Чтобы гарантированно избежать проблем, тебе нужно на первом шаге временно сделать записи уникальными не только по позиции, но и по дате,
            // !!! чтобы они вообще не пересекались ни с какими существующими данными.
            //$placeholders = implode(',', array_fill(0, count($allIds), '?'));
            //DB::update("UPDATE {$table} SET position = (id * -1) WHERE id IN ({$placeholders})", $allIds);


            // __ ШАГ 1: Уводим в минус ТОЛЬКО те записи, где позиция реально будет обновлена
            if (!empty($idsForMinus)) {
                $placeholders = implode(',', array_fill(0, count($idsForMinus), '?'));
                DB::update("UPDATE {$table} SET position = (id * -1) WHERE id IN ({$placeholders})", $idsForMinus);
            }

            // __ ШАГ 2: Собираем финальный запрос
            $casesActionAt  = [];
            $paramsActionAt = [];
            $casesPosition  = [];
            $paramsPosition = [];

            foreach ($rows as $row) {
                if (isset($row['action_at'])) {
                    $casesActionAt[] = "WHEN id = ? THEN ?";
                    array_push($paramsActionAt, $row['id'], $row['action_at']);
                }
                if (isset($row['position'])) {
                    $casesPosition[] = "WHEN id = ? THEN ?";
                    array_push($paramsPosition, $row['id'], $row['position']);
                }
            }

            $setParts    = [];
            $finalParams = [];

            if (!empty($casesActionAt)) {
                $setParts[]  = "action_at = CASE " . implode(' ', $casesActionAt) . " ELSE action_at END";
                $finalParams = array_merge($finalParams, $paramsActionAt);
            }

            if (!empty($casesPosition)) {
                $setParts[]  = "position = CASE " . implode(' ', $casesPosition) . " ELSE position END";
                $finalParams = array_merge($finalParams, $paramsPosition);
            }

            if (empty($setParts)) {
                return;
            }

            $wherePlaceholders = implode(',', array_fill(0, count($allIds), '?'));
            $sql               = "UPDATE {$table} SET " . implode(', ', $setParts) . " WHERE id IN ({$wherePlaceholders})";

            // __ Соединяем параметры: параметры CASE1 + параметры CASE2 + параметры WHERE
            DB::update($sql, array_merge($finalParams, $allIds));
        });
    }


    /**
     * ___ Массовое обновление Записей через один запрос (Raw SQL Case)
     * @param array $rows
     * @return void
     * @throws Throwable
     */
    private function bulkUpdateLines(array $rows)
    {
        // __ Получаем имя таблицы
        $table = (new CuttingTaskLine)->getTable();

        // __ 1. Находим только те ID, у которых действительно меняется позиция (чтобы не уводить в минус лишнее)
        $rowsWithPosition = array_filter($rows, fn($row) => !is_null($row['position'] ?? null));
        $idsForMinus      = array_column($rowsWithPosition, 'id');

        // __ 2. Находим все ID, которые участвуют в обновлении (хоть позиция, хоть amount)
        $allIds = array_column($rows, 'id');

        DB::transaction(function () use ($table, $rows, $idsForMinus, $allIds) {
            // __ ШАГ 1: Уводим в минус ТОЛЬКО те записи, где позиция реально будет обновлена
            if (!empty($idsForMinus)) {
                $minusPlaceholders = implode(',', array_fill(0, count($idsForMinus), '?'));
                DB::update(
                    "UPDATE {$table} SET position = (id * -1) WHERE id IN ({$minusPlaceholders})",
                    $idsForMinus
                );
            }

            // __ ШАГ 2: Собираем финальный запрос
            $casesAmount    = [];
            $paramsAmount   = [];
            $casesPosition  = [];
            $paramsPosition = [];
            // $casesTaskId    = [];
            // $paramsTaskId   = [];

            foreach ($rows as $row) {
                if (isset($row['amount'])) {
                    $casesAmount[] = "WHEN id = ? THEN ?";
                    array_push($paramsAmount, $row['id'], $row['amount']);
                }
                if (isset($row['position'])) {
                    $casesPosition[] = "WHEN id = ? THEN ?";
                    array_push($paramsPosition, $row['id'], $row['position']);
                }
                // if (isset($row['cutting_task_id'])) {
                //     $casesTaskId[] = "WHEN id = ? THEN ?";
                //     array_push($paramsTaskId, $row['id'], $row['cutting_task_id']);
                // }
            }

            $setParts    = [];
            $finalParams = [];

            if (!empty($casesAmount)) {
                $setParts[]  = "amount = CASE " . implode(' ', $casesAmount) . " ELSE amount END";
                $finalParams = array_merge($finalParams, $paramsAmount);
            }

            if (!empty($casesPosition)) {
                $setParts[]  = "position = CASE " . implode(' ', $casesPosition) . " ELSE position END";
                $finalParams = array_merge($finalParams, $paramsPosition);
            }

            // if (!empty($casesTaskId)) {
            //     $setParts[]  = "cutting_task_id = CASE ".implode(' ', $casesTaskId)." ELSE cutting_task_id END";
            //     $finalParams = array_merge($finalParams, $paramsTaskId);
            // }

            if (empty($setParts)) {
                return;
            }

            $wherePlaceholders = implode(',', array_fill(0, count($allIds), '?'));
            $sql               = "UPDATE {$table} SET " . implode(', ', $setParts) . " WHERE id IN ({$wherePlaceholders})";

            // __ Соединяем параметры: параметры CASE1 + параметры CASE2 + параметры WHERE
            DB::update($sql, array_merge($finalParams, $allIds));
        });
    }


    /**
     * ___ Устанавливает комментарий к Сменному Заданию
     * @param Request $request
     * @return string
     */
    public function setCuttingTaskComment(Request $request)
    {
        try {
            $validated = $request->validate([
                'id'      => 'required|integer|exists:cutting_tasks,id',
                'comment' => 'present|nullable|string',
            ]);

            $cuttingTask = CuttingTask::query()->find($validated['id']);
            if (!$cuttingTask) {
                throw new Exception('Missing cutting task with id: ' . $validated['id'] . '.');
            }

            $cuttingTask->comment = $validated['comment'];
            $cuttingTask->save();

            return EndPointStaticRequestAnswer::ok();
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }


    /**
     * ___ Устанавливает новую дату (action_at) Сменному Заданию
     * @param Request $request
     * @return string
     */
    public function setCuttingTaskActionAt(Request $request)
    {
        try {
            $validated = $request->validate([
                'id'   => 'required|integer|exists:cutting_tasks,id',
                'date' => 'present|nullable|string|date_format:Y-m-d',
            ]);

            //$targetDate = is_null($validated['date']) ? Carbon::now() : Carbon::parse($validated['date']);

            //$targetDate = is_null($validated['date'])
            //    ? Carbon::now()->startOfDay()->format(RETURN_DATE_TIME_FORMAT)
            //    : Carbon::parse($validated['date'])->format(RETURN_DATE_TIME_FORMAT);

            DB::transaction(function () use ($validated) {
                // __ Получаем само СЗ и его старую дату + применяем изменения
                $cuttingTask = CuttingTask::query()->find($validated['id']);
                $oldDate     = $cuttingTask->action_at;
                // __ Устанавливаем позицию в отрицательную зону, так как наверняка в день, куда перемещаем уже есть такая позиция
                $cuttingTask->position  = -1 * $cuttingTask->id;
                $cuttingTask->action_at = Carbon::parse($validated['date'])->startOfDay()->format(RETURN_DATE_TIME_FORMAT);
                $cuttingTask->save();

                // __ Получаем все СЗ за день, из которого убираем СЗ
                $cuttingTasksFrom = CuttingTask::query()
                    ->whereDayAt($oldDate)
                    ->orderBy('position')
                    ->get();

                // __ Создаем производственный день или получаем его, если он уже существует
                // __ Идея - создать новый день, если он не существует
                $day = CuttingDay::findOrCreateByDateAndChange($validated['date']);

                // __ Получаем все СЗ за день, в которое добавляем СЗ c уже измененной датой
                $cuttingTasksTo = CuttingTask::query()
                    ->whereDayAt($validated['date'])
                    ->orderBy('position')
                    ->get();

                // __ Перемещаем СЗ в новый день в конец списка, так благодарая отрицательному position и orderBy он будет в самом начале
                // __ Проверяем, что коллекция не пустая
                if ($cuttingTasksTo->isNotEmpty()) {
                    // shift() удаляет первый элемент из коллекции и возвращает его
                    $firstItem = $cuttingTasksTo->shift();
                    // push() добавляет этот элемент в самый конец коллекции
                    $cuttingTasksTo->push($firstItem);
                }

                // debug
                //$cuttingTasksFromArray = $cuttingTasksFrom->toArray();
                //$cuttingTasksToArray = $cuttingTasksTo->toArray();


                // __ Теперь формируем данные для обновления с учетом position

                // __ Тот день, из которого убираем СЗ
                for ($i = 0; $i < 2; $i++) {
                    $cuttingTasks = $i == 0 ? $cuttingTasksFrom : $cuttingTasksTo;

                    $tasksToUpdate = [];
                    $position      = 1;
                    foreach ($cuttingTasks as $task) {
                        if ($task->position !== $position) {
                            $tasksToUpdate[] = [
                                'id'        => $task->id,
                                'action_at' => null,        // оставляем дату прежней
                                'position'  => $position,
                            ];
                        };
                        $position++;
                    }

                    // __ Применяем изменения
                    CuttingService::bulkUpdateTasks($tasksToUpdate);
                }
            });

            return EndPointStaticRequestAnswer::ok();
        } catch (Exception|Throwable $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }


    /**
     * ___ Устанавливаем статус Выполнено для линии
     * @param Request $request
     * @return AnonymousResourceCollection|string
     */
    public function setCuttingTaskLinesDone(Request $request)
    {
        try {
            $validated = $request->validate([
                'ids'   => 'required|array',
                'ids.*' => 'required|integer|exists:cutting_task_lines,id',
            ]);

            foreach ($validated['ids'] as $id) {
                $line = CuttingTaskLine::query()->find($id);
                if (!$line) {
                    throw new Exception('Missing cutting task line with id: ' . $id . '.');
                }

                $line->finished_at = now();
                $line->save();
            }

            $lines = CuttingTaskLine::query()->whereIn('id', $validated['ids'])->get();
            return CuttingTaskLineResource::collection($lines);
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }

    /**
     * ___ Устанавливаем статус Не выполнено для линии
     * @param Request $request
     * @return AnonymousResourceCollection|string
     */
    public function setCuttingTaskLinesFalse(Request $request)
    {
        try {
            $validated = $request->validate([
                'ids'    => 'required|array',
                'ids.*'  => 'required|integer|exists:cutting_task_lines,id',
                'reason' => 'required|string',
            ]);

            foreach ($validated['ids'] as $id) {
                $line = CuttingTaskLine::query()->find($id);
                if (!$line) {
                    throw new Exception('Missing cutting task line with id: ' . $id . '.');
                }

                $line->false_at     = now();
                $line->false_reason = $validated['reason'];

                $history = $line->false_history;
                if (is_null($history)) {
                    $history = [];
                }

                $history[]           = [
                    'at'     => $line->false_at->format(RETURN_DATE_TIME_FORMAT),
                    'by'     => auth()->id(),
                    'reason' => $validated['reason'],
                ];
                $line->false_history = $history;
                $line->finished_at   = null;
                $line->save();
            }

            $lines = CuttingTaskLine::query()->whereIn('id', $validated['ids'])->get();
            return CuttingTaskLineResource::collection($lines);
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }

    /**
     * ___ Сбрасываем отметку Выполнено/Не выполнено для линии
     * @param Request $request
     * @return AnonymousResourceCollection|string
     */
    public function setCuttingTaskLinesReset(Request $request)
    {
        try {
            $validated = $request->validate([
                'ids'   => 'required|array',
                'ids.*' => 'required|integer|exists:cutting_task_lines,id',
            ]);

            foreach ($validated['ids'] as $id) {
                $line = CuttingTaskLine::query()->find($id);
                if (!$line) {
                    throw new Exception('Missing cutting task line with id: ' . $id . '.');
                }

                $line->finished_at  = null;
                $line->false_at     = null;
                $line->false_reason = null;
                $line->save();
            }

            $lines = CuttingTaskLine::query()->whereIn('id', $validated['ids'])->get();
            return CuttingTaskLineResource::collection($lines);
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }


    public function taskLinesTableSet(Request $request)
    {
        try {
            $all = $request->all();

            $validated = $request->validate([
                // __ Проверяем, что 'data' — это обязательный, не пустой массив
                'data'         => 'required|array|min:1',

                // __ Проверяем ID внутри каждого элемента массива
                'data.*.id'    => 'required|integer|exists:cutting_task_lines,id',

                // __ Проверяем строку 'table' на соответствие конкретным значениям
                'data.*.table' => [
                    'required',
                    'string',
                    Rule::in([
                        CuttingTaskLine::FIELD_TABLE_1,
                        CuttingTaskLine::FIELD_TABLE_2,
                        CuttingTaskLine::FIELD_TABLE_3
                    ]),
                ],
            ]);

            // __ Валидация с кастомными сообщениями
            //$validated = $request->validate([
            //    'data' => 'required|array|min:1',
            //    'data.*.id' => 'required|integer|exists:cutting_task_lines,id',
            //    'data.*.table' => ['required', 'string', Rule::in([CuttingTaskLine::FIELD_TABLE_1, CuttingTaskLine::FIELD_TABLE_2, CuttingTaskLine::FIELD_TABLE_3])],
            //], [
            //    'data.required' => 'Массив данных обязателен для заполнения.',
            //    'data.min' => 'Массив данных не должен быть пустым.',
            //    'data.*.id.exists' => 'Выбранный ID задачи не существует в базе данных.',
            //    'data.*.table.in' => 'Поле table должно принимать значения: table_1, table_2 или table_3.',
            //]);

            $data = $validated['data'];
            DB::transaction(function () use ($data) {
                foreach ($data as $item) {
                    $line = CuttingTaskLine::query()->find($item['id']);
                    if (!$line) {
                        throw new Exception('Missing cutting task line with id: ' . $item['id'] . '.');
                    }
                    $line->table = $item['table'];
                    $line->save();
                }
            });


            // __ Скрипт для обновления одним запросом
            //// Строим сырой запрос для массового обновления (Bulk Update)
            //// Формируем плейсхолдеры (?, ?) для каждого элемента массива данных
            //$valuePairs = array_map(function () {
            //    return '(?, ?)';
            //}, $data);
            //
            //$valuesSql = implode(', ', $valuePairs);
            //
            //// Собираем все значения в один плоский массив для безопасной привязки параметров (SQL Injection Protection)
            //$bindings = [];
            //foreach ($data as $item) {
            //    $bindings[] = $item['id'];
            //    $bindings[] = $item['table'];
            //}
            //
            //// Итоговый SQL-запрос для PostgreSQL
            ///** @noinspection SqlDialectInspection */
            //$query = "
            //    UPDATE cutting_task_lines AS c
            //    SET \"table\" = v.new_table
            //    FROM (VALUES {$valuesSql}) AS v(id, new_table)
            //    WHERE c.id = CAST(v.id AS INTEGER)
            //";
            //
            //// Выполняем одним запросом внутри транзакции
            //DB::transaction(function () use ($query, $bindings) {
            //    DB::update($query, $bindings);
            //});


            return EndPointStaticRequestAnswer::ok('Изменено успешно');
        } catch (Exception|Throwable $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }


    /**
     * Возвращает массив уникальных id заказов
     * @param mixed $data
     * @return array
     */
    private function getUniqueOrdersIds(mixed $data): array
    {
        $result = [];

        if (is_null($data) || is_bool($data)) {
            return $result;
        }

        foreach ($data as $item) {
            $result[] = $item['line']['order_id'];
        }


        return array_unique($result);
    }


    public function test()
    {
        $result = CuttingService::test();


        return $result;
    }

}
