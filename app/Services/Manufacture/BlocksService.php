<?php
/** @noinspection DuplicatedCode */

namespace App\Services\Manufacture;


use App\Classes\ManufactureDayAndChange;
use App\Models\Manufacture\Cells\Block\Block;
use App\Models\Manufacture\Cells\Block\BlockCollection;
use App\Models\Manufacture\Cells\Block\BlockTask;
use App\Models\Manufacture\Cells\Block\BlockTaskLine;
use App\Models\Manufacture\Cells\Block\BlockTaskStatus;
use App\Models\Models\ModelConstruct;
use App\Models\Order\Order;
use App\Services\BusinessProcessesService;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Throwable;

final class BlocksService
{
    private static array $blocksCacheCode1c = [];
    private static array $blockCollectionsCacheCode1c = [];

    // ___ Получаем Блок по коду 1С
    public static function getBlockByCode1c(string $code1c): ?Block
    {
        if (count(self::$blocksCacheCode1c) === 0) {
            self::getBlocks();
        }

        if (isset(self::$blocksCacheCode1c[$code1c])) {
            return self::$blocksCacheCode1c[$code1c];
        }

        return null;
    }

    // ___ Кэштруем Блоки
    private static function getBlocks(): void
    {
        try {
            $blocks = Block::query()
                ->with(['blockCollection'])
                ->get();

            foreach ($blocks as $block) {
                self::$blocksCacheCode1c[$block->code_1c] = $block;
            }
        } catch (Exception $e) {
            self::$blocksCacheCode1c = [];
        }
    }

    // ___ Получаем Коллекцию Блоков по коду 1С
    public static function getBlockCollectionByCode1c(string $code1c): ?BlockCollection
    {
        if (count(self::$blockCollectionsCacheCode1c) === 0) {
            self::getBlockCollections();
        }

        if (isset(self::$blockCollectionsCacheCode1c[$code1c])) {
            return self::$blockCollectionsCacheCode1c[$code1c];
        }

        return null;
    }

    // ___ Кэштруем Коллекцию Блоков
    private static function getBlockCollections(): void
    {
        try {
            $blockCollections = BlockCollection::all();
            foreach ($blockCollections as $collection) {
                self::$blockCollectionsCacheCode1c[$collection->code_1c] = $collection;
            }
        } catch (Exception $e) {
            self::$blockCollectionsCacheCode1c = [];
        }
    }


