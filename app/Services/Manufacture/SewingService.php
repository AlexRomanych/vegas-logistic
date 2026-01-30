<?php

namespace App\Services\Manufacture;


use App\Classes\EndPointStaticRequestAnswer;
use App\Classes\SewingTimeLabor;
use App\Models\Manufacture\Cells\Sewing\SewingTask;
use App\Models\Manufacture\Cells\Sewing\SewingTaskLine;
use App\Models\Manufacture\Cells\Sewing\SewingTaskStatus;
use App\Models\Order\Order;
use App\Services\BusinessProcessesService;
use App\Services\ModelsService;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Throwable;

final class SewingService
{

    /**
     * ___ Создать СЗ для Пошива из основного Заказа
     * @param  int  $orderId  ID основного Заказа
     * @param  string|null  $plannedDate  Дата планируемого выполнения СЗ - должна быть либо дата, либо смещение, приоритет - дата
     * @return SewingTask|null
     * @throws Throwable
     */
    public static function createSewingTaskFromOrderId(
        int $orderId,
        string|null $plannedDate = null
    ): ?SewingTask {
        // try {
        // __ Проверяем на существование заказа
        $order = Order::query()->with(['lines', 'client'])->find($orderId);
        if (!$order) {
            return null;
        }

        if (!(is_null($plannedDate) || $plannedDate === '')) {
            $plannedDate = normalizeToCarbon($plannedDate);
        } else {
            // __ Получаем смещение в днях для Пошива
            $offset      = BusinessProcessesService::getDateOffsetForOrderMovingProcessByNodeIdAndClientId(SEWING_NODE_ID, $order->client->id);
            $plannedDate = normalizeToCarbon($order->load_at)->addDays($offset);
        }

        $createdTask = null;
        // DB::transaction(function () use ($order, $plannedDate, &$createdTask) {

        // __ Создаем СЗ
        $createdTask = SewingTask::query()->create([
            'action_at' => $plannedDate,
            'order_id'  => $order->id,
            'position'  => self::getSewingTaskLastPositionInDay($plannedDate) + 1, // __ Получаем позицию для новой СЗ
        ]);
        if (!$createdTask) {
            throw new Exception('Failed to create SewingTask');
        }

        // __ Создаем контент (строки) СЗ
        $position = 1;
        foreach ($order->lines as $line) {
            // __ Если это расчетная модель (AVERAGE), то ставим позицию 0
            // if ($order->lines->count() === 1 && ModelsService::isElementAverage($line->model_code_1c)) {
            //     $position = 0;
            // }

            // __ Получаем трудозатраты
            /** @noinspection DuplicatedCode */
            $sewingTimeLabor = new SewingTimeLabor(
                model: $line->model_code_1c,
                size: $line->size,
                amount: $line->amount,
            );


            // __ !!! Новая логика, когда в СЗ записываем отдельные трудозатраты в одно поле time для каждой ШМ + добавляем подмену свойств
            // __ Для каждой ШМ записываем отдельно
            $sewingMachines = [
                SewingTask::FIELD_UNIVERSAL  => [
                    'time'   => $sewingTimeLabor->getTimeUniversal(),
                    'amount' => $sewingTimeLabor->getAmountUniversal()
                ],
                SewingTask::FIELD_AUTO       => [
                    'time'   => $sewingTimeLabor->getTimeAuto(),
                    'amount' => $sewingTimeLabor->getAmountAuto()
                ],
                SewingTask::FIELD_SOLID_HARD => [
                    'time'   => $sewingTimeLabor->getTimeSolidHard(),
                    'amount' => $sewingTimeLabor->getAmountSolidHard()
                ],
                SewingTask::FIELD_SOLID_LITE => [
                    'time'   => $sewingTimeLabor->getTimeSolidLite(),
                    'amount' => $sewingTimeLabor->getAmountSolidLite()
                ],
                SewingTask::FIELD_UNDEFINED  => [
                    'time'   => $sewingTimeLabor->getTimeUndefined(),
                    'amount' => $sewingTimeLabor->getAmountUndefined()
                ],
            ];

            foreach ($sewingMachines as $machine => $data) {
                if ($data['amount'] < 1) {
                    continue;
                }
                $createdTaskLine = SewingTaskLine::query()->create([
                    'sewing_task_id' => $createdTask->id,
                    'order_line_id'  => $line->id,
                    'amount'         => $data['amount'],
                    'position'       => $position++,
                    'time'           => $data['time'],

                    // __ Задаем подмену свойств
                    'phantom'        => $machine,
                    'phantom_json'   => ['is_'.$machine => true],

                ]);
            }

            // __ !!! Старая логика, когда в СЗ записывали общие трудозатраты
            // $createdTaskLine = SewingTaskLine::query()->create([
            //     'sewing_task_id'             => $createdTask->id,
            //     'order_line_id'              => $line->id,
            //     'amount'                     => $line->amount,
            //     'position'                   => $position++,
            //     'time_labor'                 => $sewingTimeLabor->getTimeLaborArray(), // __ Записываем общие трудозатраты в jsonb в БД
            //
            //     // __ Эти поля убрал
            //     // SewingTask::FIELD_UNIVERSAL  => $sewingTimeLabor->getTimeUniversal(),
            //     // SewingTask::FIELD_AUTO       => $sewingTimeLabor->getTimeAuto(),
            //     // SewingTask::FIELD_SOLID_HARD => $sewingTimeLabor->getTimeSolidHard(),
            //     // SewingTask::FIELD_SOLID_LITE => $sewingTimeLabor->getTimeSolidLite(),
            //     // SewingTask::FIELD_UNDEFINED  => $sewingTimeLabor->getTimeUndefined(),
            // ]);
        }

        // __ Создаем запись в Статусе: Создано
        $createdTask->statuses()->attach([
            SewingTaskStatus::SEWING_STATUS_CREATED_ID => [
                'set_at'     => now(),
                'created_by' => auth()->id(),
            ]
        ]);

        // });
        return $createdTask;
        // } catch (Exception|Throwable $e) {
        //     return EndPointStaticRequestAnswer::fail($e);
        // }


    }


