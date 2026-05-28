<?php

namespace App\Classes;

use App\Models\Manufacture\Cells\Cutting\CuttingOperation;
use App\Models\Manufacture\Cells\Cutting\CuttingOperationSchema;

//use App\Models\Manufacture\Cells\Cutting\CuttingTask;
use App\Models\Manufacture\Cells\Cutting\CuttingTaskLine;
use App\Models\Models\Model;
use App\Models\Order\OrderLine;
use App\Models\Shared\Size;
use App\Services\Manufacture\CuttingService;
use App\Services\ModelsService;
use App\Services\SizeService;

final class CuttingTimeLabor
{
    // --- Трудозатраты на единицу изделия для ШМ
    // !!! TODO: Эти значения нужно брать из БД для средней модели и рассчитывать для конкретной модели
    private int $timePerPic = 0;
    private int $timePerPicPanel = 0;
    private int $timePerPicSide = 0;
    private int $timePerPicTable_1 = 0;
    private int $timePerPicTable_2 = 0;
    private int $timePerPicTable_3 = 0;
    private int $timePerPicUndefined = 0;


    // --- Трудозатраты на общее количество изделий для ШМ
    //private int $time = 0;
    //private int $timeTotal = 0;
    private int $timeTable_1 = 0;
    private int $timeTable_2 = 0;
    private int $timeTable_3 = 0;
    private int $timeUndefined = 0;

    private int $time = 0;              // __ Время, которое будем возвращать единым значением по строке
    private int $timePanel = 0;
    private int $timeSide = 0;


    // --- Количество изделий по типам ШМ
    private int $amount = 0;            // __ Общее Количество (То, что было передано в конструкторе)
    private float $amountTable_1 = 0;
    private float $amountTable_2 = 0;
    private float $amountTable_3 = 0;
    private float $amountUndefined = 0;

    // --- Тип ШМ
    private string $cuttingType = '';

    // --- Модель + Размер
    private string $modelCode1C = '';   // __ Код 1С Модели, не сохраняем всю модель, чтобы экономить память
    // private Size $size;

    // --- Phantom
    private string $phantom = '';
    private array $phantomJson = [];

    // --- Panel/Side
    private bool $isPanel = false;
    private bool $isSide = false;