    /**
     * ___ Создаем СЗ для Блоков по orderId
     * @param int $orderId
     * @param string|null $plannedDate
     * @return BlockTask|null
     * @throws Throwable
     * @noinspection PhpUndefinedFieldInspection
     */
    public static function createBlockTaskFromOrderId(
        int $orderId,
        string|null $plannedDate = null,
    ): ?BlockTask {
        //try {


        // __ Проверяем на существование заказа
        // __ TODO Доработать выборку данных (убрать не нужные)
        $order = Order::query()->with([/*'lines', */ 'client'])->find($orderId);
        if (!$order) {
            return null;
        }

        //$orderDebug = $order->toArray();


        // __ Получаем плоский массив кодов 1С Активных Блоков СП
        $blockCodes = Block::query()->own()->pluck('code_1c')->toArray();
        if (count($blockCodes) === 0) {
            return null;
        }


        // __ Плучаем Расход по блокам с сортировкой по приоритету исполнения
        $groupedPivotRecordsExpense = DB::table('order_line_material_pivot as pivot')
            ->join('order_lines as lines', 'lines.id', '=', 'pivot.order_line_id')
            ->join('blocks', 'blocks.code_1c', '=', 'pivot.material_code_1c')
            ->join('block_collections', 'blocks.collection', '=', 'block_collections.code_1c')
            ->where('lines.order_id', $orderId)
            ->whereIn('pivot.detail', [ModelConstruct::DETAIL_CONSTRUCT_BASE_BLOCK])
            ->whereIn('pivot.material_code_1c', $blockCodes)
            // Перечисляем только те поля, которые нам реально нужны:
            ->select([
                'pivot.order_line_id',
                'pivot.material_code_1c', // ⚠️ КРИТИЧЕСКИ ВАЖНО для последующего groupBy!
                'pivot.detail',
                'pivot.expense',
                'pivot.rest',
                'lines.amount',
                'blocks.name',
                'block_collections.priority',
                //'block_collections.code_1c as collection_code_1c',

                //'pivot.quantity' — например, какое-то еще твое поле
                //'pivot.material_name_expense',
                //'pivot.position',
            ])
            ->orderBy('block_collections.priority', 'asc')
            ->get()
            ->groupBy('material_code_1c')
            ->toArray();

        $a = 0;


        // __ Получаем плановую дату
        if (!(is_null($plannedDate) || $plannedDate === '')) {
            $plannedDate = normalizeToCarbon($plannedDate);
        } else {
            // __ Получаем смещение в днях для Блоков
            $offset      = BusinessProcessesService::getDateOffsetForOrderMovingProcessByNodeIdAndClientId(BLOCKS_NODE_ID, $order->client->id);
            $plannedDate = normalizeToCarbon($order->load_at)->addDays($offset);
        }

        $createdTask = null;
        DB::transaction(function () use ($order, $plannedDate, $groupedPivotRecordsExpense, &$createdTask) {
            // __ Создаем СЗ
            $createdTask = BlockTask::query()->create([
                'action_at' => $plannedDate,
                'order_id'  => $order->id,
                'position'  => self::getBlockTaskLastPositionInDay($plannedDate) + 1, // __ Получаем позицию для нового СЗ
            ]);
            if (!$createdTask) {
                throw new Exception('Failed to create BlockTask');
            }

            // __ Создаем контент (строки) СЗ
            // !!! Сразу пишем в базу. Можно Создать -> Отсортировать (Приоритет + Размер + Линия) -> Записать
            $position = 1;
            foreach ($groupedPivotRecordsExpense as $code1C => $records) {
                $block = self::getBlockByCode1c($code1C);
                if (!$block) {
                    throw new Exception('Missing Block with code 1c ' . $code1C);
                }

                // __ Получаем так, потому что collection - полк в Block
                $collection = $block->getRelation('blockCollection');

                // __ Формируем контекст для Блока в СЗ
                $orderLineContext = [];
                $totals           = 0;
                foreach ($records as $record) {
                    $orderLineContext[] = [
                        'order_line_id'     => $record->order_line_id,
                        'order_line_amount' => $record->amount,
                        'expense'           => (float)$record->expense,
                        'rest'              => (float)$record->rest,
                    ];
                    $totals             += (float)$record->expense + (float)$record->rest;
                }

                BlockTaskLine::query()->create([
                    'block_task_id'      => $createdTask->id,
                    'block_code_1c'      => $block->code_1c,
                    'block_code_1c_copy' => $block->code_1c,
                    'block_name'         => $block->name,
                    'order_line_ids'     => $orderLineContext,
                    'amount'             => (int)$totals,
                    'line'               => $collection->line,
                    'position'           => $position++,
                    'productivity'       => $collection->productivity,
                    'square'             => $block->length * $block->width / 100 / 100,
                    'time'               => $collection->productivity !== 0.0 ? ($block->length * $block->width / 100 / 100) * (int)$totals / $collection->productivity : 0,
                ]);
            }

            // __ Создаем запись в Статусе: Создано
            $createdTask->statuses()->attach([
                BlockTaskStatus::BLOCK_STATUS_CREATED_ID => [
                    'set_at'     => now(),
                    'created_by' => auth()->id(),
                ]
            ]);
        });

        return $createdTask;
    }

    /**
     * ___ Получаем позицию последнего СЗ в дне
     * @param string|Carbon|null $date Дата нужного дня
     * @return int
     */
    public static function getBlockTaskLastPositionInDay(string|Carbon $date = null): int
    {
        if (is_null($date) || $date === '') {
            return 0;
        }

        $date = normalizeToCarbon($date);

        $pos = BlockTask::query()->whereDate('action_at', $date)->max('position');
        if (!is_null($pos)) {
            return $pos;
        }
        return BlockTask::query()
            ->whereDate('action_at', $date)
            ->count();
    }

