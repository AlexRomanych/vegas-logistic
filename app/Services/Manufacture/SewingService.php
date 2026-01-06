<?php

namespace App\Services\Manufacture;


use App\Models\Manufacture\Cells\Sewing\SewingTask;
use App\Models\Manufacture\Cells\Sewing\SewingTaskLine;
use App\Models\Manufacture\Cells\Sewing\SewingTaskStatus;
use App\Models\Order\Order;
use App\Services\BusinessProcessesService;
use App\Services\ModelsService;
use Exception;
use Illuminate\Support\Facades\DB;
use Throwable;

final class SewingService
{

    /**
     * ___ Создать СЗ для Пошива из основного Заказа
     * @param int $orderId ID основного Заказа
     * @param string|null $plannedDate Дата планируемого выполнения СЗ - должна быть либо дата, либо смещение, приоритет - дата
     * @return SewingTask|null
     * @throws Throwable
     */
    public static function createSewingTaskFromOrderId(
        int         $orderId,
        string|null $plannedDate = null): ?SewingTask
    {

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
        DB::transaction(function () use ($order, $plannedDate, &$createdTask) {

            // __ Создаем СЗ
            $createdTask = SewingTask::query()->create([
                'action_at' => $plannedDate,
                'order_id'  => $order->id,
                'position'  => 1,       // __ Устанавливаем текущую позицию в 1
            ]);
            if (!$createdTask) {
                throw new Exception('Failed to create SewingTask');
            }

            // __ Создаем контент (строки) СЗ
            foreach ($order->lines as $line) {
                $createdTaskLine = SewingTaskLine::query()->create([
                    'sewing_task_id' => $createdTask->id,
                    'order_line_id'  => $line->id,
                    'amount'         => $line->amount,
                ]);
            }

            // __ Создаем запись в Статусе: Создано
            $createdTask->statuses()->attach([
                SewingTaskStatus::SEWING_STATUS_CREATED_ID => [
                    'set_at'     => now(),
                    'created_by' => auth()->id(),
                ]
            ]);

        });

        return $createdTask;
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
                    $createdTaskLine = SewingTaskLine::query()->create([
                        'sewing_task_id' => $sewingTask->id,
                        'order_line_id'  => $line->id,
                        'amount'         => $line->amount,
                        'position'       => $position++,
                    ]);
                }
            } else {
                $sewingTask->delete();
            }
        }





        return true;
    }
}
