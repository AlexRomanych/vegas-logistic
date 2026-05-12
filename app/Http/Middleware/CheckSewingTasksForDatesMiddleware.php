<?php

namespace App\Http\Middleware;

use App\Models\Manufacture\Cells\Sewing\SewingTask;
use App\Models\Manufacture\Cells\Sewing\SewingTaskStatus;
use App\Services\Manufacture\SewingService;
use Carbon\Carbon;
use Closure;
use Exception;
use Illuminate\Http\Request;
use Log;
use Symfony\Component\HttpFoundation\Response;
use Throwable;



/*
 * ___ Middleware для синхронизации производственного плана
 * ___ Находим все задачи, которые должны быть выполнены до текущего момента, но не выполнены
 * ___ и пихаем в текущую дату
 */
class CheckSewingTasksForDatesMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     * @throws Throwable
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $nowDate = Carbon::now();

            // __ Выбираем все задачи, с датой выполнения меньше текущей даты
            $statusOrder = [
                SewingTaskStatus::SEWING_STATUS_PENDING_ID,
                SewingTaskStatus::SEWING_STATUS_ROLLING_ID,
                SewingTaskStatus::SEWING_STATUS_CREATED_ID,
            ];

            $sewingTasksBefore = SewingTask::query()
                ->byStatus($statusOrder)
                ->with('latestTaskStatusByDate') // Грузим связь, чтобы не было N+1
                ->whereDate('action_at', '<', $nowDate)
                ->get()
                ->sort(function ($a, $b) use ($statusOrder) {

                    // __ 1. Извлекаем ID текущего статуса
                    $statusA = $a->latestTaskStatusByDate->sewing_task_status_id;
                    $statusB = $b->latestTaskStatusByDate->sewing_task_status_id;

                    // 1. Извлекаем ID текущего статуса (предполагаем, что связь возвращает коллекцию)
                    //$statusA = $a->latestTaskStatusByDate->first()?->id;
                    //$statusB = $b->latestTaskStatusByDate->first()?->id;

                    // __ Определяем позиции в массиве приоритетов
                    $posA = array_search($statusA, $statusOrder);
                    $posB = array_search($statusB, $statusOrder);

                    // __ Обработка случая, если статус не найден в массиве (отправляем в конец)
                    $posA = ($posA === false) ? 999 : $posA;
                    $posB = ($posB === false) ? 999 : $posB;

                    // __ Сравнение по статусу
                    if ($posA !== $posB) {
                        return $posA <=> $posB;
                    }

                    // __ 2. Сравнение по дате (action_at)
                    if ($a->action_at->ne($b->action_at)) {
                        return $a->action_at <=> $b->action_at;
                    }

                    // __ 3. Сравнение по позиции
                    return $a->position <=> $b->position;
                })
                ->values(); // Сбрасываем ключи после сортировки


            // __ Почему это работает именно так:
            // __ FIELD(status_id, 1, 2, 3): Эта функция MySQL возвращает индекс (1, 2, 3...) позиции значения в списке.
            // __ Таким образом, записи со статусом CREATED_ID получат 1, ROLLING_ID — 2 и так далее.
            // __ Это и обеспечивает ваш "ручной" порядок.

            // __ Приоритетность: Laravel (и SQL) применяет сортировки строго в том порядке, в котором они написаны в коде.
            // __ Сначала сгруппирует по статусам, потом внутри каждой группы — по времени, и напоследок — по position.

            // __ Стабильность: Добавление position и action_at в конец цепочки гарантирует, что если у двух задач
            // __ одинаковый статус, они не будут «прыгать» при каждом обновлении страницы.

            // __ Список статусов, в порядке приоритета
            //$statusOrder = [
            //    SewingTaskStatus::SEWING_STATUS_PENDING_ID,
            //    SewingTaskStatus::SEWING_STATUS_ROLLING_ID,
            //    SewingTaskStatus::SEWING_STATUS_CREATED_ID,
            //];

            //$sewingTasksBefore = SewingTask::query()
            //    ->byStatus($statusOrder)
            //    ->with('latestTaskStatus')
            //    ->whereDate('action_at', '<', $nowDate)
            //    // 1. Сортировка по весу статуса (в порядке из массива $statusOrder)
            //    ->orderByRaw('FIELD(status_id, ' . implode(',', $statusOrder) . ')')
            //    // 2. Сортировка по дате внутри статуса
            //    ->orderBy('action_at', 'asc')
            //    // 3. Сортировка по позиции внутри даты
            //    ->orderBy('position', 'asc')
            //    ->get();

            // __ debug
            //$a = $sewingTasksBefore->toArray();
            //$b = 0;

            //$sewingTasks = SewingTask::query()
            //    ->byStatus([
            //        SewingTaskStatus::SEWING_STATUS_CREATED_ID,
            //        SewingTaskStatus::SEWING_STATUS_ROLLING_ID,
            //        SewingTaskStatus::SEWING_STATUS_PENDING_ID,
            //    ])
            //    ->whereDate('action_at', '<', $nowDate)
            //    ->get();

            // __ Если что-то нашли, то формируем текущий производственный день
            if ($sewingTasksBefore->count() > 0) {

                // __ Получаем задачи на текущий день
                $sewingTasksNow = SewingTask::query()
                    ->whereDate('action_at', '>=', $nowDate->startOfDay())
                    ->whereDate('action_at', '<=', $nowDate->endOfDay())
                    ->with('latestTaskStatusByDate') // Грузим связь, чтобы не было N+1
                    ->orderBy('position', 'asc')
                    ->get();


                // __ Объединяем по следующим правилам:
                // __ 0. В sewingTasksBefore дату action_at заменить на текущую
                // __ 1. При равных данных приоритет у коллекции sewingTasksBefore (элементы будут стоять выше при равных условиях)
                // __ 2. Потом Отсортировать по статусу в указанном порядке
                // __ 3. Потом отсортировать по position

                // __ source_priority: Это «костыль» для выполнения условия №1. Так как ты меняешь дату в первой коллекции,
                // __ они становятся «равными» по времени с текущими. Флаг гарантирует, что старые задачи всегда будут выше новых при прочих равных.

                // __ array_search: Внутри sort мы определяем индекс статуса. Чем меньше индекс, тем выше задача.

                // __ Сцепка (concat): Мы не теряем данные и сохраняем объекты моделей со всеми их методами.

                // __ 1. Подготавливаем коллекцию "До" (меняем дату)
                $sewingTasksBefore->transform(function ($item) use ($nowDate) {
                    $item->action_at = $nowDate->startOfDay()->format('Y-m-d H:i:s');
                    $item->source_priority = 1; // Добавляем виртуальный флаг приоритета для стабильной сортировки
                    return $item;
                });

                // __ 2. Помечаем текущую коллекцию
                $sewingTasksNow->transform(function ($item) {
                    $item->source_priority = 2;
                    return $item;
                });

                // __ 3. Объединяем и сортируем
                $statusOrder = [
                    SewingTaskStatus::SEWING_STATUS_DONE_ID,
                    SewingTaskStatus::SEWING_STATUS_RUNNING_ID,
                    SewingTaskStatus::SEWING_STATUS_PENDING_ID,
                    SewingTaskStatus::SEWING_STATUS_ROLLING_ID,
                    SewingTaskStatus::SEWING_STATUS_CREATED_ID,
                ];

                $combinedTasks = $sewingTasksBefore->concat($sewingTasksNow)
                    ->sort(function ($a, $b) use ($statusOrder) {
                        // __ Условие 2: Сортировка по статусу (в указанном порядке)
                        $posA = array_search($a->latestTaskStatusByDate->sewing_task_status_id, $statusOrder);
                        $posB = array_search($b->latestTaskStatusByDate->sewing_task_status_id, $statusOrder);

                        // __ Если статусы разные, сортируем по их позиции в массиве
                        if ($posA !== $posB) {
                            return $posA <=> $posB;
                        }

                        // __ Условие 1: Приоритет у коллекции "Before" (source_priority: 1 < 2)
                        if ($a->source_priority !== $b->source_priority) {
                            return $a->source_priority <=> $b->source_priority;
                        }

                        // __ Условие 3: Сортировка по position
                        return $a->position <=> $b->position;
                    })
                    ->values(); // Сбрасываем ключи коллекции

                // __ 3. Меняем позиции
                $position = 1;
                $combinedTasks->transform(function ($item) use (&$position) {
                    $item->position = $position++;
                    return $item;
                });

                // ___ Формат входных данных для обновления:
                // $tasksToUpdate[] = [
                //     'id'        => taskId,
                //     'action_at' => new_action_at ?? null,
                //     'position'  => new_position ?? null,
                // ];

                $tasksToUpdate = $combinedTasks->map(function ($item) {
                    return [
                        'id'        => $item->id,
                        'action_at' => $item->action_at,
                        'position'  => $item->position,
                    ];
                })->toArray();

                // __ Обновляем SewingTasks текущего дня
                SewingService::bulkUpdateTasks($tasksToUpdate);
            }
        } catch (Throwable|Exception $e) {
            //report($e); // Отправит ошибку в лог или Sentry
            //return $next($request);

            Log::critical($e->getMessage());
            // Выбрасываем ошибку, которую перехватит Handler и покажет красивое сообщение
            abort(500, $e->getMessage());
            //abort(500, 'Ошибка синхронизации производственного плана. Пожалуйста, обратитесь к администратору.');
        }

        return $next($request);
    }
}
