<?php

namespace App\Classes;

use App\Models\Manufacture\Cells\Sewing\SewingOperation;
use App\Models\Manufacture\Cells\Sewing\SewingTask;
use App\Models\Manufacture\Cells\Sewing\SewingTaskLine;
use App\Models\Models\Model;
use App\Models\Order\OrderLine;
use App\Models\Shared\Size;
use App\Services\ModelsService;
use App\Services\SizeService;

final class SewingTimeLabor
{
    // --- Трудозатраты на единицу изделия для ШМ
    // !!! TODO: Эти значения нужно брать из БД для средней модели и рассчитывать для конкретной модели
    private int $timeUniversalPerPic = 0;
    private int $timeAutoPerPic = 0;
    private int $timeSolidHardPerPic = 0;
    private int $timeSolidLitePerPic = 0;
    private int $timeUndefinedPerPic = 0;

    // --- Трудозатраты на общее количество изделий для ШМ
    private int $timeUniversal = 0;
    private int $timeAuto = 0;
    private int $timeSolidHard = 0;
    private int $timeSolidLite = 0;
    private int $timeUndefined = 0;

    // --- Количество изделий по типам ШМ
    private int $amount = 0;            // __ Общее Количество (То, что было передано в конструкторе)
    private float $amountUniversal = 0;
    private float $amountAuto = 0;
    private float $amountSolidHard = 0;
    private float $amountSolidLite = 0;
    private float $amountUndefined = 0;

    // --- Тип ШМ
    private string $sewingType = '';


    // --- Модель + Размер
    private string $modelCode1C = '';   // __ Код 1С Модели, не сохраняем всю модель, чтобы экономить память
    // private Size $size;

