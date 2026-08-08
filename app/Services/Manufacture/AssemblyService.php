<?php

namespace App\Services\Manufacture;


use App\Classes\AssemblySize;
use App\Models\Manufacture\Cells\Assembly\AssemblyTask;
use App\Models\Manufacture\Cells\Assembly\AssemblyTaskLine;
use App\Models\Manufacture\Cells\Assembly\AssemblyTaskLineSector;
use App\Models\Manufacture\Cells\Assembly\AssemblyTaskStatus;
use App\Models\Materials\Material;
use App\Models\Order\Order;
use App\Services\BusinessProcessesService;
use App\Services\ModelsService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Throwable;
//use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection;

final class AssemblyService
{

    /**
     *  ___ Создать СЗ для Сборки из основного Заказа
     * @param int $orderId             __ ID основного Заказа
     * @param string|null $plannedDate __ Дата планируемого выполнения СЗ - должна быть либо дата, либо смещение, приоритет - дата
     * @return AssemblyTask|null
     * @noinspection DuplicatedCode
     * @throws Throwable
     */
    public static function createAssemblyTaskFromOrderId(
        int $orderId,
        string|null $plannedDate = null,
    ): ?AssemblyTask {
        try {
            // __ Проверяем на существование заказа
            $order = Order::query()->with(['lines', 'client'])->find($orderId);
            if (!$order) {
                return null;
            }

            // __ Получаем плановую дату
            if (!(is_null($plannedDate) || $plannedDate === '')) {
                $plannedDate = normalizeToCarbon($plannedDate);
            } else {
                // __ Получаем смещение в днях для Пошива
                $offset      = BusinessProcessesService::getDateOffsetForOrderMovingProcessByNodeIdAndClientId(ASSEMBLY_NODE_ID, $order->client->id);
                $plannedDate = normalizeToCarbon($order->load_at)->addDays($offset);
            }

            $createdTask = null;
            DB::transaction(function () use ($order, $plannedDate, &$createdTask) {
                // __ Создаем СЗ
                $createdTask = AssemblyTask::query()->create([
                    'action_at' => $plannedDate,
                    'order_id'  => $order->id,
                    'position'  => self::getAssemblyTaskLastPositionInDay($plannedDate) + 1, // __ Получаем позицию для нового СЗ
                ]);

                if (!$createdTask) {
                    throw new Exception('Failed to create AssemblyTask');
                }

                $summarySectorsExpense = [];
                foreach (AssemblyTask::ASSEMBLY_TASK_SECTORS as $sector) {
                    $summarySectorsExpense[$sector] = self::getSectorExpense($order->id, $sector);
                }


                // __ Создаем контент (строки) СЗ
                $position = 1;
                foreach ($order->lines as $line) {
                    // __ Если это расчетная модель (AVERAGE), то ставим позицию 0
                    // if ($order->lines->count() === 1 && ModelsService::isElementAverage($line->model_code_1c)) {
                    //     $position = 0;
                    // }

                    // __ Сам контент (строки) СЗ
                    /** @var AssemblyTaskLine $createdTaskLine */
                    $createdTaskLine = AssemblyTaskLine::query()->create([
                        'assembly_task_id' => $createdTask->id,
                        'order_line_id'    => $line->id,
                        'amount'           => $line->amount,
                        'position'         => $position++,
                        'time'             => 0,

                        // __ Задаем подмену свойств
                        'phantom'          => null,
                        'phantom_json'     => null,

                    ]);

                    // __ Контекст в разрезе производственного участка
                    foreach (AssemblyTask::ASSEMBLY_TASK_SECTORS as $sector) {
                        $sectorExpense = $summarySectorsExpense[$sector][$line->id] ?? null;

                        if (!isset($sectorExpense)) {
                            continue;
                        }

                        foreach ($sectorExpense as $expense) {
                            // __ Получаем размер детальки
                            $assemblySize = new AssemblySize($sector, $expense->outputs, $line->width, $line->length, $line->height);

                            //$a = 0;

                            /** @var AssemblyTaskLineSector $createdTaskLineSector */
                            $createdTaskLineSector = AssemblyTaskLineSector::query()->create([

                                // __ Связи
                                'assembly_task_line_id' => $createdTaskLine->id,
                                'order_line_id'         => $line->id,

                                // __ Количество
                                'amount'                => $line->amount,
                                'count'                 => $assemblySize->getAmount(),

                                // __ Параметры
                                'width'                 => $line->width,
                                'length'                => $line->length,
                                'height'                => $line->height,

                                // __ Размеры детальки
                                'detail_width'          => $assemblySize->getWidth(),
                                'detail_length'         => $assemblySize->getLength(),
                                'detail_height'         => $assemblySize->getHeight(),

                                // __ Материал
                                'material_code_1c'      => $expense->material_code_1c,
                                'material_name'         => $expense->material_name_expense,

                                // __ Расход
                                'expense'               => (float)$expense->expense,
                                'rest'                  => (float)$expense->rest,
                                'total'                 => (float)$expense->expense + (float)$expense->rest,

                                // __ Трудозатраты
                                'time'                  => 0,

                                // __ Участок
                                'sector'                => $sector,

                                // __ Задаем подмену свойств
                                'phantom'               => null,
                                'phantom_json'          => null,

                                //'position'              => 0,
                            ]);
                        }
                    }
                }

                // __ Создаем запись в Статусе: Создано
                $createdTask->statuses()->attach([
                    AssemblyTaskStatus::ASSEMBLY_STATUS_CREATED_ID => [
                        'set_at'     => now(),
                        'created_by' => auth()->id(),
                    ]
                ]);
            });

            return $createdTask;
        } catch (Exception|Throwable $e) {
            throw ($e);
        }
    }


