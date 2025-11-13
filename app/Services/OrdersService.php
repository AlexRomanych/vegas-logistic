<?php

namespace App\Services;

use App\Contracts\VegasDataGetContract;
use App\Contracts\VegasDataUpdateContract;
use App\Facades\Plan;
use App\Models\Shared\Period;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use App\Models\Order\{Order, AssemblyPart, Line, OrderType};


final class OrdersService implements VegasDataUpdateContract
{

    private Period $period;                 // заданный период вывода информации
    private Period $renderPeriod;           // отображаемый период (первый понедельник - последнее воскресение)
    private int $weeksAmount;               // количество недель для рендеринга плана


    private static array $orderTypesCache = [];     // Типы заявок

    public function __construct(public VegasDataGetContract $getter)
    {
    }

    /**
     * Возвращает массив заказов для отображения плана в зависимости от типа
     * @param Period|null $inPeriod Период плана
     * @param string|null $type Тип плана
     * @param bool|null $ext Подгружать или нет все основные данные
     * @return array
     */
    public function selectOrders(?Period $inPeriod = null, ?string $type = ASSEMBLY_PLAN_TYPE, ?bool $ext = true): array
    {
        $period = $inPeriod ?? Plan::getPeriod();

        $relations = match (strtolower($type)) {
            ASSEMBLY_PLAN_TYPE => 'assemblyParts',
            SEWING_PLAN_TYPE => 'sewingParts',
            CUTTING_PLAN_TYPE => 'cuttingParts',
            LOADS_PLAN_TYPE => LOADS_PLAN_TYPE,
            default => null
        };

        // Важен порядок
        if ($relations === LOADS_PLAN_TYPE) {

            $orders = Order::query()
                ->where('load_date', '>=', $period->getStart())
                ->where('load_date', '<=', $period->getEnd())
                ->with('assemblyParts')
                ->get()
                ->toArray();

        } elseif (!is_null($relations)) {

            $query = Order::whereHas($relations, function (Builder $query) use ($period) {
                $query
                    ->where('manufacture_date', '>=', $period->getStart())
                    ->where('manufacture_date', '<=', $period->getEnd());
            });

            if ($ext) {
                $query = $query->with($relations);
            }

            $orders = $query->get()->toArray();
        } else {

            $orders = Order::query()
                ->where('plan_period', '>=', $period->getStart())
                ->where('plan_period', '<=', $period->getEnd())
                ->get()
                ->toArray();

        }

        return $orders;
    }


    /**
     * Возвращает подготовленный массив для отображения плана сборки в штуках
     * @param array $orders
     * @param Period|null $period
     * @param Period|null $renderPeriod
     * @param int|null $weeksAmount
     * @return array
     */
    public function getRenderAssemblyPicsData(
        array    $orders = [],
        Period   $period = null,
        Period   &$renderPeriod = null,
        int|null &$weeksAmount = null): array
    {

        if (!$this->checkPlanData($orders, $period)) {
            return [];
        }

        // Возвращаем по ссылке переменные для отображения во view
        $renderPeriod = $this->renderPeriod;
        $weeksAmount = $this->weeksAmount;

        $renderData = [];

        $tempDate = new Carbon($this->renderPeriod->getStart());
        $key = 1;

        for ($i = 1; $i <= $this->weeksAmount; $i++) {

            for ($j = 1; $j <= 7; $j++) {

                //                dump($key);
                $totalAmountsInPics = [
                    'm' => 0,
                    'u' => 0,
                    'c' => 0,
                    'f' => 0,
                ];

                $renderData[$key] = [
                    //                    'title' => $tempDate->format('d.m.Y\(D\)')
                    'title' => $tempDate->format('d.m.Y')
                    //                    'title' => $tempDate->toDateString()
                ];

                //                dd($renderData);

                $renderParts = [];

                foreach ($orders as $order) {

                    foreach ($order['assembly_parts'] as $assemblyPart) {

                        $manufactureDate = new Carbon($assemblyPart['manufacture_date']);

                        if ($tempDate->equalTo($manufactureDate) && $tempDate->greaterThanOrEqualTo($period->getStart()) && $tempDate->lessThanOrEqualTo($period->getEnd())) {

                            $amountsInPics = \App\Facades\Order::getTypesAmountInPart($assemblyPart);

                            $openStatus = \App\Facades\Order::isOpenPart($assemblyPart);

                            $renderPart = [

                                'no'     => (double)$order['no_num'],
                                'client' => $order['client']['short_name'],
                                'os'     => $openStatus,
                                'm'      => $amountsInPics['mattresses'],
                                'u'      => $amountsInPics['up_covers'],
                                'c'      => $amountsInPics['children'],
                                'f'      => $amountsInPics['formats'],

                            ];

                            $renderParts[] = $renderPart;

                            $totalAmountsInPics['m'] = $totalAmountsInPics['m'] + $renderPart['m'];
                            $totalAmountsInPics['u'] = $totalAmountsInPics['u'] + $renderPart['u'];
                            $totalAmountsInPics['c'] = $totalAmountsInPics['c'] + $renderPart['c'];
                            $totalAmountsInPics['f'] = $totalAmountsInPics['f'] + $renderPart['f'];

                        }

                    }
                }

                $totalAmountsInPics['t'] =
                    $totalAmountsInPics['m'] +
                    $totalAmountsInPics['u'] +
                    $totalAmountsInPics['c'] +
                    $totalAmountsInPics['f'];

                $renderData[$key] = array_merge($renderData[$key], [
                    'data'  => $renderParts,
                    'total' => $totalAmountsInPics
                ]);

                $tempDate = $tempDate->addDay();
                $key++;

            }
        }

        return $renderData;
    }


