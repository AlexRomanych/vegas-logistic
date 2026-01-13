<?php

namespace App\Classes;

use App\Models\Manufacture\Cells\Sewing\SewingTask;
use App\Models\Manufacture\Cells\Sewing\SewingTaskLine;
use App\Models\Models\Model;
use App\Models\Order\OrderLine;
use App\Models\Shared\Size;
use App\Services\ModelsService;
use App\Services\SizeService;

class SewingTimeLabor
{
    private int $timeUniversal = 0;
    private int $timeAuto = 0;
    private int $timeSolidHard = 0;
    private int $timeSolidLite = 0;
    private int $timeUndefined = 0;
    private int $amount = 0;
    private string $sewingType = '';


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
        $this->amount = $amount;

        // __ Если передана строка Сменного задания
        if ($sewingTaskLine /*&& $sewingTaskLine instanceof SewingTaskLine*/) {
            $orderLine = OrderLine::query()->find($sewingTaskLine->order_line_id);  // __ Получаем Контекст Строки Заказа
            if ($orderLine) {
                $model = ModelsService::getModelByCode1C($orderLine->model_code_1c); // __ Получаем Модель
                if ($model) {
                    $this->setSewingType($model);
                    $size = new Size($orderLine->width, $orderLine->length, $orderLine->height); // __ Получаем Размер
                    // $size = $this->getSize($orderLine->size);
                    $this->calcTimeLabor($model, $size, $orderLine->amount);
                }
            }
        } else {
            if ($model && $size && $amount !== 0) {
                // __ Находим модель
                $workModel = $this->getModel($model);
                if ($workModel) {
                    $this->setSewingType($workModel);
                    $workSize = $this->getSize($size);
                    if ($workSize) {
                        $this->calcTimeLabor($workModel, $workSize, $amount);
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
                $model->is_undefined  => SewingTask::FIELD_UNDEFINED,
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
        return $workSize;
    }

    // ___ Получаем сами трудозатраты

    /** @noinspection PhpUndefinedFieldInspection */
    private function calcTimeLabor(Model $model, Size $size, int $amount): void
    {
        // !!! __ TODO: Тут вся логика по трудозатратам
        // !!! __ TODO: Нужно сходить в БД и получить трудозатраты - Сходим
        // !!! __ TODO: Нужно привязать модель к размеру - Привязываем
        // !!! __ TODO: Нужно сделать массив с расчетным количеством изделий - Считаем и Выдаем


        if (ModelsService::isElementAverage($model)) {
            $this->timeUniversal = /*$this->getTimeLabor()*/
                100 * $amount * 0.4; // __ 40%
            $this->timeAuto      = /*$this->getTimeLabor()*/
                100 * $amount * 0.3;
            $this->timeSolidHard = /*$this->getTimeLabor()*/
                100 * $amount * 0.15;
            $this->timeSolidLite = /*$this->getTimeLabor()*/
                100 * $amount * 0.1;
            $this->timeUndefined = /*$this->getTimeLabor()*/
                100 * $amount * 0.05;
        } else {
            //['is_auto', 'is_universal', 'is_solid', 'is_solid_lite', 'is_solid_hard', 'is_undefined'];
            if ($model->is_universal) {
                $this->timeUniversal = /*$this->getTimeLabor()*/
                    100 * $amount;
            } elseif ($model->is_auto) {
                $this->timeAuto = /*$this->getTimeLabor()*/
                    150 * $amount;
            } elseif ($model->is_solid_hard) {
                $this->timeSolidHard = /*$this->getTimeLabor()*/
                    200 * $amount;
            } elseif ($model->is_solid_lite) {
                $this->timeSolidLite = /*$this->getTimeLabor()*/
                    250 * $amount;
            } elseif ($model->is_undefined) {
                $this->timeUndefined = /*$this->getTimeLabor()*/
                    300 * $amount;
            }
        }
    }

    private function getTimeLabor(): int
    {
        $scale = 1;
        $max   = 100;
        return mt_rand(1, $max) * $scale;
    }


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

    // ___ Получаем среднее количество изделий по типу ШМ
    public function getAveragesAmount(): array
    {
        return [
            SewingTask::FIELD_UNIVERSAL  => $this->amount * 0.4,
            SewingTask::FIELD_AUTO       => $this->amount * 0.3,
            SewingTask::FIELD_SOLID_HARD => $this->amount * 0.15,
            SewingTask::FIELD_SOLID_LITE => $this->amount * 0.1,
            SewingTask::FIELD_UNDEFINED  => $this->amount * 0.05,
        ];
    }

    private function getLaborArray(): array
    {
        return [
            SewingTask::FIELD_UNIVERSAL  => $this->timeUniversal,
            SewingTask::FIELD_AUTO       => $this->timeAuto,
            SewingTask::FIELD_SOLID_HARD => $this->timeSolidHard,
            SewingTask::FIELD_SOLID_LITE => $this->timeSolidLite,
            SewingTask::FIELD_UNDEFINED  => $this->timeUndefined,
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
    public function getTime(): array
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

    public function getTimeAverageArray(): array
    {
        return $this->getLaborArray();
    }

    public function getTimeUniversalArray(): array
    {
        return [SewingTask::FIELD_UNIVERSAL => $this->timeUniversal];
    }

    public function getTimeAutoArray(): array
    {
        return [SewingTask::FIELD_AUTO => $this->timeAuto];
    }

    public function getTimeSolidHardArray(): array
    {
        return [SewingTask::FIELD_SOLID_HARD => $this->timeSolidHard];
    }

    public function getTimeSolidLiteArray(): array
    {
        return [SewingTask::FIELD_SOLID_LITE => $this->timeSolidLite];
    }

    public function getTimeUndefinedArray(): array
    {
        return [SewingTask::FIELD_UNDEFINED => $this->timeUndefined];
    }

}