    /**
     * ___ Получаем трудозатраты либо по Строке СЗ, либо по Модели, Размеру и количеству
     * @param CuttingTaskLine|null $cuttingTaskLine **Сменное задание (Строка СЗ (CuttingLine))**
     * @param string|Model|null $model              __Модель__
     * @param string|Size|null $size                __Размер__
     * @param int $amount                           __Количество__
     */
    public function __construct(
        CuttingTaskLine|null $cuttingTaskLine = null,
        string|Model|null $model = null,
        string|Size|null $size = null,
        int $amount = 1
    ) {
        // __ Устанавливаем количество
        $this->amount = $amount;

        // __ Если передана строка Сменного задания
        if ($cuttingTaskLine /*&& $cuttingTaskLine instanceof CuttingTaskLine*/) {
            $this->phantom     = $cuttingTaskLine->phantom;
            $this->phantomJson = $cuttingTaskLine->phantom_json;
            $this->isPanel     = $cuttingTaskLine->is_panel;
            $this->isSide      = $cuttingTaskLine->is_side;

            // Проверяем, загружена ли уже связь в памяти этой конкретной строки
            if ($cuttingTaskLine->relationLoaded('orderLine')) {
                // Берем прямо из памяти, в базу запрос НЕ идёт
                $orderLine = $cuttingTaskLine->orderLine;
            } else {
                // Связи в памяти нет, идём в базу по старинке
                $orderLine = OrderLine::find($cuttingTaskLine->order_line_id);

                // Опционально: можно "затолкнуть" её в память модели вручную,
                // чтобы при следующем вызове она уже считалась загруженной:
                // $cuttingTaskLine->setRelation('orderLine', $orderLine);
            }

            //// Ищет строго в загруженных отношениях. Если не находит — возвращает null (в базу не идёт)
            //$orderLine = $cuttingTaskLine->getRelationValue('orderLine');
            //
            //if (is_null($orderLine)) {
            //    // В памяти не было, делаем ручной запрос
            //    $orderLine = OrderLine::find($cuttingTaskLine->order_line_id);
            //}

            //$orderLine         = OrderLine::query()->find($cuttingTaskLine->order_line_id);  // __ Получаем Контекст Строки Заказа
            if ($orderLine) {
                $model = $this->getModel($orderLine->model_code_1c); // __ Получаем Модель
                //$model = ModelsService::getModelByCode1C($orderLine->model_code_1c); // __ Получаем Модель
                if ($model) {
                    // !!! Порядок важен
                    $this->setCuttingTable($model);             // __ Устанавливаем Стол Раскроя
                    $this->calcAmounts();                       // __ Считаем количество

                    $size = new Size($orderLine->width, $orderLine->length, $orderLine->height); // __ Получаем Размер
                    // $size = $this->getSize($orderLine->size);

                    //if ($cuttingTaskLine->id === 47) {
                    //if ($orderLine->model_code_1c === '000002754') {
                        //$workModel = Model::query()
                        //    ->where(CODE_1C, '000002754')
                        //    ->with([
                        //        'modelType',
                        //        'cover',
                        //        'base',
                        //        'cuttingSchema.operations',
                        //        'cuttingOperations',
                        //    ])
                        //    ->first();
                        //$workModelArray = $workModel->toArray();
                        //
                    //    $a = 0;
                    //}


                    $this->calcTimePerPic($size);                                                // __ Считаем трудозатраты на ед.
                    $this->calcTimeLabor();                                                      // __ Считаем трудозатраты на общее количество

                }
            }
        } else {
            if ($model && $size && $amount !== 0) {
                $workModel = $this->getModel($model);           // __ Находим модель
                if ($workModel) {
                    // !!! Порядок важен
                    $this->setCuttingTable($workModel);         // __ Устанавливаем Стол Раскроя
                    $this->calcAmounts();                       // __ Считаем количество

                    $workSize = $this->getSize($size);
                    if ($workSize) {
                        $this->calcTimePerPic($workSize);       // __ Считаем трудозатраты на ед.
                        $this->calcTimeLabor();                 // __ Считаем трудозатраты на общее количество
                    }
                }
            }
        }
    }


    // ___ Сохраняем тип ШМ
    private function setCuttingTable(Model $model): void
    {
        if (ModelsService::isElementAverage($model)) {
            $this->cuttingType = CuttingTaskLine::FIELD_AVERAGE;
        } else {
            $this->cuttingType = CuttingService::getTableByModel($model);
        }
    }


    // ___ Получаем Модель
    private function getModel(string|int|Model|null $model = null): ?Model
    {
        // __ Находим модель
        $workModel = null;
        if ($model instanceof Model) {
            $workModel = $model;
        } elseif (is_string($model)) {
            $workModel = ModelsService::getElementByNameOrCode1C($model);
        }

        // __ Если модель не найдена, то ищем ее в БД
        if (!$workModel) {
            $workModel = Model::query()
                ->where(CODE_1C, $model)
                ->orWhere('name', $model)
                ->with([
                    'modelType',
                    'cover',
                    'base',
                    'cuttingSchema.operations',
                    'cuttingOperations',
                ])
                ->first();
            //$a = 0;
        }


        $this->modelCode1C = $workModel->code_1c;
        return $workModel;
    }


    // ___ Получаем размеры
    private function getSize(string|Size|null $size = null): ?Size
    {
        $workSize = null;
        if ($size instanceof Size) {
            $workSize = $size;
        } elseif (is_string($size)) {
            $workSize = SizeService::getDimensions($size);
        }

        // $this->size = $workSize;
        return $workSize;
    }


