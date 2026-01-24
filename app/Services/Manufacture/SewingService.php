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


    // ___ Распределяем СЗ по Частям СЗ
    //
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
            ->with('lines')
            ->orderBy('action_at')      // __ Сортируем по дате пр-ва сначала
            ->orderBy('position')       // __ Потом по позиции в СЗ
            ->get();

        if (!$sewingTasks) {
            return false;
        } else {
            // __ Создаем СЗ из Заявки, если по какой-то причине оно отсутствует
            // $sewingTask = self::createSewingTaskFromOrderId($orderId); // __ Пока оставляем так
        }

        // __ Распределяем содержимое Заявки по Частям СЗ

        $sewingTaskContent = [];
        $idx               = 0;
        foreach ($sewingTasks as $key => $sewingTask) {
            if (!isset($sewingTaskContent[$key])) { // __ Если еще не создан массив для Части СЗ (не сработало нижнее условие)
                $sewingTaskContent[$key] = [];      // __ Создаем пустой массив для Части СЗ (инициализируем)
            }

            $maxElements = $sewingTask->lines->sum('amount');  // __ Получаем максимальное количество элементов в Части СЗ

            if ($key === count($sewingTasks) - 1) {            // __ Если это последняя Часть СЗ
                while ($idx < count($order->lines)) {          // __ Пока не закончатся строки в Заявке (Запихиваем все до конца)
                    $sewingTaskContent[$key][] = $order->lines[$idx++];
                }
            } else {
                $currentElements = 0;                          // __ Текущее количество элементов в Части СЗ
                while ($currentElements + $order->lines[$idx]->amount < $maxElements && $idx < count($order->lines)) {
                    $sewingTaskContent[$key][] = $order->lines[$idx++];
                    $currentElements           += $order->lines[$idx]->amount;
                }

                if ($idx < count($order->lines)) {          // __ Если остались строки в Заявке, но не хватило места в Части СЗ (вторая часть условия)
                    if ($currentElements < $maxElements) {  // __ Если текущее количество элементов в Части СЗ не равно максимальному (есть куда пихать)

                        // __ Разбиваем количество на 2 части
                        $newItem                   = clone $order->lines[$idx];
                        $newItem->amount           = $maxElements - $currentElements;
                        $sewingTaskContent[$key][] = $newItem;

                        $transitionItem                = clone $order->lines[$idx];
                        $transitionItem->amount        = $order->lines[$idx]->amount - $newItem->amount;
                        $sewingTaskContent[$key + 1][] = $transitionItem;

                        $idx++;
                    }
                }
            }
        }

        // __ Записываем содержимое в Части СЗ в БД
        foreach ($sewingTasks as $key => $sewingTask) {
            if (isset($sewingTaskContent[$key])) {
                $position = 1;
                foreach ($sewingTaskContent[$key] as $line) {
                    // __ Получаем трудозатраты
                    /** @noinspection DuplicatedCode */
                    $sewingTimeLabor = new SewingTimeLabor(
                        model: $line->model_code_1c,
                        size: $line->size,
                        amount: $line->amount,
                    );

                    $createdTaskLine = SewingTaskLine::query()->create([
                        'sewing_task_id'             => $sewingTask->id,
                        'order_line_id'              => $line->id,
                        'amount'                     => $line->amount,
                        'position'                   => $position++,
                        'time_labor'                 => $sewingTimeLabor->getTimeLaborArray(), // __ Записываем общие трудозатраты в jsonb в БД
                        SewingTask::FIELD_UNIVERSAL  => $sewingTimeLabor->getTimeUniversal(),
                        SewingTask::FIELD_AUTO       => $sewingTimeLabor->getTimeAuto(),
                        SewingTask::FIELD_SOLID_HARD => $sewingTimeLabor->getTimeSolidHard(),
                        SewingTask::FIELD_SOLID_LITE => $sewingTimeLabor->getTimeSolidLite(),
                        SewingTask::FIELD_UNDEFINED  => $sewingTimeLabor->getTimeUndefined(),

                    ]);
                }
            } else {
                $sewingTask->delete();
            }
        }


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