    /**
     * Возвращает подготовленный массив для отображения плана сборки в штуках
     * @param array $orders
     * @param Period|null $period
     * @param Period|null $renderPeriod
     * @param int|null $weeksAmount
     * @return array
     */
    public function getRenderUnloadsPicsData(
        array    $orders = [],
        Period   $period = null,
        Period   &$renderPeriod = null,
        int|null &$weeksAmount = null): array
    {

        if (!$this->checkPlanData($orders, $period)) {
            return [];
        }

        // Возвращаем по ссылке переменные для отображения во view
        $renderPeriod = $this->renderPeriod;
        $weeksAmount = $this->weeksAmount;

        $renderData = [];

        $tempDate = new Carbon($this->renderPeriod->getStart());
        $key = 1;

        for ($i = 1; $i <= $this->weeksAmount; $i++) {

            for ($j = 1; $j <= 7; $j++) {

                $totalAmountsInPics = [
                    'm' => 0,
                    'u' => 0,
                    'c' => 0,
                    'f' => 0,
                ];

                $renderData[$key] = [
                    'title' => $tempDate->format('d.m.Y')
                ];


                $renderOrders = [];

                foreach ($orders as $order) {
                    $unloadDate = new Carbon($order['load_date']);

                    if ($tempDate->equalTo($unloadDate)) {
                        $amountsInPics = \App\Facades\Order::getTypesAmountInOrder($order);
                        $openStatus = \App\Facades\Order::isOpenOrder($order);

                        $renderOrder = [
                            'no'     => (double)$order['no_num'],
                            'client' => $order['client']['short_name'],
                            'load'   => $unloadDate->format('d.m.Y'),
                            'os'     => $openStatus,
                            'm'      => $amountsInPics['mattresses'],
                            'u'      => $amountsInPics['up_covers'],
                            'c'      => $amountsInPics['children'],
                            'f'      => $amountsInPics['formats'],
                        ];

                        $renderOrders[] = $renderOrder;


                        $totalAmountsInPics['m'] = $totalAmountsInPics['m'] + $renderOrder['m'];
                        $totalAmountsInPics['u'] = $totalAmountsInPics['u'] + $renderOrder['u'];
                        $totalAmountsInPics['c'] = $totalAmountsInPics['c'] + $renderOrder['c'];
                        $totalAmountsInPics['f'] = $totalAmountsInPics['f'] + $renderOrder['f'];
                    }
                }

                $totalAmountsInPics['t'] =
                    $totalAmountsInPics['m'] +
                    $totalAmountsInPics['u'] +
                    $totalAmountsInPics['c'] +
                    $totalAmountsInPics['f'];

                $renderData[$key] = array_merge($renderData[$key], [
                    'data'  => $renderOrders,
                    'total' => $totalAmountsInPics
                ]);

                $tempDate = $tempDate->addDay();
                $key++;

            }
        }

        return $renderData;
    }