    // ___ Получаем количество единиц по типам ШМ (разбиваем общее на веса)
    private function calcAmounts(): void
    {
        // !!! __ TODO: Тут вся логика по количеству
        // !!! __ TODO: Нужно сходить в БД и получить количество и трудозатраты - Сходим
        // !!! __ TODO: Нужно привязать модель к размеру - Привязываем
        // !!! __ TODO: Нужно сделать массив с расчетным количеством изделий - Считаем и Выдаем


        if ($this->cuttingType === CuttingTaskLine::FIELD_AVERAGE) {
            $this->amountTable_1   = $this->amount * 0.4;
            $this->amountTable_2   = $this->amount * 0.35;
            $this->amountTable_3   = $this->amount * 0.15;
            $this->amountUndefined = $this->amount * 0.0;
            $this->correctAmounts();    // __ Корректируем количество
        } else {
            if ($this->cuttingType === CuttingTaskLine::FIELD_TABLE_1) {
                $this->amountTable_1 = $this->amount;
            } elseif ($this->cuttingType === CuttingTaskLine::FIELD_TABLE_2) {
                $this->amountTable_2 = $this->amount;
            } elseif ($this->cuttingType === CuttingTaskLine::FIELD_TABLE_3) {
                $this->amountTable_3 = $this->amount;
            } elseif ($this->cuttingType === CuttingTaskLine::FIELD_UNDEFINED) {
                $this->amountUndefined = $this->amount;
            }
        }
    }


    // ___ Получаем трудозатраты на единицу изделия
    private function calcTimePerPic(Size $size): void
    {
        // !!! __ TODO: Тут вся логика по трудозатратам на единицу
        // !!! __ TODO: Нужно сходить в БД и получить трудозатраты - Сходим
        // !!! __ TODO: Нужно привязать модель к размеру - Привязываем
        // !!! __ TODO: Нужно сделать массив с расчетным количеством изделий - Считаем и Выдаем

        $model = $this->getModel($this->modelCode1C);

        $modelToArray = $model->toArray();

        $this->timePerPicTable_1   = 0;
        $this->timePerPicTable_2   = 0;
        $this->timePerPicTable_3   = 0;
        $this->timePerPicUndefined = 0;

        if ($this->cuttingType === CuttingTaskLine::FIELD_AVERAGE) {
            $this->timePerPicTable_1   = 100;
            $this->timePerPicTable_2   = 150;
            $this->timePerPicTable_3   = 200;
            $this->timePerPicUndefined = 0;
        } else {
            // __ Получаем операции (Если нет схемы (schema_id === 0), то операции из модели, иначе из схемы)
            $operations = $model->cuttingSchema->id !== 0 ? $model->cuttingSchema->operations : $model->cuttingOperations;

            // $m = $model->toArray();
            // $op = [];
            // $operations->each(function ($operation) use ($op) {$op[] = $operation->all();});

            $timePerPicArr         = $this->getTimeLaborPerPic($operations, $size);
            $this->timePerPic      = $timePerPicArr[0];
            $this->timePerPicPanel = $timePerPicArr[1];
            $this->timePerPicSide  = $timePerPicArr[2];

            if ($this->cuttingType === CuttingTaskLine::FIELD_TABLE_1) {
                $this->timePerPicTable_1 = $timePerPicArr[0];
            } elseif ($this->cuttingType === CuttingTaskLine::FIELD_TABLE_2) {
                $this->timePerPicTable_2 = $timePerPicArr[0];
            } elseif ($this->cuttingType === CuttingTaskLine::FIELD_TABLE_3) {
                $this->timePerPicTable_3 = $timePerPicArr[0];
            } elseif ($this->cuttingType === CuttingTaskLine::FIELD_UNDEFINED) {
                $this->timePerPicUndefined = $timePerPicArr[0];
            }
        }
    }

    // ___ Получаем сами трудозатраты