    /**
     * ___ Возвращаем следующую производственную смену
     * @param ManufactureDayAndChange|Carbon|string $manufactureEntity
     * @param int|null $change
     * @return ManufactureDayAndChange
     */
    public static function getNextChange(
        ManufactureDayAndChange|Carbon|string $manufactureEntity,
        int $change = null
    ): ManufactureDayAndChange {
        $manufDateAndChange = null;
        if ($manufactureEntity instanceof ManufactureDayAndChange) {
            $manufDateAndChange = new ManufactureDayAndChange($manufactureEntity->getManufactureDay()->addDay(), 1);
        } else {
            $manufDateAndChange = new ManufactureDayAndChange(normalizeToCarbon($manufactureEntity)->addDay(), 1);
            // $manufDateAndChange = new ManufactureDayAndChange(normalizeToCarbon($manufactureEntity)->addDay(), $change);
        }

        return $manufDateAndChange;
    }

    /**
     * ___ Массовое обновление СЗ
     * @param array $rows
     * @return void
     * @throws Throwable
     * @noinspection DuplicatedCode
     */
    public static function bulkUpdateTasks(array $rows): void
    {
        // __ Получаем имя таблицы
        $table = (new BlockTask)->getTable();

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

            $casesChange  = [];
            $paramsChange = [];

            foreach ($rows as $row) {
                if (isset($row['action_at'])) {
                    $casesActionAt[] = "WHEN id = ? THEN ?";
                    array_push($paramsActionAt, $row['id'], $row['action_at']);
                }

                if (isset($row['position'])) {
                    $casesPosition[] = "WHEN id = ? THEN ?";
                    array_push($paramsPosition, $row['id'], $row['position']);
                }

                if (isset($row['change'])) {
                    $casesChange[] = "WHEN id = ? THEN ?";
                    array_push($paramsChange, $row['id'], $row['change']);
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

            // __ Интегрируем блок смены в итоговый
            // __ change обернуто в косые кавычки: `change` = CASE .... Это сделано обязательно, MySQL
            // __ change обернуто в двойные кавычки: "change" = CASE .... Это сделано обязательно, PostgreSQL
            // __ так как слово CHANGE является зарезервированной системной командой в SQL
            if (!empty($casesChange)) {
                $setParts[]  = '"change" = CASE ' . implode(' ', $casesChange) . ' ELSE "change" END';
                $finalParams = array_merge($finalParams, $paramsChange);
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
     * ___ Массовое обновление СЗ
     * @param array $rows
     * @return void
     * @throws Throwable
     */
    public static function bulkUpdateTasks_Old(array $rows): void
    {
        // ___ Формат входных данных:
        // $tasksToUpdate[] = [
        //     'id'        => taskId,
        //     'action_at' => new_action_at ?? null,
        //     'position'  => new_position ?? null,
        // ];


        // __ Получаем имя таблицы
        $table = (new BlockTask)->getTable();

        // __ 1. Находим только те ID, у которых действительно меняется позиция (чтобы не уводить в минус лишнее)
        $idsForMinus = array_column(array_filter($rows, fn($r) => isset($r['position'])), 'id');

        // __ 2. Находим все ID, которые участвуют в обновлении (хоть позиция, хоть amount)
        $allIds = array_column($rows, 'id');

        DB::transaction(function () use ($table, $rows, $idsForMinus, $allIds) {
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



    public static function test()
    {
        //return ['data' => 'ok'];
        //$blockTask = self::createBlockTaskFromOrderId(1634);
        //$blockTask = self::createBlockTaskFromOrderId(1635);

        //$coll = BlockCollection::query()->with('kdbDoc')->get();
        //$collArray = $coll->toArray();

        $order = Order::query()
            ->withExists('cuttingTask') // <-- Добавит boolean-поле cutting_task_exists
            ->withExists('sewingTask')  // <-- Добавит boolean-поле sewing_task_exists
            ->withExists('blockTask')  // <-- Добавит boolean-поле block_task_exists
            ->with([
                'lines.model.modelType',
                'lines.specification',
                'lines.specificationAdd',
                'client',
                'orderType',
                'sewingTask.lines',
                'sewingTask.currentStatus',
                'sewingTask.lines.orderLine',
                'cuttingTask.lines',
                'cuttingTask.currentStatus',
                'cuttingTask.lines.orderLine',
            ])
            ->findOrFail(620);
        $orderArr = $order->toArray();

        $a = 0;



    }
}