    /**
     * Проверяет на вшивость данные и устанавливает при необходимости даты
     * @param array $orders
     * @param Period|null $period
     * @return bool
     */
    private function checkPlanData(array $orders = [], Period $period = null): bool
    {
        if (count($orders, COUNT_RECURSIVE) === 0) {
            return false;
        }

        if (is_null($period)) {
            $this->period = Plan::getPeriod();
            $this->renderPeriod = Plan::getPeriodRender();
        } else {
            $this->period = $period;
            $this->renderPeriod = Plan::getPeriodRender($period->getStart(), $period->getEnd());
        }

        $this->weeksAmount = Plan::getWeeksAmount($this->renderPeriod);

        return true;
    }


    public function updateData(VegasDataGetContract $getter = null): void
    {
        if (!$this->getter) {
            $this->getter = $getter;
        }

        Schema::disableForeignKeyConstraints();   // выключить ограничения внешнего ключа

        $this->fillOrders();
        $this->fillParts();
        $this->fillLines();

        Schema::enableForeignKeyConstraints();    // включить ограничения внешнего ключа
    }

    // Заполняем заказы
    private function fillOrders(): void
    {
        $fileName = config('vegas.orders_EPS_json_name');
        $orders = $this->getData($fileName);

        Order::query()->truncate(); // Очистка таблицы

        foreach ($orders as $order) {

            try {

                Order::query()->forceCreate(
                    [
                        'id' => $order['id'],

                        'no_origin'         => $order['no'],
                        'no_num'            => $order['nn'],
                        'plan_period'       => new Carbon($order['pp']),
                        'load_date'         => new Carbon($order['ld']),
                        'unload_date'       => $order['ud'] === '' ? null : new Carbon($order['ud']),
                        'description'       => $order['dn'] === '' ? null : $order['dn'],
                        'manager_start'     => $order['ms'] === '' ? null : new Carbon($order['ms']),
                        'manager_end'       => $order['me'] === '' ? null : new Carbon($order['me']),
                        'comment'           => $order['cm'],
                        'responsible'       => $order['re'],
                        'manager_load_date' => $order['ml'] === '' ? null : new Carbon($order['ml']),
                        'manuf_date_1C'     => $order['ld1C'] === '' ? null : new Carbon($order['ld1C']),
                        'design_start'      => $order['ds'] === '' ? null : new Carbon($order['ds']),
                        'design_end'        => $order['de'] === '' ? null : new Carbon($order['de']),
                        'service_text'      => $order['si'] === '' ? null : $order['si'],

                        'client_id' => $order['ci'],
                    ]
                );

            } catch (Exception $e) {
                dd($order);
            }

        }
    }

    // Здесь только частички производственные сборки
    private function fillParts(): void
    {
        $fileName = config('vegas.parts_EPS_json_name');
        $parts = $this->getData($fileName);

        AssemblyPart::query()->truncate(); // Очистка таблицы

        foreach ($parts as $part) {
            AssemblyPart::query()->forceCreate(
                [
                    'id'               => $part['id'],
                    'manufacture_date' => new Carbon($part['md']),
                    'order_id'         => $part['oi'],
                ]
            );
        }
    }

