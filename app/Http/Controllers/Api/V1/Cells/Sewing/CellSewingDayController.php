<?php

namespace App\Http\Controllers\Api\V1\Cells\Sewing;

use App\Classes\EndPointStaticRequestAnswer;
use App\Http\Controllers\Controller;
use App\Http\Resources\Manufacture\Cells\Sewing\Days\SewingDayResource;
use App\Models\Manufacture\Cells\Sewing\SewingDay;
use App\Models\Manufacture\Cells\Sewing\SewingTask;
use App\Models\Manufacture\Cells\Sewing\SewingTaskStatus;
use App\Services\Manufacture\SewingService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Throwable;

class CellSewingDayController extends Controller
{

    /**
     * ___ Возвращает производственный день по дате и смене
     * @param string $date
     * @param string $change
     * @return SewingDayResource|string
     */
    public function getSewingDayByDateAndChange(string $date, string $change)
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
            $day = SewingDay::findOrCreateByDateAndChange($date);

            $day = SewingDay::query()
                ->byDates($date)
                ->with(['workers', 'responsible'])
                ->first();

            // $day = SewingDay::query()
            //     ->with(['workers', 'responsible'])
            //     ->firstOrCreate([
            //         'action_at'     => Carbon::parse($date)->format(RETURN_DATE_TIME_FORMAT),
            //         'action_at_str' => $date,
            //         'change'        => $change,
            //     ]);

