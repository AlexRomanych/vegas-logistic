<?php

namespace App\Http\Controllers\Api\V1\Cells\Assembly;

use App\Classes\EndPointStaticRequestAnswer;
use App\Http\Controllers\Controller;
use App\Http\Resources\Manufacture\Cells\Assembly\Days\AssemblyDayResource;
use App\Models\Manufacture\Cells\Assembly\AssemblyDay;
use App\Models\Manufacture\Cells\Assembly\AssemblyTask;
use App\Models\Manufacture\Cells\Assembly\AssemblyTaskStatus;
use App\Services\DefaultsService;
use App\Services\Manufacture\AssemblyService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Throwable;

class AssemblyDayController extends Controller
{

    /**
     * ___ Получаем производственные дни за период
     * @param Request $request
     * @return AnonymousResourceCollection|string
     * @noinspection PhpUndefinedFieldInspection
     * @noinspection DuplicatedCode
     */
    public function getAssemblyDays(Request $request)
    {
        try {
            //$all = $request->all();

            $validated = $request->validate([
                'period'       => 'nullable|array',
                'period.start' => 'required_if:period,*,!null|date',        // условная валидация
                'period.end'   => 'required_if:period,*,!null|date',
            ]);

            if (isset($validated['period'])) {
                $start = Carbon::parse($validated['period']['start']);
                $end   = Carbon::parse($validated['period']['end']);
            } else {
                $period = DefaultsService::getDefaultPeriodOrdersShow();
                $start  = Carbon::parse($period->getStart());
                $end    = Carbon::parse($period->getEnd());
            }

            // __ Получаем СЗ
            $assemblyTasks = AssemblysService::getAssemblyTasksByDatesAndStatus($start, $end);

            // __ Получаем Производственные дни
            $days = AssemblyDay::query()
                ->byPeriod($start, $end)
                ->with(['workers', 'responsible', 'cellEvents'])
                ->get();

            //$assemblyTasksArray = $assemblyTasks->toArray();
            //$daysArray = $days->toArray();

            // __ Группируем задачи по составному ключу (action_at + change)
            // __ Приводим дату к строке Y-m-d, чтобы гарантировать точное совпадение дней
            $groupedTasks = $assemblyTasks->groupBy(function (AssemblyTask $task) {
                $date = $task->action_at instanceof Carbon
                    ? $task->action_at->format('Y-m-d')
                    : date('Y-m-d', strtotime($task->action_at));

                return "{$date}_{$task->change}";
            });

            // __ Руками «прошиваем» отношение assemblyTasks для каждого дня
            $days->each(function (AssemblyDay $day) use ($groupedTasks) {
                $date = $day->action_at instanceof Carbon
                    ? $day->action_at->format('Y-m-d')
                    : date('Y-m-d', strtotime($day->action_at));

                $key = "{$date}_{$day->change}";

                // __ Достаем задачи для этого дня и смены, если их нет — возвращаем пустую коллекцию
                $associatedTasks = $groupedTasks->get($key, collect());

                // __ Сортируем задачи внутри этого дня по полю 'position'
                $sortedTasks = $associatedTasks->sortBy('position')->values();

                // __ Заселяем кастомное отношение прямо в память модели AssemblyDay
                $day->setRelation('assemblyTasks', $sortedTasks);
            });

            return AssemblyDayResource::collection($days);
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }


    /**
     * ___ Возвращает производственный день по дате и смене
     * @param string $date
     * @param string $change
     * @return AnonymousResourceCollection|string
     */
    public function getAssemblyDayByDateAndChange(string $date, string $change)
    {
        try {
            $validated = Validator::make([
                'date'   => $date,
                'change' => $change,
            ], [
                'date'   => 'required|date_format:Y-m-d',
                'change' => 'required|in:'
                    . AssemblyDay::CHANGE_0 . ','
                    . AssemblyDay::CHANGE_1 . ','
                    . AssemblyDay::CHANGE_2
            ]);

            if ($validated->fails()) {
                throw new Exception($validated->errors()->first());
            }

            // __ Создаем производственный день или получаем его, если он уже существует
            if ($change !== AssemblyDay::CHANGE_0) {
                AssemblyDay::findOrCreateByDateAndChange($date, $change);
                $days = AssemblyDay::query()
                    ->where('change', $change)
                    ->byDates($date)
                    ->with(['workers', 'responsible', 'cellEvents'])
                    ->get();
            } else {
                AssemblyDay::findOrCreateByDateAndChange($date, AssemblyDay::CHANGE_1);
                AssemblyDay::findOrCreateByDateAndChange($date, AssemblyDay::CHANGE_2);
                $days = AssemblyDay::query()
                    ->byDates($date)
                    ->with(['workers', 'responsible', 'cellEvents'])
                    ->get();
            }

            return AssemblyDayResource::collection($days);
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }

    /**
     * ___ Устанавливает комментарий к производственному дню
     * @param Request $request
     * @return string
     */
    public function setAssemblyDayComment(Request $request)
    {
        try {
            $validated = $request->validate([
                'id'      => 'required|integer|exists:assembly_days,id',
                'comment' => 'present|nullable|string',
            ]);

            $assemblyDay = AssemblyDay::query()->find($validated['id']);
            if (!$assemblyDay) {
                throw new Exception('Missing assembly day with id: ' . $validated['id'] . '.');
            }

            $assemblyDay->comment = $validated['comment'];
            $assemblyDay->save();

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
    public function getAssemblyDaysByDates(Request $request)
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
                AssemblyDay::findOrCreateByDateAndChange($date, AssemblyDay::CHANGE_1);
                AssemblyDay::findOrCreateByDateAndChange($date, AssemblyDay::CHANGE_2);
            }

            $days = AssemblyDay::query()
                ->byDates($validated['dates'])
                ->with(['workers', 'responsible', 'cellEvents'])
                ->get();

            return AssemblyDayResource::collection($days);
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }


    /**
     * ___ Добавляет рабочего к производственному дню
     * @param Request $request
     * @return string
     */
    public function addWorkerToAssemblyDay(Request $request)
    {
        try {
            $validated = $request->validate([
                'day_id'    => 'required|integer|exists:assembly_days,id',
                'worker_id' => 'required|integer|exists:workers,id',
            ]);

            $assemblyDay = AssemblyDay::query()->findOrFail($validated['day_id']);
            $assemblyDay->workers()->syncWithoutDetaching([
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
    public function addWorkersToAssemblyDay(Request $request)
    {
        try {
            $validated = $request->validate([
                'day_id'       => 'required|integer|exists:assembly_days,id',
                'worker_ids'   => 'required|array|min:1', // Проверяем, что это массив и он не пуст
                'worker_ids.*' => 'integer|exists:workers,id', // Проверяем каждый элемент массива
            ]);

            $assemblyDay = AssemblyDay::query()->findOrFail($validated['day_id']);

            // __ Подготавливаем данные для синхронизации
            // __ Превращаем [1, 2, 3] в [1 => ['wt' => 480], 2 => ['wt' => 480], ...]
            $syncData = array_fill_keys($validated['worker_ids'], [
                'working_time' => 8 * 60
            ]);

            // __ Добавляем только новые связи, не трогая старые
            $assemblyDay->workers()->syncWithoutDetaching($syncData);

            /**
             * sync($data, false) делает следующее:
             * 1. Добавляет новых рабочих из списка.
             * 2. ОБНОВЛЯЕТ данные (working_time) у тех, кто уже есть в списке.
             * 3. НЕ УДАЛЯЕТ тех рабочих, которые уже привязаны к дню, но не присланы в этот раз.
             */
            // $assemblyDay->workers()->sync($syncData, false); // <-- дополнительно добавляем false

            /**
             * sync($data) делает полную синхронизацию:
             * 1. Добавляет новых рабочих из worker_ids.
             * 2. Обновляет working_time у тех, кто уже был в базе и прислан в этот раз.
             * 3. УДАЛЯЕТ (detach) всех рабочих, которые были привязаны к дню, но отсутствуют в worker_ids.
             */
            // Для этого используется классический метод sync() без дополнительных флагов.
            // Это поведение в Laravel считается стандартным: всё, что не перечислено в массиве,
            // будет удалено из связей (таблицы pivot), а всё, что перечислено — добавлено или обновлено.
            $assemblyDay->workers()->sync($syncData); // <-- не добавляем false


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
    public function removeWorkerFromAssemblyDay(Request $request)
    {
        try {
            $validated = $request->validate([
                'day_id'    => 'required|integer|exists:assembly_days,id',
                'worker_id' => 'required|integer|exists:workers,id',
            ]);

            $assemblyDay = AssemblyDay::query()->find($validated['day_id']);

            // __ Отвязываем рабочего от производственного дня
            $assemblyDay->workers()->detach($validated['worker_id']);

            // __ Если рабочий был ответственным, то убираем его
            if ($assemblyDay->responsible_id == $validated['worker_id']) {
                $assemblyDay->responsible_id = null;
                $assemblyDay->save();
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
    public function addResponsibleToAssemblyDay(Request $request)
    {
        try {
            $validated = $request->validate([
                'day_id'    => 'required|integer|exists:assembly_days,id',
                'worker_id' => 'required|integer|exists:workers,id',
            ]);

            $assemblyDay                 = AssemblyDay::query()->find($validated['day_id']);
            $assemblyDay->responsible_id = $validated['worker_id'];
            $assemblyDay->save();

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
    public function removeResponsibleFromAssemblyDay(Request $request)
    {
        try {
            $validated = $request->validate([
                'day_id'    => 'required|integer|exists:assembly_days,id',
                'worker_id' => 'required|integer|exists:workers,id',
            ]);

            $assemblyDay                 = AssemblyDay::query()->find($validated['day_id']);
            $assemblyDay->responsible_id = null;
            $assemblyDay->save();

            return EndPointStaticRequestAnswer::ok();
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }


    /**
     * ___ Стартуем СЗ производственного дня
     * @param Request $request
     * @return AssemblyDayResource|string
     * @noinspection PhpPossiblePolymorphicInvocationInspection
     */
    public function startAssemblyDay(Request $request)
    {
        try {
            $validated = $request->validate([
                'id' => 'required|integer|exists:assembly_days,id',
            ]);

            $assemblyDay = AssemblyDay::query()->find($validated['id']);

            // __ Сохраняем
            DB::transaction(function () use ($assemblyDay) {
                // __ Или начинаем, если не начато, или продолжаем, но запоминаем время возобновления
                if (is_null($assemblyDay->start_at)) {
                    $assemblyDay->start_at = now();
                } else {
                    $assemblyDay->resume_at = now();
                }
                $assemblyDay->finish_at = null;
                $assemblyDay->save();

                // __ Находим все СЗ, которые относятся к данному производственному дню и меняем их статус на "Выполняется"
                $action_date = Carbon::parse($assemblyDay->action_at)->startOfDay();

                $pendingAssemblyTasks = AssemblyTask::query()
                    // ->whereBetween('action_at', [
                    //     $action_date->startOfDay(),
                    //     $action_date->endOfDay()
                    // ])
                    ->whereDate('action_at', '>=', $action_date->startOfDay())
                    ->whereDate('action_at', '<=', $action_date->endOfDay())
                    ->where('change', $assemblyDay->change)
                    ->byStatus(AssemblyTaskStatus::ASSEMBLY_STATUS_PENDING_ID)
                    // ->byStatus(AssemblyTaskStatus::ASSEMBLY_STATUS_RUNNING_ID)
                    ->with(['statuses',])
                    ->get();

                if ($pendingAssemblyTasks->isEmpty()) {
                    throw new Exception('Tasks in Day with day_id = ' . $assemblyDay->id . ' and change = ' . $assemblyDay->change . ' not found');
                }

                //$pendAssemblyTasksArr = $pendingAssemblyTasks->toArray();

                foreach ($pendingAssemblyTasks as $task) {
                    // __ Создаем запись в Статусе: Выполняется
                    $task->statuses()->attach([
                        AssemblyTaskStatus::ASSEMBLY_STATUS_RUNNING_ID => [
                            'set_at'     => $assemblyDay->start_at,
                            'created_by' => auth()->id(),
                        ]
                    ]);
                }
            });

            return new AssemblyDayResource($assemblyDay);
        } catch (Exception|Throwable $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }


    /**
     * ___ Заканчиваем СЗ производственного дня
     * @param Request $request
     * @return AssemblyDayResource|string
     * @noinspection PhpPossiblePolymorphicInvocationInspection
     */
    public function finishAssemblyDay(Request $request)
    {
        // __ Завершаем день со СЗ
        // __ 1. Находим производственный день
        // __ 2. Находим СЗ для этого Дня со статусом Выполняется
        // __ 3. Проверяем все Линии на статус Выполнено
        // __ 4. Закрываем СЗ


        try {
            $validated = $request->validate([
                'id' => 'required|integer|exists:assembly_days,id',
            ]);

            // __ Находим производственный день
            $assemblyDay = AssemblyDay::query()
                ->find($validated['id']);

            // __ Сохраняем все в транзакции
            DB::transaction(function () use ($assemblyDay) {
                $assemblyDay->finish_at = now();

                // __ Добавляем длительность в секундах
                $startPoint         = is_null($assemblyDay->resume_at) ? $assemblyDay->start_at : $assemblyDay->resume_at;
                $assemblyDay->duration += $startPoint?->diffInSeconds($assemblyDay->finish_at) ?? 0;
                $assemblyDay->save();

                // __ Находим все СЗ, которые относятся к данному производственному дню и меняем их статус на "Выполнено"
                $action_date = Carbon::parse($assemblyDay->start_at)->startOfDay();

                $pendingAssemblyTasks = AssemblyTask::query()
                    ->whereDate('action_at', '>=', $action_date->startOfDay())
                    ->whereDate('action_at', '<=', $action_date->endOfDay())
                    ->where('change', $assemblyDay->change)
                    ->byStatus(AssemblyTaskStatus::ASSEMBLY_STATUS_RUNNING_ID)
                    ->with(['statuses', 'assemblyLines'])
                    ->get();

                if ($pendingAssemblyTasks->isEmpty()) {
                    throw new Exception('Tasks in Day with day_id = ' . $assemblyDay->id . ' and change = ' . $assemblyDay->change . ' not found');
                }

                // debug
                //$pendTaskArr = $pendingAssemblyTasks->toArray();

                // __ Собираем невыполненные СЗ
                $falseTasks = [];

                foreach ($pendingAssemblyTasks as $task) {
                    // __ Собираем невыполненный контент
                    $falseAssemblyLines        = [];
                    $falseAssemblyLinesAmounts = 0;
                    $totalAssemblyLinesAmounts = 0;


                    foreach ($task->assemblyLines as $line) {
                        // __ Проверка на то, чтобы строка была или выполнена или указана причина невыполнения
                        if (is_null($line->finished_at) && is_null($line->false_at)) {
                            throw new Exception('Missing done or false status for line with id: ' . $line->id);
                        }

                        // __ Собираем все невыполненные строчки
                        if (!is_null($line->false_at)) {
                            $falseAssemblyLines[]      = $line;
                            $falseAssemblyLinesAmounts += $line->amount;
                        }

                        $totalAssemblyLinesAmounts += $line->amount;
                    }

                    // __ Если есть невыполненные - переносим на следующий день и ставим первыми
                    if (count($falseAssemblyLines) !== 0) {
                        $falseTasks[] = [
                            'task'        => $task,
                            'false_lines' => $falseAssemblyLines,
                            // __ Все задания невыполненные
                            // __ Флаг, что нужно перенести все СЗ на другую дату
                            'all_false'   => $falseAssemblyLinesAmounts === $totalAssemblyLinesAmounts,
                        ];
                    }

                    // __ Создаем запись в Статусе: Выполнено, если в СЗ есть хотя бы одна выполненная линия
                    if ($falseAssemblyLinesAmounts < $totalAssemblyLinesAmounts) {
                        $task->statuses()->attach([
                            AssemblyTaskStatus::ASSEMBLY_STATUS_DONE_ID => [
                                'set_at'     => $assemblyDay->finish_at,
                                'created_by' => auth()->id(),
                            ]
                        ]);
                    }
                }

                // __ Обрабатываем все невыполненное
                if (count($falseTasks) !== 0) {
                    // __ Получаем следующую смену
                    $nextChange = AssemblysService::getNextChange($assemblyDay->action_at, $assemblyDay->change);

                    // __ Получаем все СЗ следующей смены
                    $existingTasks = AssemblyTask::query()
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
                    $position      = 1;
                    $tasksToUpdate = [];

                    // __ Добавляем перенесенные СЗ
                    foreach ($falseTasks as $falseTask) {
                        // __ Ситуация, когда перенесли все линии СЗ (Просто переносим на другую дату)
                        if ($falseTask['all_false']) {
                            $tasksToUpdate[] = [
                                'id'        => $falseTask['task']->id,
                                'action_at' => $nextChange->getManufactureDay()->startOfDay(),
                                'change'    => $nextChange->getChange(),
                                'position'  => $position++,
                            ];

                            // __ Тут же Создаем запись в Статусе: Готово к выполнению с добавлением секунды
                            $falseTask['task']->statuses()->attach([
                                AssemblyTaskStatus::ASSEMBLY_STATUS_PENDING_ID => [
                                    'set_at'     => $assemblyDay->finish_at->addSecond(),
                                    'created_by' => auth()->id(),
                                ]
                            ]);

                            continue;
                        }


                        // __ Создаем новые СЗ и сохраняем в БД
                        $newTask           = $falseTask['task']->replicate();
                        $newTask->position *= -1;
                        $newTask->save();
                        $newTask->position = $newTask->id * (-1);
                        $newTask->save();

                        // __ Создаем запись в Статусе: Создано при закрытии СЗ
                        $newTask->statuses()->attach([
                            AssemblyTaskStatus::ASSEMBLY_STATUS_ROLLING_ID => [
                                'set_at'     => $assemblyDay->finish_at,
                                'created_by' => auth()->id(),
                            ]
                        ]);

                        // __ Тут же Создаем запись в Статусе: Готово к выполнению с добавлением секунды
                        $newTask->statuses()->attach([
                            AssemblyTaskStatus::ASSEMBLY_STATUS_PENDING_ID => [
                                'set_at'     => $assemblyDay->finish_at->addSecond(),
                                'created_by' => auth()->id(),
                            ]
                        ]);


                        // __ Тут получили id уже нового СЗ
                        // __ Привязываем невыполненные линии к новому СЗ
                        // TODO: Warn!! Тут можно попробовать сделать одним запросом
                        $positionLine = 1;
                        foreach ($falseTask['false_lines'] as $line) {
                            $line->assembly_task_id = $newTask->id;
                            $line->position      = $positionLine++;
                            $line->save();
                        }

                        // __ Переносим на следующий день
                        $tasksToUpdate[] = [
                            'id'        => $newTask->id,
                            'action_at' => $nextChange->getManufactureDay()->startOfDay(),
                            'change'    => $nextChange->getChange(),
                            'position'  => $position++,
                        ];
                    }

                    // __ Добавляем существующие СЗ
                    foreach ($existingTasks as $task) {
                        $tasksToUpdate[] = [
                            'id'        => $task->id,
                            'action_at' => null,        // оставляем дату прежней
                            'change'    => null,        // оставляем дату прежней
                            'position'  => $position++,
                        ];
                    }

                    // __ Применяем изменения
                    AssemblysService::bulkUpdateTasks($tasksToUpdate);


                    // __ Делаем сквозную нумерацию заново в текущем дне и в том, куда перенесли
                    // __ Преобразуем в строки Y-m-d, чтобы array_unique отработал 100% корректно
                    $datesToReorder = array_unique([
                        $action_date->format('Y-m-d'),
                        $nextChange->getManufactureDay()->format('Y-m-d'),
                    ]);

                    //$datesToReorder = [
                    //    $action_date,
                    //    $nextChange->getManufactureDay(),
                    //];

                    // __ Используем unique(), чтобы не пересчитывать один и тот же день дважды (если перенесли в 2-ю смену того же дня)
                    foreach (array_unique($datesToReorder) as $targetDate) {

                        // __ Выбираем все СЗ на дату: сперва 1-я смена, затем 2-я; внутри смен — по position
                        $tasks = AssemblyTask::query()
                            ->whereDate('action_at', '>=', Carbon::parse($targetDate)->startOfDay())
                            ->whereDate('action_at', '<=', Carbon::parse($targetDate)->endOfDay())
                            //->whereDate('action_at', $targetDate)
                            ->orderBy('change', 'asc')
                            ->orderBy('position', 'asc')
                            ->get();

                        if ($tasks->isEmpty()) {
                            continue;
                        }

                        $position      = 1;
                        $tasksToUpdate = [];

                        // __ Формируем массив для сквозной перенумерации (1, 2, 3...)
                        foreach ($tasks as $task) {
                            $tasksToUpdate[] = [
                                'id'        => $task->id,
                                'action_at' => null, // оставляем прежней
                                'change'    => null, // оставляем прежней
                                'position'  => $position++,
                            ];
                        }

                        // __ Применяем изменения
                        AssemblysService::bulkUpdateTasks($tasksToUpdate);
                    }
                }
            });

            return new AssemblyDayResource($assemblyDay);
        } catch (Exception|Throwable $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }


    /**
     * ___ Установки маяка готовности к добавлению новых СЗ
     * @param Request $request
     * @return AssemblyDayResource|string
     */
    public function readySetAssemblyDay(Request $request)
    {
        try {
            $validated = $request->validate([
                'id' => 'required|integer|exists:assembly_days,id',
            ]);

            $assemblyDay        = AssemblyDay::query()->find($validated['id']);
            $assemblyDay->ready = true;

            $history = $assemblyDay->history;
            if (is_null($history)) {
                $history = [];
            }

            $history[]         = [
                'at'     => Carbon::now()->format(RETURN_DATE_TIME_FORMAT),
                'by'     => auth()->id(),
                'action' => 'Set ready for adding new Assembly Tasks',
            ];
            $assemblyDay->history = $history;

            $assemblyDay->save();

            return new AssemblyDayResource($assemblyDay);
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }

    /**
     * ___ Установки маяка готовности к добавлению новых СЗ
     * @param Request $request
     * @return AssemblyDayResource|string
     */
    public function readyUnsetAssemblyDay(Request $request)
    {
        try {
            $validated = $request->validate([
                'id' => 'required|integer|exists:assembly_days,id',
            ]);

            $assemblyDay        = AssemblyDay::query()->find($validated['id']);
            $assemblyDay->ready = false;

            $history = $assemblyDay->history;
            if (is_null($history)) {
                $history = [];
            }

            $history[]         = [
                'at'     => Carbon::now()->format(RETURN_DATE_TIME_FORMAT),
                'by'     => auth()->id(),
                'action' => 'Set unready for adding new Assembly Tasks',
            ];
            $assemblyDay->history = $history;

            $assemblyDay->save();

            return new AssemblyDayResource($assemblyDay);
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
    public function readyGetAssemblyDay(string $date, string $change)
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
            $data       = $validated->validated();
            $parsedDate = Carbon::parse($data['date']);
            $day        = AssemblyDay::query()
                ->where('change', $data['change'])
                ->whereDate('action_at', '>=', $parsedDate->startOfDay())
                ->whereDate('action_at', '<=', $parsedDate->endOfDay())
                ->first();

            return $day ? ['data' => !!$day->ready] : ['data' => false];
            //return new AssemblyDayResource($day);
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }

}
