<?php

namespace App\Models\Shared\Facades;

use App\Facades\Model;
//use App\Models\Model as VegasModel;
use App\Facades\Size;
use App\Models\Abstract\Part;
use App\Models\Order\Order;

class OrderFacade
{

    /**
     * Возвращает количество моделей по типу во всем заказе
     * @param Order|array|null $order
     * @return int[]
     */
    public function getTypesAmountInOrder(Order|array|null $order = null): array
    {
        $result = $this->setReturnAmountsToZero();

        if (is_null($order)) {
            return $result;
        }

        if ($order instanceof Order) {
            $order = $order->toArray();
        }

        $parts =  $order['assembly_parts'] ?? $order['sewing_parts'] ?? $order['cutting_parts'];

        foreach ($parts as $part) {

            $partResult = $this->getTypesAmountInPart($part);

            // Складываем результирующие массивы по одинаковым ключам
//            array_walk($result, function(&$value, $key, $partResult){
//                return $value = $value + $partResult[$key];
//            }, $partResult);

            array_walk($result, fn(&$value, $key, $partResult) => $value = $value + $partResult[$key], $partResult);


//            $result['mattresses'] = $result['mattresses'] + $partResult['mattresses'];
//            $result['m'] = $result['m'] + $partResult['m'];
//            $result['up_covers'] = $result['up_covers'] + $partResult['up_covers'];
//            $result['u'] = $result['u'] + $partResult['u'];
//            $result['children'] = $result['children'] + $partResult['children'];
//            $result['c'] = $result['c'] + $partResult['c'];



//                'mattresses'  => $mattresses,   'm' => $mattresses,
//                'up_covers'   => $up_covers,    'u' => $up_covers,
//                'children'    => $children,     'c' => $children,
//                'formats'     => $formats,      'f' => $formats,
//                'pillows'     => $pillows,      'p' => $pillows,
//                'duvet'       => $duvet,        'd' => $duvet,
//                'accessories' => $accessories,  'a' => $accessories,
//                'others'      => $others,       'o' => $others,
//            ];



        }



        return $result;
    }


    /**
     * Возвращает количество по типам в производственной части
     * @param Part|array|null $part
     * @return array|int[]
     */
    public function getTypesAmountInPart(Part|array|null $part = null): array
    {
        $result = $this->setReturnAmountsToZero();

        if (is_null($part)) {
            return $result;
        }

        if ($part instanceof Part) {
            $part = $part->toArray();
        }

        $mattresses = 0;
        $up_covers = 0;
        $children = 0;
        $formats = 0;
        $pillows = 0;
        $duvet = 0;
        $accessories = 0;
        $others = 0;

        foreach ($part['lines'] as $line) {

            $modelName = $line['model']['name'];
            $size = $line['size'];
            $amount = $line['amount'];

            // warning Очень важен порядок
            if (Model::isAverage($modelName)) {
                $mattresses += $amount;
            } elseif (Size::isFormat($size)) {
                $formats += $amount;
            } elseif (Model::isChildren($modelName)) {
                $children += $amount;
            } elseif (Model::isMattress($modelName)) {
                $mattresses += $amount;
            } elseif (Model::isUpCover($modelName)) {
                $up_covers += $amount;
            } elseif (Model::isPillow($modelName)) {
                $pillows += $amount;
            } elseif (Model::isDuvet($modelName)) {
                $duvet += $amount;
            } elseif (Model::isAccessory($modelName)) {
                $accessories += $amount;
            } else {
                $others += $amount;
            }
        }

        return [
            'mattresses'  => $mattresses,   'm' => $mattresses,
            'up_covers'   => $up_covers,    'u' => $up_covers,
            'children'    => $children,     'c' => $children,
            'formats'     => $formats,      'f' => $formats,
            'pillows'     => $pillows,      'p' => $pillows,
            'duvet'       => $duvet,        'd' => $duvet,
            'accessories' => $accessories,  'a' => $accessories,
            'others'      => $others,       'o' => $others,
        ];
    }


    /**
     * Сбрасывает результирующий массив
     * @return int[]
     */
    private function setReturnAmountsToZero(): array
    {
        return [
            'mattresses'  => 0, 'm' => 0,
            'up_covers'   => 0, 'u' => 0,
            'children'    => 0, 'c' => 0,
            'formats'     => 0, 'f' => 0,
            'pillows'     => 0, 'p' => 0,
            'duvet'       => 0, 'd' => 0,
            'accessories' => 0, 'a' => 0,
            'others'      => 0, 'o' => 0,
        ];
    }


    /**
     * Проверяет, является ли производственная часть со средними моделями
     * @param Part|array|null $part
     * @return bool
     */
    public function isAveragePart(Part|array|null $part = null): bool
    {
        if (is_null($part)) {
            return false;
        }

        if ($part instanceof Part) {
            $part = $part->toArray();
        }

        foreach ($part['lines'] as $line) {

            if (Model::isAverage($line['model']['name'])) {
                return true;
            }
        }
        return false;
    }


    /**
     * Проверяет, является ли часть открытой (загруженной в 1С)
     * @param Part|array|null $part
     * @return bool
     */
    public function isOpenPart(Part|array|null $part = null): bool
    {
        return ! $this->isAveragePart($part);
    }


    /**
     * Проверяет, является ли заказ со средними (расчетными) моделями
     * @param Order|array|null $order
     * @return bool
     */
    public function isAverageOrder(Order|array|null $order = null): bool
    {
        if (is_null($order)) {
            return false;
        }

        if ($order instanceof Order) {
            $order = $order->toArray();
        }

        $parts =  $order['assembly_parts'] ?? $order['sewing_parts'] ?? $order['cutting_parts'];

        foreach ($parts as $part) {
            if ($this->isAveragePart($part)) {
                return true;
            }
        }

        return false;
    }


    /**
     * Проверяет, является ли заказ открытым (загруженным в 1С)
     * @param Order|array|null $order
     * @return bool
     */
    public function isOpenOrder(Order|array|null $order = null): bool
    {
        return ! $this->isAverageOrder($order);
    }

}