    // Заполняем сами данные
    private function fillLines(): void
    {
        //        $fileName = config('vegas.lines_EPS_json_name');
        //        result = $this->getData($fileName);


        $file = File::get(storage_path('\\app\\data1c\\lines.json'));

        $pointer = 0;
        $result = [];

        while ($file[$pointer] !== ']') {
            if ($file[$pointer] === '{') {
                $jsonPart = $file[$pointer];
                $pointer++;

                while ($file[$pointer] !== '}') {
                    $jsonPart = $jsonPart . $file[$pointer];
                    $pointer++;
                }

                $jsonPart = $jsonPart . $file[$pointer];
                $result[] = json_decode($jsonPart, true);   // в виде ассоциативного массива

            }
            $pointer++;
        }

        Line::query()->truncate(); // Очистка таблицы

        //        $linesCollect[] = [];

        foreach ($result as $key => $line) {
            try {
                Line::query()->forceCreate(
                    [
                        'load_1C'          => new Carbon($line['m']),
                        'size'             => $line['s'],
                        'amount'           => $line['a'],
                        'name'             => $line['n'],
                        'textile'          => $line['t'],
                        'comment'          => strlen($line['d']) <= 255 ? $line['d'] : mb_strcut($line['d'], 1, 255),
                        'describe_1'       => strlen($line['d1']) <= 255 ? $line['d1'] : mb_strcut($line['d'], 1, 255),
                        'describe_2'       => strlen($line['d2']) <= 255 ? $line['d2'] : mb_strcut($line['d'], 1, 255),
                        'describe_3'       => strlen($line['d3']) <= 255 ? $line['d3'] : mb_strcut($line['d'], 1, 255),
                        'assembly_part_id' => $line['p'],
                        'model_code1C'     => $line['e'],
                    ]
                );

                if ($key % 5000 === 0) {
                    dump($key . ' --> ' . Carbon::now()->toTimeString());
                }
            } catch (Exception $e) {
                dd($key, $e, $line);
            }


            //            try {
            //                $linesCollect[] = [
            //
            //                    'load_1C'          => new Carbon($line['m']),
            //                    'size'             => $line['s'],
            //                    'amount'           => $line['a'],
            //                    'name'             => $line['n'],
            //                    'textile'          => $line['t'],
            //                    'comment'          => $line['d'],
            //                    'describe_1'       => $line['d1'],
            //                    'describe_2'       => $line['d2'],
            //                    'describe_3'       => $line['d3'],
            //                    'assembly_part_id' => $line['p'],
            //                    'model_code1C'     => $line['e'],
            //
            //                ];
            //            } catch (Exception $e) {
            //                dd($key, $e, $result[11782]);
            //            }


        }

        //        Line::query()->upsert([
        //            ...$linesCollect
        //
        //        ],
        //        ['id'],
        //        ['load_1C',
        //         'size',
        //         'amount',
        //         'name',
        //         'textile',
        //         'comment',
        //         'describe_1',
        //         'describe_2',
        //         'describe_3',
        //         'assembly_part_id',
        //         'model_code1C']);

    }

    private function getData(string $filename): array
    {
        return $this->getter->getDataFromFile($filename);
    }


    /**
     * ___ Возвращает массив типов заказов
     * @return array
     */
    public static function getOrderTypes(): array
    {
        try {
            $orderTypes = OrderType::all();
            foreach ($orderTypes as $orderType) {
                self::$orderTypesCache[$orderType->type_index] = $orderType;
            }
            return self::$orderTypesCache;
        } catch (Exception $e) {
            self::$orderTypesCache = [];
            return [];
        }
    }

    /**
     * ___ Возвращает тип заказа по индексу
     * @param string $typeIndex
     * @return OrderType|null
     */
    public static function getOrderTypeByIndex(string $typeIndex): ?OrderType
    {
        if (count(self::$orderTypesCache) === 0) {
            self::getOrderTypes();
        }

        if (isset(self::$orderTypesCache[$typeIndex])) {
            return self::$orderTypesCache[$typeIndex];
        }

        return null;
    }


    /**
     * ___ Возвращает тип заявки по номеру заявки
     * @param string $orderNo
     * @param int $clientID
     * @return OrderType
     */
    public static function getOrderTypeByOrderNoAndClientId(string $orderNo, int $clientID = 0): OrderType
    {
        $orderNo = trim($orderNo);
        $orderNo = str_replace(',', '.', $orderNo);
        $orderNo = getDigitPartAndDotAndComma($orderNo);

        if (!is_numeric($orderNo)) {
            return self::getOrderTypeByIndex('.00');       // неизвестный тип заказа, если номер не число
        }

        if (!str_contains($orderNo, '.')) {
            return self::getOrderTypeByIndex('_');         // основной тип заказа, если нет точки
        }

        $parts = explode('.', $orderNo, 2);

        if (ClientsService::isClient_LMM($clientID)) {
            if (in_array($parts[1], ['1', '2'])) {
                return self::getOrderTypeByIndex('.' . $parts[1]);     // Дополнительная заявка ЛММ
            }
        }

        if (isset(self::$orderTypesCache['.' . $parts[1]])) {
            return self::getOrderTypeByIndex('.' . $parts[1]);
        }

        if (in_array($parts[1], ['1', '2', '3', '4', '5', '6', '7', '8', '9'])) {
            return self::getOrderTypeByIndex('.1-10; .20-...');     // Дополнительная заявка
        }

        return self::getOrderTypeByIndex('.00');
    }

}