    /**
     *   ___ Распределяем СЗ по Частям СЗ
     *   ___ Сюда приходим тогда, когда есть прогнозное СЗ, а поверх него загружаем Заявку
     * @param int $orderId
     * @return bool
     * @throws Throwable
     */
    public static function distributeAssemblyTaskFromOrderId(int $orderId): bool
    {
        // !!! Костыль !!! Доработать процедуру распределения
        self::createAssemblyTaskFromOrderId($orderId);
        return true;
    }


    /**
     * ___ Получаем позицию последнего СЗ в дне
     * @param string|Carbon|null $date Дата нужного дня
     * @return int
     */
    public static function getAssemblyTaskLastPositionInDay(string|Carbon $date = null): int
    {
        if (is_null($date) || $date === '') {
            return 0;
        }

        $date = normalizeToCarbon($date);

        return AssemblyTask::query()
            ->whereDate('action_at', $date)
            ->count();
        // return SewingTask::query()->whereDate('action_at', $date)->max('position');
    }


    // ___ Получаем список кодов из 1с для нужнуго участка производства (ППУ, Латекс, Тонкий настил, ...)
    public static function getSectorCodes1c(string $sector): array
    {
        $codes = match ($sector) {
            AssemblyTask::ASSEMBLY_TASK_SECTOR_LATEX      => ['000000011'],
            AssemblyTask::ASSEMBLY_TASK_SECTOR_COCONUT    => ['000000003'],
            AssemblyTask::ASSEMBLY_TASK_SECTOR_LAYER      => ['000000447'],
            AssemblyTask::ASSEMBLY_TASK_SECTOR_FOAM_SIDE,
            AssemblyTask::ASSEMBLY_TASK_SECTOR_FOAM_LAYER => ['000000021'],
            default                                       => [''],
        };

        $groups = Material::query()
            ->assembly($codes)
            ->get();

        $codes = [];
        foreach ($groups as $group) {
            foreach ($group->categories as $category) {
                foreach ($category->materials as $material) {
                    $codes[] = $material->code_1c;
                }
            }
        }

        return $codes;
    }