    private function calcTimeLabor(): void
    {
        if ($this->cuttingType === CuttingTaskLine::FIELD_AVERAGE) {
            $this->timeTable_1   = $this->timePerPicTable_1 * $this->amountTable_1;
            $this->timeTable_2   = $this->timePerPicTable_2 * $this->amountTable_2;
            $this->timeTable_3   = $this->timePerPicTable_3 * $this->amountTable_3;
            $this->timeUndefined = $this->timePerPicUndefined * $this->amountUndefined;
        } else {
            if ($this->cuttingType === CuttingTaskLine::FIELD_TABLE_1) {
                $this->timeTable_1 = $this->timePerPicTable_1 * $this->amountTable_1;
            } elseif ($this->cuttingType === CuttingTaskLine::FIELD_TABLE_2) {
                $this->timeTable_2 = $this->timePerPicTable_2 * $this->amountTable_2;
            } elseif ($this->cuttingType === CuttingTaskLine::FIELD_TABLE_3) {
                $this->timeTable_3 = $this->timePerPicTable_3 * $this->amountTable_3;
            } elseif ($this->cuttingType === CuttingTaskLine::FIELD_UNDEFINED) {
                $this->timeUndefined = $this->timePerPicUndefined * $this->amountUndefined;
            }

            $this->time      = $this->timePerPic * $this->amount;
            $this->timePanel = $this->timePerPicPanel * $this->amount;
            $this->timeSide  = $this->timePerPicSide * $this->amount;
        }
    }


    // --- Количество

    // ___ Получаем среднее количество изделий по типу ШМ
    public function getAveragesAmount(): array
    {
        return [
            CuttingTaskLine::FIELD_TABLE_1   => $this->getAmountTable_1(),
            CuttingTaskLine::FIELD_TABLE_2   => $this->getAmountTable_2(),
            CuttingTaskLine::FIELD_TABLE_3   => $this->getAmountTable_3(),
            CuttingTaskLine::FIELD_UNDEFINED => $this->getAmountUndefined(),
        ];
    }

    // --- Возвращаем количество в числовом формате, те, которые определены (кроме Average)
    public function getAmountTable_1(): int
    {
        return $this->amountTable_1;
    }

    public function getAmountTable_2(): int
    {
        return $this->amountTable_2;
    }

    public function getAmountTable_3(): int
    {
        return $this->amountTable_3;
    }

    public function getAmountUndefined(): int
    {
        return $this->amountUndefined;
    }
    //
    //public function getAmountUniversal(): int
    //{
    //    return $this->amountUniversal;
    //}
    //
    //public function getAmountAuto(): int
    //{
    //    return $this->amountAuto;
    //}
    //
    //public function getAmountSolidHard(): int
    //{
    //    return $this->amountSolidHard;
    //}
    //
    //public function getAmountSolidLite(): int
    //{
    //    return $this->amountSolidLite;
    //}


    // --- Трудозатраты

    // ___ Получаем трудозатраты в массив
    public function getTimeLaborArray(): array
    {
        return $this->getLaborArray();
    }

    // ___ Получаем трудозатраты в JSON
    public function getTimeLaborJson(): string
    {
        return json_encode($this->getLaborArray());
    }

    // ___ Получаем трудозатраты в ассоциативном массиве
    private function getLaborArray(): array
    {
        return [
            CuttingTaskLine::FIELD_TABLE_1   => $this->getTimeTable_1(),
            CuttingTaskLine::FIELD_TABLE_2   => $this->getTimeTable_2(),
            CuttingTaskLine::FIELD_TABLE_3   => $this->getTimeTable_3(),
            CuttingTaskLine::FIELD_UNDEFINED => $this->getTimeUndefined(),
        ];
    }