            return new SewingDayResource($day);
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }

    /**
     * ___ Устанавливает комментарий к производственному дню
     * @param Request $request
     * @return string
     */
    public function setSewingDayComment(Request $request)
    {
        try {
            $validated = $request->validate([
                'id'      => 'required|integer|exists:sewing_days,id',
                'comment' => 'present|nullable|string',
            ]);

            $sewingDay = SewingDay::query()->find($validated['id']);
            if (!$sewingDay) {
                throw new Exception('Missing sewing day with id: ' . $validated['id'] . '.');
            }

            $sewingDay->comment = $validated['comment'];
            $sewingDay->save();

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
    public function getSewingDaysByDates(Request $request)
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
                SewingDay::findOrCreateByDateAndChange($date);
            }

            $days = SewingDay::query()
                ->byDates($validated['dates'])
                ->with(['workers', 'responsible'])
                ->get();

            return SewingDayResource::collection($days);
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }


    /**
     * ___ Добавляет рабочего к производственному дню
     * @param Request $request
     * @return string
     */
    public function addWorkerToSewingDay(Request $request)
    {
        try {
            $validated = $request->validate([
                'day_id'    => 'required|integer|exists:sewing_days,id',
                'worker_id' => 'required|integer|exists:workers,id',
            ]);

            $sewingDay = SewingDay::query()->find($validated['day_id']);
            $sewingDay->workers()->syncWithoutDetaching([
                $validated['worker_id'] => ['working_time' => 8 * 60]  // __ ID рабочего и данные для pivot-таблицы
            ]);

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
    public function removeWorkerFromSewingDay(Request $request)
    {
        try {
            $validated = $request->validate([
                'day_id'    => 'required|integer|exists:sewing_days,id',
                'worker_id' => 'required|integer|exists:workers,id',
            ]);

            $sewingDay = SewingDay::query()->find($validated['day_id']);

            // __ Отвязываем рабочего от производственного дня
            $sewingDay->workers()->detach($validated['worker_id']);

            // __ Если рабочий был ответственным, то убираем его
            if ($sewingDay->responsible_id == $validated['worker_id']) {
                $sewingDay->responsible_id = null;
                $sewingDay->save();
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
    public function addResponsibleToSewingDay(Request $request)
    {
        try {
            $validated = $request->validate([
                'day_id'    => 'required|integer|exists:sewing_days,id',
                'worker_id' => 'required|integer|exists:workers,id',
            ]);

            $sewingDay = SewingDay::query()->find($validated['day_id']);
            $sewingDay->responsible_id = $validated['worker_id'];
            $sewingDay->save();

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
    public function removeResponsibleFromSewingDay(Request $request)
    {
        try {
            $validated = $request->validate([
                'day_id'    => 'required|integer|exists:sewing_days,id',
                'worker_id' => 'required|integer|exists:workers,id',
            ]);

            $sewingDay = SewingDay::query()->find($validated['day_id']);
            $sewingDay->responsible_id = null;
            $sewingDay->save();

            return EndPointStaticRequestAnswer::ok();
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }


    /**
     * ___ Стартуем СЗ производственного дня
     * @param Request $request
     * @return SewingDayResource|string
     */
    public function startSewingDay(Request $request)
    {
        try {
            $validated = $request->validate([
                'id' => 'required|integer|exists:sewing_days,id',
            ]);

            $sewingDay = SewingDay::query()->find($validated['id']);
            $sewingDay->start_at = now();

            // __ Сохраняем
            DB::transaction(function () use ($sewingDay) {
                $sewingDay->save();

                // __ Находим все СЗ, которые относятся к данному производственному дню и меняем их статус на "Выполняется"
                $action_date = Carbon::parse($sewingDay->start_at)->startOfDay();

                $pendingSewingTasks = SewingTask::query()
                    ->whereBetween('action_at', [
                        $action_date->startOfDay(),
                        $action_date->endOfDay()
                    ])
                    // ->whereDate('action_at', '<', $action_date)
                    ->byStatus(SewingTaskStatus::SEWING_STATUS_PENDING_ID)
                    ->with(['statuses',])
                    ->get();

                foreach ($pendingSewingTasks as $task) {

                    // __ Создаем запись в Статусе: Выполняется
                    $task->statuses()->syncWithoutDetaching([
                        SewingTaskStatus::SEWING_STATUS_RUNNING_ID => [
                            'set_at'     => $sewingDay->start_at,
                            'created_by' => auth()->id(),
                        ]
                    ]);
                }

            });

            return new SewingDayResource($sewingDay);
        } catch (Exception|Throwable $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }


    /**
     * ___ Заканчиваем СЗ производственного дня
     * @param Request $request
     * @return SewingDayResource|string
     */
    public function finishSewingDay(Request $request)
    {

        // __ Завершаем день со СЗ
        // __ 1. Находим производственный день
        // __ 2. Находим СЗ для этого Дня со статусом Выполняется
        // __ 3. Проверяем все Линии на статус Выполнено
        // __ 4. Закрываем СЗ


        try {
            $validated = $request->validate([
                'id' => 'required|integer|exists:sewing_days,id',
            ]);

            // __ Находим производственный день
            $sewingDay = SewingDay::query()
                ->find($validated['id']);
            $sewingDay->finish_at = now();

            // __ Сохраняем
            DB::transaction(function () use ($sewingDay) {
                $sewingDay->save();

                // __ Находим все СЗ, которые относятся к данному производственному дню и меняем их статус на "Выполняется"
                $action_date = Carbon::parse($sewingDay->start_at)->startOfDay();

                $pendingSewingTasks = SewingTask::query()
                    ->whereBetween('action_at', [
                        $action_date->startOfDay(),
                        $action_date->endOfDay()
                    ])
                    // ->whereDate('action_at', '<', $action_date)
                    ->byStatus(SewingTaskStatus::SEWING_STATUS_RUNNING_ID)
                    ->with(['statuses', 'sewingLines'])
                    ->get();


                // __ Собираем невыполненные СЗ
                $falseTasks = [];

                foreach ($pendingSewingTasks as $task) {

                    // __ Собираем невыполненный контент
                    $falseSewingLines = [];
                    $falseSewingLinesAmounts = 0;
                    $totalSewingLinesAmounts = 0;


                    foreach ($task->sewingLines as $line) {

                        // __ Проверка на то, чтобы строка была или выполнена или указана причина невыполнения
                        if (is_null($line->finished_at) && is_null($line->false_at)) {
                            throw new Exception('Missing done or false status for line with id: ' . $line->id);
                        }

                        // __ Собираем все невыполненные строчки
                        if (!is_null($line->false_at)) {
                            $falseSewingLines[] = $line;
                            $falseSewingLinesAmounts += $line->amount;
                        }

                        $totalSewingLinesAmounts += $line->amount;
                    }

                    // __ Если есть невыполненные - переносим на следующий день и ставим первыми
                    if (count($falseSewingLines) === 0) {

                        $falseTasks[] = [
                            'task'        => $task,
                            'false_lines' => $falseSewingLines,
                            // __ Все задания невыполненные
                            // __ Флаг, что нужно перенести все СЗ на другую дату
                            'all_false'   => $falseSewingLinesAmounts === $totalSewingLinesAmounts,
                        ];

                    }

                    // __ Создаем запись в Статусе: Выполнено
                    $task->statuses()->syncWithoutDetaching([
                        SewingTaskStatus::SEWING_STATUS_DONE_ID => [
                            'set_at'     => $sewingDay->finish_at,
                            'created_by' => auth()->id(),
                        ]
                    ]);

                }


                // __ Обрабатываем все невыполненное
                if (count($falseTasks) !== 0) {

                    // __ Получаем следующую смену
                    $nextChange = SewingService::getNextChange($sewingDay->action_at, $sewingDay->change);

                    // __ Получаем все СЗ следующей смены
                    $existingTasks = SewingTask::query()
                        ->whereBetween('action_at', [
                            $nextChange->getManufactureDay()->startOfDay(),
                            $nextChange->getManufactureDay()->endOfDay()
                        ])
                        ->orderBy('position')
                        ->get();

                    // __ Объединяем в один массив существующие СЗ и перенесенные
                    // __ из предыдущего дня, располагая а начале массива
                    // __ и перенумеровываем заново
                    $position      = 1;
                    $tasksToUpdate = [];

                    // __ Добавляем перенесенные СЗ
                    foreach ($falseTasks as $task) {

                        // __ Создаем новые СЗ и сохраняем в БД
                        $newTask = $task->replicate();
                        $newTask->position *= -1;
                        $newTask->save();
                        $newTask->position = $newTask->id * (-1);
                        $newTask->save();

                        // __ Тут получили id уже нового СЗ
                        // __ Привязываем невыполненные линии к новому СЗ
                        // TODO: Warn!! Тут можно попробовать сделать одним запросом
                        foreach ($falseTasks['false_lines'] as $line) {
                            $line->task_id = $newTask->id;
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
                    SewingService::bulkUpdateTasks($tasksToUpdate);
                }

            });

            return new SewingDayResource($sewingDay);
        } catch (Exception|Throwable $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }


}