    /**
     * ___ Получаем расход по строкам контекста Заявки (OrderLines)
     * @param int $orderId
     * @param string $sector
     * @return array
     */
    public static function getSectorExpense(int $orderId, string $sector): array
    {
        // __ Получаем коды материалов, которые нужно дернуть
        $codes = self::getSectorCodes1c($sector);

        // Проверяем, относится ли сектор к особым случаям ('side' или 'layer')
        $isSide          = ($sector === AssemblyTask::ASSEMBLY_TASK_SECTOR_FOAM_SIDE);
        $isLayer         = ($sector === AssemblyTask::ASSEMBLY_TASK_SECTOR_FOAM_LAYER);
        $isSpecialSector = $isSide || $isLayer;

        // __ Получаем Расход
        $groupedPivotRecordsExpense = DB::table('order_line_material_pivot as pivot')
            ->join('order_lines as lines', 'lines.id', '=', 'pivot.order_line_id')
            ->where('lines.order_id', $orderId)

            // __ Только выбранные материалы
            ->whereIn('pivot.material_code_1c', $codes)

            // __ Условия фильтрации по полю procedure для 'side' и 'layer'
            ->when($isSpecialSector, function ($query) use ($isSide, $isLayer) {
                // __ Поле procedure НЕ должно содержать "шт" (без учета регистра)
                // В PostgreSQL используем ILIKE (или LOWER(pivot.procedure) NOT LIKE '%шт%')
                $query->where('pivot.procedure', 'NOT ILIKE', '%шт%');
                //$query->whereRaw("pivot.procedure !~* ?", ['[Шш][Тт]']);

                //$query->where('pivot.procedure', 'NOT LIKE', '%шт%')
                //    ->where('pivot.procedure', 'NOT LIKE', '%Шт%')
                //    ->where('pivot.procedure', 'NOT LIKE', '%ШТ%');

                // __ Если sector == 'side' -> должно быть слово "борт"
                if ($isSide) {
                    $query->where('pivot.procedure', 'ILIKE', '%борт%');
                    //$query->whereRaw("pivot.procedure ~* ?", ['[Бб][Оо][Рр][Тт]']);

                    //$query->where(function ($q) {
                    //    $q->where('pivot.procedure', 'LIKE', '%борт%')
                    //        ->orWhere('pivot.procedure', 'LIKE', '%Борт%')
                    //        ->orWhere('pivot.procedure', 'LIKE', '%БОРТ%');
                    //});
                }

                // __ Если sector == 'layer' -> должно быть слово "наст"
                if ($isLayer) {
                    $query->whereRaw("pivot.procedure ~* ?", ['[Нн][Аа][Сс][Тт]']);
                    //$query->whereRaw('LOWER(pivot.procedure) LIKE ?', ['%наст%']);
                    //$query->where('pivot.procedure', '~*', 'наст');
                    //$query->where('pivot.procedure', 'LIKE', '%Наст%');

                    //$query->where(function ($q) {
                    //    $q->where('pivot.procedure', 'LIKE', '%наст%')
                    //        ->orWhere('pivot.procedure', 'LIKE', '%Наст%')
                    //        ->orWhere('pivot.procedure', 'LIKE', '%НАСТ%');
                    //});
                }
            })

            // __ Перечисляем только те поля, которые нам реально нужны:
            ->select([
                'pivot.order_line_id', // ⚠️ КРИТИЧЕСКИ ВАЖНО для последующего groupBy!
                'pivot.material_code_1c',
                'pivot.material_name_expense',
                'pivot.detail',
                'pivot.position',
                'pivot.expense',
                'pivot.rest',
                'pivot.outputs',
            ])
            ->orderBy('pivot.position', 'asc')
            ->get()
            ->groupBy('order_line_id')
            ->toArray();


        return $groupedPivotRecordsExpense;
    }

    /**
     * ___ Фильтруем по Чехлу
     * @param Collection $data
     * @return Collection
     */
    public static function filterCovers(Collection $data): Collection
    {
        // __ Фильтруем вложенную коллекцию lines для каждой задачи
        return $data->transform(function ($task) {
            // __ Оставляем только те строки, модель не Чехол
            $filteredLines = $task->lines->filter(function ($line) {
                return ModelsService::isElementBase($line->orderLine->model);
            });

            // __ Перезаписываем загруженную связь lines отфильтрованной коллекцией (без сброса индексации ключей)
            $task->setRelation('lines', $filteredLines->values());

            return $task;
        });
    }


    public static function test(Request|null $request = null): mixed
    {
        //$assemblyMaterials = Material::query()
        //    ->assembly('000000003')
        //    ->get();
        //
        //$assemblyMaterialsArray = $assemblyMaterials->toArray();

        $expense = self::getSectorExpense(738, AssemblyTask::ASSEMBLY_TASK_SECTOR_FOAM_LAYER);


        $a = 0;

        return 0;
    }
}