    /**
     *  ___ Распределяем СЗ по Частям СЗ
     *  ___ Сюда приходим тогда, когда есть прогнозное СЗ, а поверх него загружаем Заявку
     * @param  int  $orderId
     * @return bool
     * @throws Throwable
     */
    public static function distributeSewingTaskFromOrderId(int $orderId): bool
    {

        // __ Получаем саму Заявку
        $order = Order::query()
            ->with(['lines', 'client'])
            ->find($orderId);

        if (!$order) {
            return false;
        }

        // __ Получаем СЗ. Оно тут должно быть
        $sewingTasks = SewingTask::query()
            ->where('order_id', $orderId)
            ->with('sewingLines')
            ->orderBy('action_at')      // __ Сортируем по дате пр-ва сначала
            ->orderBy('position')       // __ Потом по позиции в СЗ
            ->get();

        if (!$sewingTasks) {
            return false;
        } else {
            // __ Создаем СЗ из Заявки, если по какой-то причине оно отсутствует
            // $sewingTask = self::createSewingTaskFromOrderId($orderId); // __ Пока оставляем так
        }

        // ___ Первый проход. Распределяем содержимое Заявки по Частям СЗ

        // __ Разбиваем Заявку на ШМ
        $universals = [];
        $autos      = [];
        $solidHards = [];
        $solidLites = [];
        $unknowns   = [];

        foreach ($order->lines as $orderLine) {
            $model = ModelsService::getModelByCode1C($orderLine->model_code_1c);

            if (!ModelsService::isElementAverage($model)) {
                match ($model->machine_type) {
                    SewingTask::FIELD_UNIVERSAL  => $universals[] = $orderLine,
                    SewingTask::FIELD_AUTO       => $autos[] = $orderLine,
                    SewingTask::FIELD_SOLID_HARD => $solidHards[] = $orderLine,
                    SewingTask::FIELD_SOLID_LITE => $solidLites[] = $orderLine,
                    default                      => $unknowns[] = $orderLine,
                };

            }
        }

        $FIELD_DATA   = 'data';
        $FIELD_MEMORY = 'memory';
        $FIELD_MERGED = 'merged';

        $sewingTaskContent = [];
        // $idx               = 0;
        foreach ($sewingTasks as $key => $sewingTask) {

            foreach ($sewingTask->sewingLines as $sewingLine) {

                switch ($sewingLine->phantom):
                    case SewingTask::FIELD_UNIVERSAL:
                        $workField = SewingTask::FIELD_UNIVERSAL;
                        $workArray = &$universals;
                        break;
                    case SewingTask::FIELD_AUTO:
                        $workField = SewingTask::FIELD_AUTO;
                        $workArray = &$autos;
                        break;
                    case SewingTask::FIELD_SOLID_HARD:
                        $workField = SewingTask::FIELD_SOLID_HARD;
                        $workArray = &$solidHards;
                        break;
                    case SewingTask::FIELD_SOLID_LITE:
                        $workField = SewingTask::FIELD_SOLID_LITE;
                        $workArray = &$solidLites;
                        break;
                    default:
                        $workField = SewingTask::FIELD_UNDEFINED;
                        $workArray = &$unknowns;
                endswitch;

                // __ Запоминаем расчетную информацию для данной ШМ
                if (!isset($sewingTaskContent[$key][$workField][$FIELD_MEMORY])) {
                    $sewingTaskContent[$key][$workField][$FIELD_MEMORY] = $sewingLine->amount;
                }

                $lineAmount = $sewingTaskContent[$key][$workField][$FIELD_MEMORY];

                $data = [];

                // __ Если есть что распределять и есть куда распределять
                while ($lineAmount > 0 && count($workArray) > 0) {

                    $item = array_pop($workArray);

                    if ($item->amount <= $lineAmount) {
                        // __ Если есть куда распределить
                        $data[]     = $item;
                        $lineAmount -= $item->amount;
                    } else {
                        // __ Если не хватает для распределения
                        $newItem         = clone $item; // Создаем копию, чтобы изменения не затронули оригинал
                        $newItem->amount = $lineAmount;

                        $data[] = $newItem;

                        $item->amount -= $lineAmount;    // Уменьшаем остаток в оригинале
                        $workArray[]  = $item;           // Возвращаем остаток в массив

                        $lineAmount = 0;
                    }
                }

                // __ Записываем распределение
                $sewingTaskContent[$key][$workField][$FIELD_DATA] = $data;

                // __ Записывает текущий остаток для следующего распределения
                $sewingTaskContent[$key][$workField][$FIELD_MEMORY] = $lineAmount;
            }
        }

        // ___ Второй проход. Распределяем все, что осталось

        // __ Складываем все в одну кучу
        $rest = array_merge($universals, $autos, $solidHards, $solidLites, $unknowns);

        if (count($rest) > 0) {
            foreach ($sewingTasks as $key => $sewingTask) {

                foreach ($sewingTask->sewingLines as $sewingLine) {

                    $workField = match ($sewingLine->phantom) {
                        SewingTask::FIELD_UNIVERSAL  => SewingTask::FIELD_UNIVERSAL,
                        SewingTask::FIELD_AUTO       => SewingTask::FIELD_AUTO,
                        SewingTask::FIELD_SOLID_HARD => SewingTask::FIELD_SOLID_HARD,
                        SewingTask::FIELD_SOLID_LITE => SewingTask::FIELD_SOLID_LITE,
                        default                      => SewingTask::FIELD_UNDEFINED,
                    };

                    $lineAmount = $sewingTaskContent[$key][$workField][$FIELD_MEMORY];

                    // __ Если есть что распределять и есть куда распределять
                    while ($lineAmount > 0 && count($rest) > 0) {

                        $item = array_pop($rest);

                        if ($item->amount <= $lineAmount) {
                            // __ Если есть куда распределить
                            $sewingTaskContent[$key][$workField][$FIELD_DATA][] = $item;

                            $lineAmount -= $item->amount;
                        } else {
                            // __ Если не хватает для распределения
                            $newItem         = clone $item; // Создаем копию, чтобы изменения не затронули оригинал
                            $newItem->amount = $lineAmount;

                            $sewingTaskContent[$key][$workField][$FIELD_DATA][] = $newItem;

                            $item->amount -= $lineAmount;       // Уменьшаем остаток в оригинале
                            $rest[]       = $item;              // Возвращаем остаток в массив []

                            $lineAmount = 0;
                        }
                    }

                    $sewingTaskContent[$key][$workField][$FIELD_MEMORY] = $lineAmount;
                }

            }
        }

        // ___ Третья попытка, когда реальная заявка больше плановой и есть остаток

        // __ Сгружаем в первую часть
        if (count($rest) > 0) {
            $sewingTaskContent[0][SewingTask::FIELD_UNIVERSAL][$FIELD_DATA] =
                array_merge($sewingTaskContent[0][SewingTask::FIELD_UNIVERSAL][$FIELD_DATA], $rest);
        }

        // __ Теперь нужно объединить все те Lines, которые были разбиты на несколько и попали в одну часть СЗ
        foreach ($sewingTasks as $key => $sewingTask) {

            $merged = [];

            foreach ($sewingTask->sewingLines as $sewingLine) {

                $workField = match ($sewingLine->phantom) {
                    SewingTask::FIELD_UNIVERSAL  => SewingTask::FIELD_UNIVERSAL,
                    SewingTask::FIELD_AUTO       => SewingTask::FIELD_AUTO,
                    SewingTask::FIELD_SOLID_HARD => SewingTask::FIELD_SOLID_HARD,
                    SewingTask::FIELD_SOLID_LITE => SewingTask::FIELD_SOLID_LITE,
                    default                      => SewingTask::FIELD_UNDEFINED,
                };

                // __ Сгружаем в кучу
                $merged = array_merge($merged, $sewingTaskContent[$key][$workField][$FIELD_DATA]);
            }

            $result = [];

            foreach ($merged as $line) {
                $code = $line->id;

                if (!isset($result[$code])) {
                    // __ Если такой код встретили впервые — клонируем объект,
                    // __ чтобы не испортить оригинал в исходном массиве
                    $result[$code] = clone $line;
                } else {
                    // __ Если код уже есть — просто прибавляем количество
                    $result[$code]->amount += $line->amount;
                }
            }

            // __ Сбрасываем ключи, чтобы вернуть обычный индексированный массив [0, 1, 2...]
            $sewingTaskContent[$key][$FIELD_MERGED] = array_values($result);
        }

        // __ Сохраняем содержимое
        DB::transaction(function () use ($sewingTasks, $sewingTaskContent, $FIELD_MERGED) {

            // __ Заводим позиции записей в СЗ в отрицательную зону
            // __ Или удаляем
            foreach ($sewingTasks as $key => $sewingTask) {
                foreach ($sewingTask->sewingLines as $sewingLine) {
                    $sewingLine->delete();
                }
            }

            // __ Записываем содержимое в Части СЗ в БД
            foreach ($sewingTasks as $key => $sewingTask) {
                if (isset($sewingTaskContent[$key])) {
                    $position = 1;

                    if (isset($sewingTaskContent[$key][$FIELD_MERGED])) {
                        foreach ($sewingTaskContent[$key][$FIELD_MERGED] as $line) {

                            // __ Получаем трудозатраты
                            /** @noinspection DuplicatedCode */
                            $sewingTimeLabor = new SewingTimeLabor(
                                model: $line->model_code_1c,
                                size: $line->size,
                                amount: $line->amount,
                            );

                            $createdTaskLine = SewingTaskLine::query()->create([
                                'sewing_task_id' => $sewingTask->id,
                                'order_line_id'  => $line->id,
                                'amount'         => $line->amount,
                                'position'       => $position++,
                                'time'           => $sewingTimeLabor->getTime(),
                            ]);

                        }
                    }
                } else {
                    $sewingTask->delete();
                }
            }

            return true;
        });

        return true;
    }


    /**
     * ___ Получаем позицию последнего СЗ в дне
     * @param  string|Carbon|null  $date  Дата нужного дня
     * @return int
     */
    public static function getSewingTaskLastPositionInDay(string|Carbon $date = null): int
    {
        if (is_null($date) || $date === '') {
            return 0;
        }

        $date = normalizeToCarbon($date);

        return SewingTask::query()
            ->whereDate('action_at', $date)
            ->count();
        // return SewingTask::query()->whereDate('action_at', $date)->max('position');
    }
}
