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

    /**
     * ___ Получаем трудозатраты либо по Строке СЗ, либо по Модели, Размеру и количеству
     * @param SewingTaskLine|null $sewingTaskLine **Сменное задание (Строка СЗ (SewingLine))**
     * @param string|Model|null $model __Модель__
     * @param string|Size|null $size __Размер__
     * @param int $amount __Количество__
     */
    public function __construct(
        SewingTaskLine|null $sewingTaskLine = null,
        string|Model|null   $model = null,
        string|Size|null    $size = null,
        int                 $amount = 1)
    {
        // __ Если передана строка Сменного задания
        if ($sewingTaskLine /*&& $sewingTaskLine instanceof SewingTaskLine*/) {
            $orderLine = OrderLine::query()->find($sewingTaskLine->order_line_id);  // __ Получаем Контекст Строки Заказа
            if ($orderLine) {
                $model = ModelsService::getModelByCode1C($orderLine->model_code_1c); // __ Получаем Модель
                if ($model) {
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
                    $workSize = $this->getSize($size);
                    if ($workSize) {
                        $this->calcTimeLabor($workModel, $workSize, $amount);
                    }
                }
            }
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
            $this->timeUniversal = $this->getTimeLabor() * $amount;
            $this->timeAuto      = $this->getTimeLabor() * $amount;
            $this->timeSolidHard = $this->getTimeLabor() * $amount;
            $this->timeSolidLite = $this->getTimeLabor() * $amount;
            $this->timeUndefined = $this->getTimeLabor() * $amount;
        } else {
            //['is_auto', 'is_universal', 'is_solid', 'is_solid_lite', 'is_solid_hard', 'is_undefined'];
            if ($model->is_universal) {
                $this->timeUniversal = $this->getTimeLabor() * $amount;
            } elseif ($model->is_auto) {
                $this->timeAuto = $this->getTimeLabor() * $amount;
            } elseif ($model->is_solid_hard) {
                $this->timeSolidHard = $this->getTimeLabor() * $amount;
            } elseif ($model->is_solid_lite) {
                $this->timeSolidLite = $this->getTimeLabor() * $amount;
            } elseif ($model->is_undefined) {
                $this->timeUndefined = $this->getTimeLabor() * $amount;
            }
        }
    }

    private function getTimeLabor(): int
    {
        $scale = 1;
        $max   = 100;
        return mt_rand(1, $max) * $scale;
    }

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
            SewingTask::FIELD_UNIVERSAL  => 0,
            SewingTask::FIELD_AUTO       => 0,
            SewingTask::FIELD_SOLID_HARD => 0,
            SewingTask::FIELD_SOLID_LITE => 0,
            SewingTask::FIELD_UNDEFINED  => 0,
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
}