    /**
     * ___ Получаем трудозатраты либо по Строке СЗ, либо по Модели, Размеру и количеству
     * @param  SewingTaskLine|null  $sewingTaskLine  **Сменное задание (Строка СЗ (SewingLine))**
     * @param  string|Model|null  $model  __Модель__
     * @param  string|Size|null  $size  __Размер__
     * @param  int  $amount  __Количество__
     */
    public function __construct(
        SewingTaskLine|null $sewingTaskLine = null,
        string|Model|null $model = null,
        string|Size|null $size = null,
        int $amount = 1
    ) {
        // __ Устанавливаем количество
        $this->amount = $amount;

        // __ Если передана строка Сменного задания
        if ($sewingTaskLine /*&& $sewingTaskLine instanceof SewingTaskLine*/) {
            $orderLine = OrderLine::query()->find($sewingTaskLine->order_line_id);  // __ Получаем Контекст Строки Заказа
            if ($orderLine) {
                $model = ModelsService::getModelByCode1C($orderLine->model_code_1c); // __ Получаем Модель
                if ($model) {
                    // !!! Порядок важен
                    $this->setSewingType($model);               // __ Устанавливаем тип ШМ
                    $this->calcAmounts();                       // __ Считаем количество

                    $size = new Size($orderLine->width, $orderLine->length, $orderLine->height); // __ Получаем Размер
                    // $size = $this->getSize($orderLine->size);
                    $this->calcTimePerPic($size);                                                // __ Считаем трудозатраты на ед.
                    $this->calcTimeLabor();                                                      // __ Считаем трудозатраты на общее количество
                }
            }
        } else {
            if ($model && $size && $amount !== 0) {
                $workModel = $this->getModel($model);           // __ Находим модель
                if ($workModel) {
                    // !!! Порядок важен
                    $this->setSewingType($workModel);           // __ Устанавливаем тип ШМ
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
    private function setSewingType(Model $model): void
    {
        if (ModelsService::isElementAverage($model)) {
            $this->sewingType = SewingTask::FIELD_AVERAGE;
        } else {
            /** @noinspection PhpUndefinedFieldInspection */
            $this->sewingType = match (true) {
                $model->is_universal  => SewingTask::FIELD_UNIVERSAL,
                $model->is_auto       => SewingTask::FIELD_AUTO,
                $model->is_solid_hard => SewingTask::FIELD_SOLID_HARD,
                $model->is_solid_lite => SewingTask::FIELD_SOLID_LITE,
                // $model->is_undefined  => SewingTask::FIELD_UNDEFINED,
                default               => SewingTask::FIELD_UNDEFINED,
            };
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

        /** @noinspection PhpUndefinedFieldInspection */
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

    /** @noinspection PhpUndefinedFieldInspection */
    private function calcAmounts(): void
    {
        // !!! __ TODO: Тут вся логика по количеству
        // !!! __ TODO: Нужно сходить в БД и получить количество и трудозатраты - Сходим
        // !!! __ TODO: Нужно привязать модель к размеру - Привязываем
        // !!! __ TODO: Нужно сделать массив с расчетным количеством изделий - Считаем и Выдаем


        if ($this->sewingType === SewingTask::FIELD_AVERAGE) {
            $this->amountUniversal = $this->amount * 0.4;
            $this->amountAuto      = $this->amount * 0.35;
            $this->amountSolidHard = $this->amount * 0.15;
            $this->amountSolidLite = $this->amount * 0.1;
            $this->amountUndefined = $this->amount * 0.0;
            $this->correctAmounts();    // __ Корректируем количество
        } else {
            if ($this->sewingType === SewingTask::FIELD_UNIVERSAL) {
                $this->amountUniversal = $this->amount;
            } elseif ($this->sewingType === SewingTask::FIELD_AUTO) {
                $this->amountAuto = $this->amount;
            } elseif ($this->sewingType === SewingTask::FIELD_SOLID_HARD) {
                $this->amountSolidHard = $this->amount;
            } elseif ($this->sewingType === SewingTask::FIELD_SOLID_LITE) {
                $this->amountSolidLite = $this->amount;
            } elseif ($this->sewingType === SewingTask::FIELD_UNDEFINED) {
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

        $this->timeUniversalPerPic = 0;
        $this->timeAutoPerPic      = 0;
        $this->timeSolidHardPerPic = 0;
        $this->timeSolidLitePerPic = 0;
        $this->timeUndefinedPerPic = 0;

        if ($this->sewingType === SewingTask::FIELD_AVERAGE) {

            $this->timeUniversalPerPic = 100;
            $this->timeAutoPerPic      = 150;
            $this->timeSolidHardPerPic = 200;
            $this->timeSolidLitePerPic = 250;
            $this->timeUndefinedPerPic = 0;

        } else {

            // __ Получаем операции (Если нет схемы (schema_id === 0), то операции из модели, иначе из схемы)
            $operations = $model->sewingSchema->id !== 0 ? $model->sewingSchema->operations : $model->sewingOperations;

            $m = $model->toArray();

            // $op = [];
            // $operations->each(function ($operation) use ($op) {$op[] = $operation->all();});

            $timePerPic = 0;
            foreach ($operations as $operation) {

                $o = $operation->toArray();

                // __ Получаем тот дополнительный коэффициент, который нужно умножить на время
                $ratio = is_null($operation->pivot->ratio) || $operation->pivot->ratio === 0 ? 1 : $operation->pivot->ratio;
                $time  = 0;

                if ($operation->type === SewingOperation::DYNAMIC_TYPE) {
                    $time = $operation->time * $size->getPerimeter();
                } elseif ($operation->type === SewingOperation::STATIC_TYPE) {
                    $time = $operation->time;
                }

                $timePerPic += $time * $ratio;
            }

            if ($this->sewingType === SewingTask::FIELD_UNIVERSAL) {
                $this->timeUniversalPerPic = $timePerPic;
            } elseif ($this->sewingType === SewingTask::FIELD_AUTO) {
                $this->timeAutoPerPic = $timePerPic;
            } elseif ($this->sewingType === SewingTask::FIELD_SOLID_HARD) {
                $this->timeSolidHardPerPic = $timePerPic;
            } elseif ($this->sewingType === SewingTask::FIELD_SOLID_LITE) {
                $this->timeSolidLitePerPic = $timePerPic;
            } elseif ($this->sewingType === SewingTask::FIELD_UNDEFINED) {
                $this->timeUndefinedPerPic = $timePerPic;
            }
        }
    }

    // ___ Получаем сами трудозатраты

    /** @noinspection PhpUndefinedFieldInspection */
    private function calcTimeLabor(): void
    {
        if ($this->sewingType === SewingTask::FIELD_AVERAGE) {
            $this->timeUniversal = $this->timeUniversalPerPic * $this->amountUniversal;
            $this->timeAuto      = $this->timeAutoPerPic * $this->amountAuto;
            $this->timeSolidHard = $this->timeSolidHardPerPic * $this->amountSolidHard;
            $this->timeSolidLite = $this->timeSolidLitePerPic * $this->amountSolidLite;
            $this->timeUndefined = $this->timeUndefinedPerPic * $this->amountUndefined;
        } else {
            if ($this->sewingType === SewingTask::FIELD_UNIVERSAL) {
                $this->timeUniversal = $this->timeUniversalPerPic * $this->amountUniversal;
            } elseif ($this->sewingType === SewingTask::FIELD_AUTO) {
                $this->timeAuto = $this->timeAutoPerPic * $this->amountAuto;
            } elseif ($this->sewingType === SewingTask::FIELD_SOLID_HARD) {
                $this->timeSolidHard = $this->timeSolidHardPerPic * $this->amountSolidHard;
            } elseif ($this->sewingType === SewingTask::FIELD_SOLID_LITE) {
                $this->timeSolidLite = $this->timeSolidLitePerPic * $this->amountSolidLite;
            } elseif ($this->sewingType === SewingTask::FIELD_UNDEFINED) {
                $this->timeUndefined = $this->timeUndefinedPerPic * $this->amountUndefined;
            }
        }
    }


    // --- Количество

    // ___ Получаем среднее количество изделий по типу ШМ
    public function getAveragesAmount(): array
    {
        return [
            ModelsService::TYPE_UNIVERSAL  => $this->getAmountUniversal(),
            ModelsService::TYPE_AUTO       => $this->getAmountAuto(),
            ModelsService::TYPE_SOLID_HARD => $this->getAmountSolidHard(),
            ModelsService::TYPE_SOLID_LITE => $this->getAmountSolidLite(),
            ModelsService::TYPE_UNDEFINED  => $this->getAmountUndefined(),
        ];
    }

    // --- Возвращаем количество в числовом формате, те, которые определены (кроме Average)
    public function getAmountUniversal(): int
    {
        return $this->amountUniversal;
    }

    public function getAmountAuto(): int
    {
        return $this->amountAuto;
    }

    public function getAmountSolidHard(): int
    {
        return $this->amountSolidHard;
    }

    public function getAmountSolidLite(): int
    {
        return $this->amountSolidLite;
    }

    public function getAmountUndefined(): int
    {
        return $this->amountUndefined;
    }


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
            SewingTask::FIELD_UNIVERSAL  => $this->getTimeUniversal(),
            SewingTask::FIELD_AUTO       => $this->getTimeAuto(),
            SewingTask::FIELD_SOLID_HARD => $this->getTimeSolidHard(),
            SewingTask::FIELD_SOLID_LITE => $this->getTimeSolidLite(),
            SewingTask::FIELD_UNDEFINED  => $this->getTimeUndefined(),
        ];
    }

    // --- Возвращаем трудозатраты в числовом формате, те, которые определены (кроме Average)
    public function getTimeUniversal(): int
    {
        return $this->timeUniversal;
    }

    public function getTimeAuto(): int
    {
        return $this->timeAuto;
    }

    public function getTimeSolidHard(): int
    {
        return $this->timeSolidHard;
    }

    public function getTimeSolidLite(): int
    {
        return $this->timeSolidLite;
    }

    public function getTimeUndefined(): int
    {
        return $this->timeUndefined;
    }

    // --- Возвращаем трудозатраты в ассоциативном массиве, если Average - то все, разбитые по статистике

    // ___ Получаем трудозатраты в ассоциативном массиве (универсальная функция)
    public function getTime(): int
    {
        return match ($this->sewingType) {
            SewingTask::FIELD_UNIVERSAL  => $this->getTimeUniversal(),
            SewingTask::FIELD_AUTO       => $this->getTimeAuto(),
            SewingTask::FIELD_SOLID_HARD => $this->getTimeSolidHard(),
            SewingTask::FIELD_SOLID_LITE => $this->getTimeSolidLite(),
            SewingTask::FIELD_UNDEFINED  => $this->getTimeUndefined(),
            default                      => 0,
        };
    }


    // ___ Получаем трудозатраты в ассоциативном массиве (универсальная функция)
    public function getTimeArray(): array
    {
        return match ($this->sewingType) {
            SewingTask::FIELD_UNIVERSAL  => $this->getTimeUniversalArray(),
            SewingTask::FIELD_AUTO       => $this->getTimeAutoArray(),
            SewingTask::FIELD_SOLID_HARD => $this->getTimeSolidHardArray(),
            SewingTask::FIELD_SOLID_LITE => $this->getTimeSolidLiteArray(),
            SewingTask::FIELD_UNDEFINED  => $this->getTimeUndefinedArray(),
            SewingTask::FIELD_AVERAGE    => $this->getTimeAverageArray(),
            default                      => [],
        };
    }


    // ___ Получаем трудозатраты в ассоциативном массиве по подменен типа ШМ
    public function getTimeByPhantom(string $phantom): array
    {
        $totalAmount = true;
        /** @noinspection PhpConditionAlreadyCheckedInspection */
        return match ($phantom) {
            SewingTask::FIELD_UNIVERSAL  => $this->getTimeUniversalArray($totalAmount),
            SewingTask::FIELD_AUTO       => $this->getTimeAutoArray($totalAmount),
            SewingTask::FIELD_SOLID_HARD => $this->getTimeSolidHardArray($totalAmount),
            SewingTask::FIELD_SOLID_LITE => $this->getTimeSolidLiteArray($totalAmount),
            SewingTask::FIELD_UNDEFINED  => $this->getTimeUndefinedArray($totalAmount),
            SewingTask::FIELD_AVERAGE    => $this->getTimeAverageArray(),
            default                      => [],
        };
    }


    public function getTimeAverageArray(): array
    {
        return $this->getLaborArray();
    }

    public function getTimeUniversalArray(bool $totalAmount = false): array
    {
        return [SewingTask::FIELD_UNIVERSAL => $totalAmount ? $this->amount * $this->timeUniversalPerPic : $this->timeUniversal];
    }

    public function getTimeAutoArray(bool $totalAmount = false): array
    {
        return [SewingTask::FIELD_AUTO => $totalAmount ? $this->amount * $this->timeAutoPerPic : $this->timeAuto];
    }

    public function getTimeSolidHardArray(bool $totalAmount = false): array
    {
        return [SewingTask::FIELD_SOLID_HARD => $totalAmount ? $this->amount * $this->timeSolidHardPerPic : $this->timeSolidHard];
    }

    public function getTimeSolidLiteArray(bool $totalAmount = false): array
    {
        return [SewingTask::FIELD_SOLID_LITE => $totalAmount ? $this->amount * $this->timeSolidLitePerPic : $this->timeSolidLite];
    }

    public function getTimeUndefinedArray(bool $totalAmount = false): array
    {
        return [SewingTask::FIELD_UNDEFINED => $totalAmount ? $this->amount * $this->timeUndefinedPerPic : $this->timeUndefined];
    }


    // ___ Корректируем количество, если оно не целое
    private function correctAmounts(): void
    {
        // __ 1. Список свойств для обработки
        $propertyNames = [
            'amountUniversal',
            'amountAuto',
            'amountSolidHard',
            'amountSolidLite',
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

}