    // --- Возвращаем время одним значением
    // __ Если расчетная модель - проверяем по фантому
    /**
     * @param bool $isFullTime
     * @return float|int
     */
    public function getRealTime(bool $isFullTime = false)
    {
        if ($isFullTime) {
            return $this->time;
        }
        if ($this->isPanel && !$this->isSide) {
            return $this->timePanel;
        }
        if ($this->isSide && !$this->isPanel) {
            return $this->timeSide;
        }


        return match ($this->cuttingType) {
            CuttingTaskLine::FIELD_AVERAGE   => match ($this->phantom) {
                CuttingTaskLine::FIELD_TABLE_1   => $this->amountTable_1,
                CuttingTaskLine::FIELD_TABLE_2   => $this->amountTable_2,
                CuttingTaskLine::FIELD_TABLE_3   => $this->amountTable_3,
                CuttingTaskLine::FIELD_UNDEFINED => $this->amountUndefined,
                default                          => 0,
            },
            CuttingTaskLine::FIELD_TABLE_1   => $this->amountTable_1,
            CuttingTaskLine::FIELD_TABLE_2   => $this->amountTable_2,
            CuttingTaskLine::FIELD_TABLE_3   => $this->amountTable_3,
            CuttingTaskLine::FIELD_UNDEFINED => $this->amountUndefined,
            default                          => 0,
        };
    }

    // --- Возвращаем трудозатраты в числовом формате, те, которые определены (кроме Average)
    public function getTimeTable_1(): int
    {
        return $this->timeTable_1;
    }

    public function getTimeTable_2(): int
    {
        return $this->timeTable_2;
    }

    public function getTimeTable_3(): int
    {
        return $this->timeTable_3;
    }

    public function getTimeUndefined(): int
    {
        return $this->timeUndefined;
    }


    // --- Возвращаем трудозатраты в ассоциативном массиве, если Average - то все, разбитые по статистике

    // ___ Получаем трудозатраты в ассоциативном массиве (универсальная функция)
    public function getTime(): int
    {
        return match ($this->cuttingType) {
            CuttingTaskLine::FIELD_TABLE_1   => $this->getTimeTable_1(),
            CuttingTaskLine::FIELD_TABLE_2   => $this->getTimeTable_2(),
            CuttingTaskLine::FIELD_TABLE_3   => $this->getTimeTable_3(),
            CuttingTaskLine::FIELD_UNDEFINED => $this->getTimeUndefined(),
            default                          => 0,
        };
    }


    // ___ Получаем трудозатраты в ассоциативном массиве (универсальная функция)
    public function getTimeArray(): array
    {
        return match ($this->cuttingType) {
            CuttingTaskLine::FIELD_TABLE_1   => $this->getTimeArrayTable_1(),
            CuttingTaskLine::FIELD_TABLE_2   => $this->getTimeArrayTable_2(),
            CuttingTaskLine::FIELD_TABLE_3   => $this->getTimeArrayTable_3(),
            CuttingTaskLine::FIELD_UNDEFINED => $this->getTimeArrayUndefined(),
            CuttingTaskLine::FIELD_AVERAGE   => $this->getTimeAverageArray(),
            default                          => [],
        };
    }


    // ___ Получаем трудозатраты в ассоциативном массиве по подменен стола
    public function getTimeByPhantom(string $phantom): array
    {
        $totalAmount = true;
        /** @noinspection PhpConditionAlreadyCheckedInspection */
        return match ($phantom) {
            CuttingTaskLine::FIELD_TABLE_1   => $this->getTimeArrayTable_1($totalAmount),
            CuttingTaskLine::FIELD_TABLE_2   => $this->getTimeArrayTable_2($totalAmount),
            CuttingTaskLine::FIELD_TABLE_3   => $this->getTimeArrayTable_3($totalAmount),
            CuttingTaskLine::FIELD_UNDEFINED => $this->getTimeArrayUndefined($totalAmount),
            CuttingTaskLine::FIELD_AVERAGE   => $this->getTimeAverageArray(),
            default                          => [],
        };
    }


    public function getTimeAverageArray(): array
    {
        return $this->getLaborArray();
    }

    public function getTimeArrayTable_1(bool $totalAmount = false): array
    {
        return [CuttingTaskLine::FIELD_TABLE_1 => $totalAmount ? $this->amount * $this->timePerPicTable_1 : $this->timeTable_1];
    }

