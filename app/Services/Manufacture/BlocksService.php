<?php
/** @noinspection DuplicatedCode */

namespace App\Services\Manufacture;


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
                ->with(['collection'])
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
            $position = 1;
            foreach ($groupedPivotRecordsExpense as $code1C => $records) {
                $block = self::getBlockByCode1c($code1C);
                if (!$block) {
                    throw new Exception('Missing Block with code 1c ' . $code1C);
                }

                // __ Получаем так, потому что collection - полк в Block
                $collection = $block->getRelation('collection');

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
                    'square'             => $block->length * $block->width,
                    'time'               => $block->length * $block->width * $collection->productivity * (int)$totals,
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


    public static function test()
    {
        //return ['data' => 'ok'];
        $blockTask = self::createBlockTaskFromOrderId(1634);
        $blockTask = self::createBlockTaskFromOrderId(1635);

        $a = 0;
    }
}
