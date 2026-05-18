<?php

namespace App\Http\Controllers\Api\V1\Cells\Cutting;

use App\Classes\EndPointStaticRequestAnswer;
use App\Http\Controllers\Controller;
use App\Http\Resources\Manufacture\Cells\Cutting\Days\CuttingDayResource;
use App\Models\Manufacture\Cells\Cutting\CuttingDay;
use App\Models\Manufacture\Cells\Cutting\CuttingTask;
use App\Models\Manufacture\Cells\Cutting\CuttingTaskStatus;
use App\Services\Manufacture\CuttingService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Throwable;

class CellCuttingDayController extends Controller
{

    /**
     * ___ Возвращает производственный день по дате и смене
     * @param string $date
     * @param string $change
     * @return CuttingDayResource|string
     */
    public function getCuttingDayByDateAndChange(string $date, string $change)
    {
        try {
            $validated = Validator::make([
                'date'   => $date,
                'change' => $change,
            ], [
                'date'   => 'required|date_format:Y-m-d',
                'change' => 'required|in:1,2',
            ]);

            if ($validated->fails()) {
                throw new Exception($validated->errors()->first());
            }

            // __ Создаем производственный день или получаем его, если он уже существует
            $day = CuttingDay::findOrCreateByDateAndChange($date);

            $day = CuttingDay::query()
                ->byDates($date)
                ->with(['workers', 'responsible'])
                ->first();

            // $day = CuttingDay::query()
            //     ->with(['workers', 'responsible'])
            //     ->firstOrCreate([
            //         'action_at'     => Carbon::parse($date)->format(RETURN_DATE_TIME_FORMAT),
            //         'action_at_str' => $date,
            //         'change'        => $change,
            //     ]);

            return new CuttingDayResource($day);
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }

    /**
     * ___ Устанавливает комментарий к производственному дню
     * @param Request $request
     * @return string
     */
    public function setCuttingDayComment(Request $request)
    {
        try {
            $validated = $request->validate([
                'id'      => 'required|integer|exists:cutting_days,id',
                'comment' => 'present|nullable|string',
            ]);

            $cuttingDay = CuttingDay::query()->find($validated['id']);
            if (!$cuttingDay) {
                throw new Exception('Missing cutting day with id: ' . $validated['id'] . '.');
            }

            $cuttingDay->comment = $validated['comment'];
            $cuttingDay->save();

            return EndPointStaticRequestAnswer::ok();
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }


    /**
     * ___ Возвращает производственные дни по массиву дат
     * @param Request $request
     * @return AnonymousResourceCollection|string
     */
    public function getCuttingDaysByDates(Request $request)
    {
        try {
            $validated = $request->validate([

                // __ Проверяем, что 'dates' — это массив
                'dates'   => 'required|array',

                // __ Проверяем каждый элемент массива:
                // __ 'date' — это валидная дата
                // __ 'date_format' — строго YYYY-MM-DD
                // __ 'distinct' — чтобы даты не повторялись
                'dates.*' => 'required|date|date_format:Y-m-d|distinct',
            ]);

            foreach ($validated['dates'] as $date) {
                // __ Создаем производственный день или получаем его, если он уже существует
                CuttingDay::findOrCreateByDateAndChange($date);
            }

            $days = CuttingDay::query()
                ->byDates($validated['dates'])
                ->with(['workers', 'responsible'])
                ->get();

            return CuttingDayResource::collection($days);
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }


    /**
     * ___ Добавляет рабочего к производственному дню
     * @param Request $request
     * @return string
     */
    public function addWorkerToCuttingDay(Request $request)
    {
        try {
            $validated = $request->validate([
                'day_id'    => 'required|integer|exists:cutting_days,id',
                'worker_id' => 'required|integer|exists:workers,id',
            ]);

            $cuttingDay = CuttingDay::query()->findOrFail($validated['day_id']);
            $cuttingDay->workers()->syncWithoutDetaching([
                $validated['worker_id'] => ['working_time' => 8 * 60]  // __ ID рабочего и данные для pivot-таблицы
            ]);

            return EndPointStaticRequestAnswer::ok();
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }

    /**
     * ___ Добавляет Группу рабочих к производственному дню
     * @param Request $request
     * @return string
     */
    public function addWorkersToCuttingDay(Request $request)
    {
        try {
            $validated = $request->validate([
                'day_id'       => 'required|integer|exists:cutting_days,id',
                'worker_ids'   => 'required|array|min:1', // Проверяем, что это массив и он не пуст
                'worker_ids.*' => 'integer|exists:workers,id', // Проверяем каждый элемент массива
            ]);

            $cuttingDay = CuttingDay::query()->findOrFail($validated['day_id']);

            // __ Подготавливаем данные для синхронизации
            // __ Превращаем [1, 2, 3] в [1 => ['wt' => 480], 2 => ['wt' => 480], ...]
            $syncData = array_fill_keys($validated['worker_ids'], [
                'working_time' => 8 * 60
            ]);

            // __ Добавляем только новые связи, не трогая старые
            $cuttingDay->workers()->syncWithoutDetaching($syncData);

            /**
             * sync($data, false) делает следующее:
             * 1. Добавляет новых рабочих из списка.
             * 2. ОБНОВЛЯЕТ данные (working_time) у тех, кто уже есть в списке.
             * 3. НЕ УДАЛЯЕТ тех рабочих, которые уже привязаны к дню, но не присланы в этот раз.
             */
            // $cuttingDay->workers()->sync($syncData, false); // <-- дополнительно добавляем false

            /**
             * sync($data) делает полную синхронизацию:
             * 1. Добавляет новых рабочих из worker_ids.
             * 2. Обновляет working_time у тех, кто уже был в базе и прислан в этот раз.
             * 3. УДАЛЯЕТ (detach) всех рабочих, которые были привязаны к дню, но отсутствуют в worker_ids.
             */
            // Для этого используется классический метод sync() без дополнительных флагов.
            // Это поведение в Laravel считается стандартным: всё, что не перечислено в массиве,
            // будет удалено из связей (таблицы pivot), а всё, что перечислено — добавлено или обновлено.
            $cuttingDay->workers()->sync($syncData); // <-- не добавляем false


            return EndPointStaticRequestAnswer::ok();
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }


    /**
     * ___ Удаляем рабочего из производственного дня
     * @param Request $request
     * @return string
     */
    public function removeWorkerFromCuttingDay(Request $request)
    {
        try {
            $validated = $request->validate([
                'day_id'    => 'required|integer|exists:cutting_days,id',
                'worker_id' => 'required|integer|exists:workers,id',
            ]);

            $cuttingDay = CuttingDay::query()->find($validated['day_id']);

            // __ Отвязываем рабочего от производственного дня
            $cuttingDay->workers()->detach($validated['worker_id']);

            // __ Если рабочий был ответственным, то убираем его
            if ($cuttingDay->responsible_id == $validated['worker_id']) {
                $cuttingDay->responsible_id = null;
                $cuttingDay->save();
            }

            return EndPointStaticRequestAnswer::ok();
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }

    /**
     * ___ Добавляет Ответственного к производственному дню
     * @param Request $request
     * @return string
     */
    public function addResponsibleToCuttingDay(Request $request)
    {
        try {
            $validated = $request->validate([
                'day_id'    => 'required|integer|exists:cutting_days,id',
                'worker_id' => 'required|integer|exists:workers,id',
            ]);

            $cuttingDay = CuttingDay::query()->find($validated['day_id']);
            $cuttingDay->responsible_id = $validated['worker_id'];
            $cuttingDay->save();

            return EndPointStaticRequestAnswer::ok();
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }


    /**
     * ___ Удаляем Ответственного из производственного дня
     * @param Request $request
     * @return string
     */
    public function removeResponsibleFromCuttingDay(Request $request)
    {
        try {
            $validated = $request->validate([
                'day_id'    => 'required|integer|exists:cutting_days,id',
                'worker_id' => 'required|integer|exists:workers,id',
            ]);

            $cuttingDay = CuttingDay::query()->find($validated['day_id']);
            $cuttingDay->responsible_id = null;
            $cuttingDay->save();

            return EndPointStaticRequestAnswer::ok();
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }


    /**
     * ___ Стартуем СЗ производственного дня
     * @param Request $request
     * @return CuttingDayResource|string
     */
    public function startCuttingDay(Request $request)
    {
        try {
            $validated = $request->validate([
                'id' => 'required|integer|exists:cutting_days,id',
            ]);

            $cuttingDay = CuttingDay::query()->find($validated['id']);

            // __ Сохраняем
            DB::transaction(function () use ($cuttingDay) {
                // __ Или начинаем, если не начато, или продолжаем, но запоминаем время возобновления
                if (is_null($cuttingDay->start_at)) {
                    $cuttingDay->start_at = now();
                } else {
                    $cuttingDay->resume_at = now();
                }
                $cuttingDay->finish_at = null;
                $cuttingDay->save();

                // __ Находим все СЗ, которые относятся к данному производственному дню и меняем их статус на "Выполняется"
                $action_date = Carbon::parse($cuttingDay->action_at)->startOfDay();

                $pendingCuttingTasks = CuttingTask::query()
                    // ->whereBetween('action_at', [
                    //     $action_date->startOfDay(),
                    //     $action_date->endOfDay()
                    // ])
                    ->whereDate('action_at', '>=', $action_date->startOfDay())
                    ->whereDate('action_at', '<=', $action_date->endOfDay())
                    ->byStatus(CuttingTaskStatus::CUTTING_STATUS_PENDING_ID)
                    // ->byStatus(CuttingTaskStatus::CUTTING_STATUS_RUNNING_ID)
                    ->with(['statuses',])
                    ->get();

                //$pendCuttingTasksArr = $pendingCuttingTasks->toArray();

                foreach ($pendingCuttingTasks as $task) {
                    // __ Создаем запись в Статусе: Выполняется
                    $task->statuses()->attach([
                        CuttingTaskStatus::CUTTING_STATUS_RUNNING_ID => [
                            'set_at'     => $cuttingDay->start_at,
                            'created_by' => auth()->id(),
                        ]
                    ]);
                }
            });

            return new CuttingDayResource($cuttingDay);
        } catch (Exception|Throwable $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }


    /**
     * ___ Заканчиваем СЗ производственного дня
     * @param Request $request
     * @return CuttingDayResource|string
     */
    public function finishCuttingDay(Request $request)
    {
        // __ Завершаем день со СЗ
        // __ 1. Находим производственный день
        // __ 2. Находим СЗ для этого Дня со статусом Выполняется
        // __ 3. Проверяем все Линии на статус Выполнено
        // __ 4. Закрываем СЗ


        try {
            $validated = $request->validate([
                'id' => 'required|integer|exists:cutting_days,id',
            ]);

            // __ Находим производственный день
            $cuttingDay = CuttingDay::query()
                ->find($validated['id']);

            // __ Сохраняем
            DB::transaction(function () use ($cuttingDay) {
                $cuttingDay->finish_at = now();

                // __ Добавляем длительность в секундах
                $startPoint = is_null($cuttingDay->resume_at) ? $cuttingDay->start_at : $cuttingDay->resume_at;
                $cuttingDay->duration += $startPoint?->diffInSeconds($cuttingDay->finish_at) ?? 0;
                $cuttingDay->save();

                // __ Находим все СЗ, которые относятся к данному производственному дню и меняем их статус на "Выполняется"
                $action_date = Carbon::parse($cuttingDay->start_at)->startOfDay();

                $pendingCuttingTasks = CuttingTask::query()
                    // ->whereBetween('action_at', [
                    //     $action_date->startOfDay(),
                    //     $action_date->endOfDay()
                    // ])
                    ->whereDate('action_at', '>=', $action_date->startOfDay())
                    ->whereDate('action_at', '<=', $action_date->endOfDay())
                    ->byStatus(CuttingTaskStatus::CUTTING_STATUS_RUNNING_ID)
                    ->with(['statuses', 'cuttingLines'])
                    ->get();

                // debug
                //$pendTaskArr = $pendingCuttingTasks->toArray();

                // __ Собираем невыполненные СЗ
                $falseTasks = [];

                foreach ($pendingCuttingTasks as $task) {
                    // __ Собираем невыполненный контент
                    $falseCuttingLines = [];
                    $falseCuttingLinesAmounts = 0;
                    $totalCuttingLinesAmounts = 0;


                    foreach ($task->cuttingLines as $line) {
                        // __ Проверка на то, чтобы строка была или выполнена или указана причина невыполнения
                        if (is_null($line->finished_at) && is_null($line->false_at)) {
                            throw new Exception('Missing done or false status for line with id: ' . $line->id);
                        }

                        // __ Собираем все невыполненные строчки
                        if (!is_null($line->false_at)) {
                            $falseCuttingLines[] = $line;
                            $falseCuttingLinesAmounts += $line->amount;
                        }

                        $totalCuttingLinesAmounts += $line->amount;
                    }

                    // __ Если есть невыполненные - переносим на следующий день и ставим первыми
                    if (count($falseCuttingLines) !== 0) {
                        $falseTasks[] = [
                            'task'        => $task,
                            'false_lines' => $falseCuttingLines,
                            // __ Все задания невыполненные
                            // __ Флаг, что нужно перенести все СЗ на другую дату
                            'all_false'   => $falseCuttingLinesAmounts === $totalCuttingLinesAmounts,
                        ];
                    }

                    // __ Создаем запись в Статусе: Выполнено, если в СЗ есть хотя бы одна выполненная линия
                    if ($falseCuttingLinesAmounts < $totalCuttingLinesAmounts) {
                        $task->statuses()->attach([
                            CuttingTaskStatus::CUTTING_STATUS_DONE_ID => [
                                'set_at'     => $cuttingDay->finish_at,
                                'created_by' => auth()->id(),
                            ]
                        ]);
                    }
                }

                // __ Обрабатываем все невыполненное
                if (count($falseTasks) !== 0) {
                    // __ Получаем следующую смену
                    $nextChange = CuttingService::getNextChange($cuttingDay->action_at, $cuttingDay->change);

                    // __ Получаем все СЗ следующей смены
                    $existingTasks = CuttingTask::query()
                        ->whereDate('action_at', '>=', $nextChange->getManufactureDay()->startOfDay())
                        ->whereDate('action_at', '<=', $nextChange->getManufactureDay()->endOfDay())
                        // ->whereBetween('action_at', [
                        //     $nextChange->getManufactureDay()->startOfDay(),
                        //     $nextChange->getManufactureDay()->endOfDay()
                        // ])
                        ->orderBy('position')
                        ->get();

                    // $existTasksArray = $existingTasks->toArray();

                    // __ Объединяем в один массив существующие СЗ и перенесенные
                    // __ из предыдущего дня, располагая а начале массива
                    // __ и перенумеровываем заново
                    $position = 1;
                    $tasksToUpdate = [];

                    // __ Добавляем перенесенные СЗ
                    foreach ($falseTasks as $falseTask) {
                        // __ Ситуация, когда перенесли все линии СЗ (Просто переносим на другую дату)
                        if ($falseTask['all_false']) {
                            $tasksToUpdate[] = [
                                'id'        => $falseTask['task']->id,
                                'action_at' => $nextChange->getManufactureDay(),
                                'position'  => $position++,
                            ];

                            // __ Тут же Создаем запись в Статусе: Готово к выполнению с добавлением секунды
                            $falseTask['task']->statuses()->attach([
                                CuttingTaskStatus::CUTTING_STATUS_PENDING_ID => [
                                    'set_at'     => $cuttingDay->finish_at->addSecond(),
                                    'created_by' => auth()->id(),
                                ]
                            ]);

                            continue;
                        }


                        // __ Создаем новые СЗ и сохраняем в БД
                        $newTask = $falseTask['task']->replicate();
                        $newTask->position *= -1;
                        $newTask->save();
                        $newTask->position = $newTask->id * (-1);
                        $newTask->save();

                        // __ Создаем запись в Статусе: Создано при закрытии СЗ
                        $newTask->statuses()->attach([
                            CuttingTaskStatus::CUTTING_STATUS_ROLLING_ID => [
                                'set_at'     => $cuttingDay->finish_at,
                                'created_by' => auth()->id(),
                            ]
                        ]);

                        // __ Тут же Создаем запись в Статусе: Готово к выполнению с добавлением секунды
                        $newTask->statuses()->attach([
                            CuttingTaskStatus::CUTTING_STATUS_PENDING_ID => [
                                'set_at'     => $cuttingDay->finish_at->addSecond(),
                                'created_by' => auth()->id(),
                            ]
                        ]);


                        // __ Тут получили id уже нового СЗ
                        // __ Привязываем невыполненные линии к новому СЗ
                        // TODO: Warn!! Тут можно попробовать сделать одним запросом
                        $positionLine = 1;
                        foreach ($falseTask['false_lines'] as $line) {
                            $line->cutting_task_id = $newTask->id;
                            $line->position = $positionLine++;
                            $line->save();
                        }

                        // __ Переносим на следующий день
                        $tasksToUpdate[] = [
                            'id'        => $newTask->id,
                            'action_at' => $nextChange->getManufactureDay(),
                            'position'  => $position++,
                        ];
                    }

                    // __ Добавляем существующие СЗ
                    foreach ($existingTasks as $task) {
                        $tasksToUpdate[] = [
                            'id'        => $task->id,
                            'action_at' => null,        // оставляем дату прежней
                            'position'  => $position++,
                        ];
                    }

                    // __ Применяем изменения
                    CuttingService::bulkUpdateTasks($tasksToUpdate);
                }
            });

            return new CuttingDayResource($cuttingDay);
        } catch (Exception|Throwable $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }


    /**
     * ___ Установки маяка готовности к добавлению новых СЗ
     * @param Request $request
     * @return CuttingDayResource|string
     */
    public function readySetCuttingDay(Request $request)
    {
        try {
            $validated = $request->validate([
                'id' => 'required|integer|exists:cutting_days,id',
            ]);

            $cuttingDay = CuttingDay::query()->find($validated['id']);
            $cuttingDay->ready = true;

            $history = $cuttingDay->history;
            if (is_null($history)) {
                $history = [];
            }

            $history[] = [
                'at'     => Carbon::now()->format(RETURN_DATE_TIME_FORMAT),
                'by'     => auth()->id(),
                'action' => 'Set ready for adding new Cutting Tasks',
            ];
            $cuttingDay->history = $history;

            $cuttingDay->save();

            return new CuttingDayResource($cuttingDay);
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }

    /**
     * ___ Установки маяка готовности к добавлению новых СЗ
     * @param Request $request
     * @return CuttingDayResource|string
     */
    public function readyUnsetCuttingDay(Request $request)
    {
        try {
            $validated = $request->validate([
                'id' => 'required|integer|exists:cutting_days,id',
            ]);

            $cuttingDay = CuttingDay::query()->find($validated['id']);
            $cuttingDay->ready = false;

            $history = $cuttingDay->history;
            if (is_null($history)) {
                $history = [];
            }

            $history[] = [
                'at'     => Carbon::now()->format(RETURN_DATE_TIME_FORMAT),
                'by'     => auth()->id(),
                'action' => 'Set unready for adding new Cutting Tasks',
            ];
            $cuttingDay->history = $history;

            $cuttingDay->save();

            return new CuttingDayResource($cuttingDay);
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }


    /**
     * ___ Возвращает маяк готовности к добавлению новых СЗ
     * @param string $date
     * @param string $change
     * @return array|false[]|string
     */
    public function readyGetCuttingDay(string $date, string $change)
    {
        try {
            $validated = Validator::make([
                'date'   => $date,
                'change' => $change,
            ], [
                'date'   => 'required|date_format:Y-m-d',
                'change' => 'required|in:1,2',
            ]);

            if ($validated->fails()) {
                throw new Exception($validated->errors()->first());
            }

            // __ Создаем производственный день или получаем его, если он уже существует
            $data = $validated->validated();
            $parsedDate = Carbon::parse($data['date']);
            $day = CuttingDay::query()
                ->whereDate('action_at', '>=', $parsedDate->startOfDay())
                ->whereDate('action_at', '<=', $parsedDate->endOfDay())
                ->first();

            return $day ? ['data' => !!$day->ready] : ['data' => false];

            //return new CuttingDayResource($day);
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }

}