    public function getTimeArrayTable_2(bool $totalAmount = false): array
    {
        return [CuttingTaskLine::FIELD_TABLE_2 => $totalAmount ? $this->amount * $this->timePerPicTable_2 : $this->timeTable_2];
    }

    public function getTimeArrayTable_3(bool $totalAmount = false): array
    {
        return [CuttingTaskLine::FIELD_TABLE_3 => $totalAmount ? $this->amount * $this->timePerPicTable_3 : $this->timeTable_3];
    }

    public function getTimeArrayUndefined(bool $totalAmount = false): array
    {
        return [CuttingTaskLine::FIELD_UNDEFINED => $totalAmount ? $this->amount * $this->timePerPicUndefined : $this->timeUndefined];
    }


    // ___ Корректируем количество, если оно не целое
    private function correctAmounts(): void
    {
        // __ 1. Список свойств для обработки
        $propertyNames = [
            'amountTable_1',
            'amountTable_2',
            'amountTable_3',
            'amountUndefined'
        ];

        $data       = [];
        $currentSum = 0;

        foreach ($propertyNames as $name) {
            $value      = $this->$name;
            $floorValue = (int)floor($value);

            $data[$name] = [
                'int'      => $floorValue,
                'fraction' => $value - $floorValue // Дробный "хвост"
            ];

            $currentSum += $floorValue;
        }

        // __ 2. Считаем, сколько единиц потеряли при округлении floor()
        $shortfall = $this->amount - $currentSum;

        // __ 3. Сортируем названия свойств по убыванию дробной части
        // __ Тот, у кого было 10.9, приоритетнее на получение +1, чем тот, у кого 10.1
        uasort($data, fn($a, $b) => $b['fraction'] <=> $a['fraction']);

        // __ 4. Распределяем недостачу
        foreach ($data as $name => $info) {
            if ($shortfall > 0) {
                $this->$name = (float)($info['int'] + 1);
                $shortfall--;
            } else {
                $this->$name = (float)$info['int'];
            }
        }
    }


    /**
     * ___ Получаем трудозатраты на единицу изделия по размеру и схеме
     * @param string|null $size
     * @param CuttingOperationSchema|null $schema
     * @return array
     */
    public function getTimeLaborBySizeAndCuttingSchema(string|null $size = null, CuttingOperationSchema $schema = null): array
    {
        // __ Страховка
        if (is_null($size) || is_null($schema)) {
            return [0, 0, 0];
        }

        $parsedSize = $this->getSize($size);

        if (is_null($parsedSize)) {
            return [0, 0, 0];
        }

        return $this->getTimeLaborPerPic($schema->operations, $parsedSize);
    }


    /**
     * ___ Сама логика расчета времени на единицу
     * @param $operations
     * @param Size $size
     * @return array
     */
    private function getTimeLaborPerPic($operations, Size $size): array
    {
        $timePerPic      = 0;
        $timePerPicPanel = 0;
        $timePerPicSide  = 0;
        foreach ($operations as $operation) {
            if (!$operation->active) {
                continue;   // __ Если операция не активна, то пропускаем ее
            }

            // $o = $operation->toArray();

            // __ Получаем тот дополнительный коэффициент, который нужно умножить на время
            $ratio = is_null($operation->pivot->ratio) || $operation->pivot->ratio === 0 ? 1 : $operation->pivot->ratio;
            $time  = 0;

            if ($operation->type === CuttingOperation::DYNAMIC_TYPE) {
                $time = $operation->time * $size->getPerimeter();
            } elseif ($operation->type === CuttingOperation::STATIC_TYPE) {
                $time = $operation->time;
            }

            if ($operation->detail === CuttingOperation::DETAIL_PANEL) {
                $timePerPicPanel += $time * $ratio;
            } elseif ($operation->detail === CuttingOperation::DETAIL_SIDE) {
                $timePerPicSide += $time * $ratio;
            }
            $timePerPic += $time * $ratio;
        }
        return [$timePerPic, $timePerPicPanel, $timePerPicSide];
    }

}




