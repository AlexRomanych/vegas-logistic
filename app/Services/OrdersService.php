<?php

namespace App\Services;


use App\Classes\EndPointStaticRequestAnswer;
use App\Enums\ElementTypes;
use App\Models\Client;
use App\Models\Order\Order;
use App\Models\Order\OrderLine;
use App\Models\Order\OrderStatus;
use App\Models\Order\OrderType;
use App\Models\Plan\PlanLoad;
use App\Services\Manufacture\SewingService;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

final class OrdersService
{

    public const VALIDATE_FIELD = 'validate';
    public const CHECK_FIELD = 'check';
    public const ADVICE_FIELD = 'advice';
    public const ORDER_ELEMENTS_TYPE_FIELD = 'elements_type';
    public const ACTION_FIELD = 'action';
    public const CHECK_PASS = 'ok';
    public const CHECK_FAIL = 'fail';
    public const ACTION_NONE = 'Нет действия';
    public const ACTION_CLIENT_IGNORE = 'Игнорировать Клиента';
    public const ACTION_CLIENT_ADD = 'Создать Клиента';
    public const ACTION_ORDER_UPDATE = 'Обновить Заявку';
    public const ACTION_ORDER_IGNORE = 'Игнорировать Заявку';
    public const ACTION_ORDER_ADD = 'Создать Заявку';
    public const ACTION_MODEL_ADD = 'Создать Модель';
    public const ACTION_MODEL_IGNORE = 'Игнорировать Модель';
    public const ORDER_ID_FIELD = 'order_id';


    private static array $orderTypesCache = [];     // Типы заявок


    // ___ Проверяем Заявки перед загрузкой в БД на вшивость
    public static function validateOrders(array &$orders): array
    {
        // __ Поля, которые получаем из базы, если заявка существует
        // $ORDER_METADATA_FIELD = 'metadata';
        // $CLIENT_SHORT_NAME_FIELD = 'client_short_name';
        // $ORDER_NO_FIELD = 'order_no';
        // $ORDER_PERIOD_FIELD = 'order_period';

        // __ Подготавливаем данные (если строка json, преобразуем в массив)
        $orders = self::prepareOrderData($orders);


        // __ Проходим по всем Заявкам
        foreach ($orders as &$order) {

            // __ Получаем Тип изделий в Заявках
            $elementsTypeData        = [];
            $elementsTypeDataPercent = [];
            $elementsType            = self::getOrderElementsTypeFromFront($order, $elementsTypeData, $elementsTypeDataPercent);
            $elementsTypeRef         = self::getOrderElementsTypeReference($elementsType);

            // __ Проверяем на то, что все изделия в Заявке - чехлы (то есть, !!! 100% - это чехлы)
            // __ Считаем общее количество изделий в Заявке
            $totals = array_reduce($order['items'], fn($carry, $item) => $carry + $item['a'], 0);
            if (isset($elementsTypeData[ElementTypes::COVERS->value]) && $elementsTypeData[ElementTypes::COVERS->value] === $totals) {
                $order[self::ORDER_ELEMENTS_TYPE_FIELD] = self::getElementsTypeRender(ElementTypes::COVERS->value);
            } else {
                $order[self::ORDER_ELEMENTS_TYPE_FIELD] = self::getElementsTypeRender($elementsTypeRef);
            }

            // __ Пробуем получить клиента по ID
            $client = null;
            if ($order['client_id'] === 0) { // Приходит с фронта сразу не определенный ID, пробуем его найти по code_1c
                $client = Client::query()->where(CODE_1C, $order['client_code'])->first();
            } else {
                $client = Client::query()->find($order['client_id']);
            }

            // __ Если не нашли клиента по ID или code_1c
            if (!$client) {

                // __ Действие зависит от того, какого типа заявка
                if (self::isOrderUndefinedType($elementsTypeRef)) {
                    if (self::isOrderCoversType($elementsType)) {  // Если референсный тип Не определен, но есть Чехлы - предлагаем добавить клиента
                        $order[self::VALIDATE_FIELD][self::CHECK_FIELD]  = 'Клиент отсутствует в базе, но Тип изделий в заявке - Чехлы.';
                        $order[self::VALIDATE_FIELD][self::ACTION_FIELD] = self::ACTION_CLIENT_ADD;
                        $order[self::VALIDATE_FIELD][self::ADVICE_FIELD] = 'Добавить клиента здесь или через Справочник Клиентов и заново загрузить Заявки. Код клиента = ' . $order['client_code'];
                    } else {
                        $order[self::VALIDATE_FIELD][self::CHECK_FIELD]  = 'Клиент отсутствует в базе и Тип изделий в заявке не определен.';
                        $order[self::VALIDATE_FIELD][self::ACTION_FIELD] = self::ACTION_CLIENT_IGNORE;
                        $order[self::VALIDATE_FIELD][self::ADVICE_FIELD] = 'Пропустить Заявку. Возможно это не матрасы или аксессуары.';
                    }

                } else {
                    $order[self::VALIDATE_FIELD][self::CHECK_FIELD]  = 'Клиент отсутствует в базе.';
                    $order[self::VALIDATE_FIELD][self::ACTION_FIELD] = self::ACTION_CLIENT_ADD;
                    $order[self::VALIDATE_FIELD][self::ADVICE_FIELD] = 'Добавить клиента здесь или через Справочник Клиентов и заново загрузить Заявки. Код клиента = ' . $order['client_code'];
                }

                $order['client_id'] = 0;                  // Перезаписываем id клиента из БД, если даже он есть в excel

            } else { // __ Если нашли клиента

                $order['client_full_name'] = $client->short_name;          // Перезаписываем имя клиента из БД
                $order['client_id']        = $client->id;                  // Перезаписываем id клиента из БД, если нашли по коду из 1С

                if (self::isOrderCoversType($elementsType)) {

                    $order[self::VALIDATE_FIELD][self::CHECK_FIELD]  = 'Изделия в Заявке - Чехлы.';
                    $order[self::VALIDATE_FIELD][self::ACTION_FIELD] = self::ACTION_ORDER_IGNORE;
                    $order[self::VALIDATE_FIELD][self::ADVICE_FIELD] = 'Загружать эту заявку не нужно. СЗ с Чехлами будут созданы автоматически';

                } else {

                    // __ Пробуем найти Заявку в БД, причем с учетом периода, если не нашли - пробуем соседние периоды
                    // __ Вероятность, что Заявка у такого клиента с таким номером попадет другой период (+- год) практически нулевая
                    // __ Перебираем периоды, чтобы наверняка исключить косяки с датами из 1С
                    $existOrder = self::getOrderByClientIdOrderNoElementsTypeLoadAt(
                        $client->id,
                        $order['order_no'],
                        $elementsTypeRef,
                        $order['load_at']
                    );

                    // __ Если не нашли Заявку с таким номером в БД, пробуем найти по коду из 1С
                    if (!$existOrder) {
                        $existOrder = Order::query()
                            ->where(CODE_1C, $order['order_code'])
                            ->first();
                    };

                    // __ Если нашли Заявку с таким номером в БД
                    if ($existOrder) {

                        // __ Записываем id Заявки
                        $order[self::VALIDATE_FIELD][self::ORDER_ID_FIELD] = $existOrder->id;

                        // __ Если нашли Заявку с таким номером в БД, но она прогнозная - перезаписываем
                        if (self::isOrderAverageType($existOrder, $elementsType)) {

                            $order[self::VALIDATE_FIELD][self::CHECK_FIELD]  = 'Прогнозная Заявка с таким номером уже есть в базе.';
                            $order[self::VALIDATE_FIELD][self::ACTION_FIELD] = self::ACTION_ORDER_UPDATE;
                            $order[self::VALIDATE_FIELD][self::ADVICE_FIELD] = 'Обновить прогнозную заявку данными из 1С.';

                        } else {

                            $order[self::VALIDATE_FIELD][self::CHECK_FIELD]  = 'Заявка с таким номером уже есть в базе.';
                            $order[self::VALIDATE_FIELD][self::ACTION_FIELD] = self::ACTION_ORDER_IGNORE;
                            $order[self::VALIDATE_FIELD][self::ADVICE_FIELD] = 'Для обновления Заявки, сначала удалите ее из базы.';

                        }
                    } else {  // __ Если не нашли Заявку с таким номером в БД

                        // __ Записываем id Заявки = 0
                        $order[self::VALIDATE_FIELD][self::ORDER_ID_FIELD] = 0;

                        if (self::isOrderMattressesType($elementsType)
                            || self::isOrderAccessoriesType($elementsType)) {

                            $order[self::VALIDATE_FIELD][self::CHECK_FIELD]  = 'Заявка с таким номером не найдена в базе.';
                            $order[self::VALIDATE_FIELD][self::ACTION_FIELD] = self::ACTION_ORDER_ADD;
                            $order[self::VALIDATE_FIELD][self::ADVICE_FIELD] = 'Добавить заявку в базу.';

                        } else {

                            $order[self::VALIDATE_FIELD][self::CHECK_FIELD]  = 'Заявка с таким номером не найдена в базе и Тип изделий в заявке не определен.';
                            $order[self::VALIDATE_FIELD][self::ACTION_FIELD] = self::ACTION_ORDER_IGNORE;
                            $order[self::VALIDATE_FIELD][self::ADVICE_FIELD] = 'Тип изделий не определен. Возможно требуется обновление моделей из 1С. Заявка не рекомендуется к добавлению в базу.';

                        }

                    }

                }

            }

            // __ Проходим по всем позициям в Заявке
            $isAllModelsExist = true;
            foreach ($order['items'] as &$orderLine) {
                $model = ModelsService::getModelByCode1C($orderLine['c']);
                if (!$model) {
                    $orderLine[self::VALIDATE_FIELD][self::CHECK_FIELD]  = 'Модель не найдена в базе.';
                    $orderLine[self::VALIDATE_FIELD][self::ACTION_FIELD] = self::ACTION_MODEL_IGNORE;
                    $orderLine[self::VALIDATE_FIELD][self::ADVICE_FIELD] = 'Добавить модель в базу через обновление Моделей и загрузить Заявки заново. Код Модели из 1С = ' . $orderLine['c'];
                    $isAllModelsExist                                    = false;
                } else {
                    $orderLine[self::VALIDATE_FIELD][self::CHECK_FIELD]  = self::CHECK_PASS;
                    $orderLine[self::VALIDATE_FIELD][self::ACTION_FIELD] = self::ACTION_NONE;
                    $orderLine[self::VALIDATE_FIELD][self::ADVICE_FIELD] = '';
                }
            }

            // __ Если не все модели найдены
            if (!$isAllModelsExist && $order[self::VALIDATE_FIELD][self::ADVICE_FIELD] !== 'Пропустить Заявку. Возможно это не матрасы или аксессуары.') {
                $order[self::VALIDATE_FIELD][self::CHECK_FIELD]  = 'Есть неизвестные модели.';
                $order[self::VALIDATE_FIELD][self::ACTION_FIELD] = self::ACTION_ORDER_IGNORE;
                $order[self::VALIDATE_FIELD][self::ADVICE_FIELD] = 'Добавить модель в базу через обновление Моделей и загрузить Заявки заново.';
            }

        }

        // __ Ищем дубликаты
        $acc = [];
        foreach ($orders as $index => $dubOrder) {

            if ($dubOrder['client_id'] === 0) continue;

            $elementsType = self::getOrderElementsTypeFromFront($dubOrder);
            // $elementsTypeRef = self::getOrderElementsTypeReference($elementsType);

            // __ Создаем уникальный ключ-строку из трех свойств
            $key = $dubOrder['client_id'] . '_' . $dubOrder['order_no'] . '_' . $elementsType;

            // __ Сохраняем не только объект, но и его положение в исходном массиве
            $acc[$key]['count']     = ($acc[$key]['count'] ?? 0) + 1;
            $acc[$key]['indices'][] = $index;
        }

        // __ Проходим по массиву дубликатов
        foreach ($acc as $item) {
            if ($item['count'] > 1) {
                foreach ($item['indices'] as $index) {
                    $orders[$index][self::VALIDATE_FIELD][self::CHECK_FIELD]  = 'Дубликат Заявки.';
                    $orders[$index][self::VALIDATE_FIELD][self::ACTION_FIELD] = self::ACTION_ORDER_IGNORE;
                    $orders[$index][self::VALIDATE_FIELD][self::ADVICE_FIELD] = 'Пропустить Заявку. Нужно проверить нумерацию Заявок в 1С.';
                }
            }
        }

        return $orders;
    }

    /**
     * ___ Получаем Заявку по Клиенту, Номеру Заявки, Типу изделий и Дате загрузки (Период заявки)
     * @param int $clientId ID Клиента
     * @param string $orderNo Номер Заявки
     * @param string $elementsType Тип изделий (матрасы, аксессуары, расчетное)
     * @param string $loadAt Дата загрузки (Период заявки)
     * @return Order|null
     */
    public static function getOrderByClientIdOrderNoElementsTypeLoadAt(
        int           $clientId,
        string        $orderNo,
        string        $elementsType,
        string|Carbon $loadAt
    ): ?Order
    {
        // __ Пробуем найти Заявку в БД, причем с учетом периода, если не нашли - пробуем соседние периоды
        // __ Вероятность, что Заявка у такого клиента с таким номером попадет другой период (+- год) практически нулевая
        // __ Перебираем периоды, чтобы наверняка исключить косяки с датами из 1С
        $orderPeriod = self::getOrderPeriod($loadAt);

        for ($i = 0; $i < 3; $i++) {

            $queryPeriod = match ($i) {
                0 => $orderPeriod,
                1 => $orderPeriod->addMonth(),
                2 => $orderPeriod->subMonth(),
            };

            $existOrder = Order::query()
                ->where('client_id', $clientId)
                ->where('order_no_str', $orderNo)
                ->where('elements_type_ref', $elementsType)
                ->with('lines')
                ->whereDate('order_period', $queryPeriod)
                ->first();

            if ($existOrder) return $existOrder;
        }

        return null;
    }


    /**
     * ___ Получаем тип Заявки (матрасы, аксессуары, смешанное или непонятно что)
     * @param array $order Заявка, которая приходит из браузера (upload)
     * @param array $data Ассоциативный массив с результатом (Ключ - тип изделий, Значение - количество)
     * @param array $dataPercent Ассоциативный массив с результатом в %(Ключ - тип изделий, Значение - количество в % от общего числа)
     * @return string
     */
    public static function getOrderElementsTypeFromFront(array $order, array &$data = [], array &$dataPercent = []): string
    {

        $result = [];
        $totals = 0;
        foreach ($order['items'] as $element) {

            $orderElementGroup = ModelsService::getElementTypeGroup($element['c'], $element['n']);  // code_1c, name

            if (!isset($result[$orderElementGroup])) {
                $result[$orderElementGroup] = $element['a'];
            } else {
                $result[$orderElementGroup] += $element['a'];
            }

            $totals += $element['a'];
        }

        // __ Страховка
        if (count($result) === 0) {
            return ElementTypes::UNDEFINED->value;
        }

        $resultString = '';
        foreach (ElementTypes::cases() as $type) {
            if (isset($result[$type->value])) {
                $resultString .= $type->value . '+';
            };
            // echo "Ключ: {$type->name}, Значение: {$type->value}" . PHP_EOL;
        }

        $data        = $result;
        $dataPercent = array_map(function ($item) use ($totals) {
            return $totals > 0 ? ($item / $totals * 100) : 0;
        }, $result);

        return rtrim($resultString, '+');


        // // __ Если найден один тип
        // if (count($result) === 1) {
        //
        //     foreach (ElementTypes::cases() as $type) {
        //         if (isset($result[$type->value])) {
        //             return $type->value;
        //         };
        //         // echo "Ключ: {$type->name}, Значение: {$type->value}" . PHP_EOL;
        //     }
        //
        // }

        // // __ Если найдено два типа, но в них только чехлы и матрасы или неопределенные, возвращаем тип - Матрасы
        // if (count($result) === 1) {
        //     if (
        //         isset($result[ElementTypes::MATTRESSES->value])
        //         // && isset($result[ElementTypes::COVERS->value])
        //     ) {
        //         return ElementTypes::MATTRESSES->value;
        //     }
        // } else if (
        //     isset($result[ElementTypes::ACCESSORIES->value])
        // ) {
        //     return ElementTypes::ACCESSORIES->value;
        // }
        //
        // return ElementTypes::MIXED->value;
    }

    /**
     * ___ Возвращает референсный тип элементов, для идентификации заявок в базе
     * @param string $orderType
     * @return string
     */
    public static function getOrderElementsTypeReference(string $orderType): string
    {
        return match (true) {
            self::isOrderMattressesType($orderType) => ElementTypes::MATTRESSES->value,
            self::isOrderAccessoriesType($orderType) => ElementTypes::ACCESSORIES->value,
            default => ElementTypes::UNDEFINED->value,
        };
    }


    /**
     * ___ Проверяем тип Заявки на матрасную группу
     * ___ Если есть и Матрасы и Чехлы и Не определено - будет Матрасы
     * @param string $orderType
     * @return bool
     */
    public static function isOrderMattressesType(string $orderType): bool
    {
        return mb_stripos($orderType, ElementTypes::MATTRESSES->value) !== false;
    }

    /**
     * ___ Проверяем тип Заявки на аксессуарную группу
     * ___ Если есть и Аксессуары и Чехлы и Не определено - будет Аксессуары
     * @param string $orderType
     * @return bool
     */
    public static function isOrderAccessoriesType(string $orderType): bool
    {
        return mb_stripos($orderType, ElementTypes::ACCESSORIES->value) !== false;
    }

    /**
     * ___ Проверяем тип Заявки на то, что тип элементов - Не определен
     * ___ Строгое соответствие
     * @param string $orderType
     * @return bool
     */
    public static function isOrderUndefinedType(string $orderType): bool
    {
        return mb_strtolower($orderType) === mb_strtolower(ElementTypes::UNDEFINED->value);
    }

    /**
     * ___ Проверяем тип Заявки на чехольную группу
     * ___ Строгое соответствие
     * @param string $orderType
     * @return bool
     */
    public static function isOrderCoversType(string $orderType): bool
    {
        return mb_strtolower($orderType) === mb_strtolower(ElementTypes::COVERS->value);
        // return mb_stripos($orderType, ElementTypes::COVERS->value) !== false;
    }


    /**
     * ___ Проверяем тип Заявки на Прогнозную группу
     * ___ Проверяем состав заявки или тип изделий
     * @param Order|null $order
     * @param string|null $orderType
     * @return bool
     */
    public static function isOrderAverageType(Order $order = null, string $orderType = null): bool
    {
        if (is_null($order) && is_null($orderType)) {
            return false;
        }

        if (!is_null($order)) {
            foreach ($order->lines as $line) {
                if (ModelsService::isElementAverage($line->model_code_1c, $line->model_name)) {
                    return true;
                }
            }
        }

        return mb_stripos($orderType, ElementTypes::AVERAGE->value) !== false;
        // return mb_strtolower($orderType) === mb_strtolower(ElementTypes::COVERS->value);
    }

    /**
     * ___ Форматируем входящий JSON с заявками в массив
     * @param array|string $orders
     * @return array
     */
    public static function prepareOrderData(array|string $orders): array
    {
        if (is_array($orders)) {
            return $orders;
        }
        try {
            return json_decode($orders, true);
        } catch (Exception $e) {
            Log::channel(ORDERS)->error('Ошибка формата входящего JSON с заявками', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString()
            ]);
            return [];
        }
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

        if (in_array($parts[1], ['1', '2'])) {
            if (ClientsService::isClient_LMM($clientID)) {
                return self::getOrderTypeByIndex('.' . $parts[1]);     // Дополнительная заявка ЛММ
            }
        }

        if (in_array($parts[1], ['1', '2', '3', '4', '5', '6', '7', '8', '9'])) {
            return self::getOrderTypeByIndex('.1-10; .20-...');     // Дополнительная заявка
        }

        if (ClientsService::isClient_LMM($clientID) && mb_strlen($parts[1]) === 3) {    // Приводим заявки ЛММ типа .219 к типу .19
            $parts[1] = mb_substr($parts[1], 1);
        }

        if (isset(self::$orderTypesCache['.' . $parts[1]])) {
            return self::getOrderTypeByIndex('.' . $parts[1]);
        }

        return self::getOrderTypeByIndex('.00');
    }


    /**
     * ___ Возвращает Период Заявки по дате загрузки
     * @param string|Carbon|Order $entity
     * @return Carbon
     */
    public static function getOrderPeriod(string|Carbon|Order $entity): Carbon
    {
        $period = self::normalizeToCarbon($entity);
        return $period->copy()->startOfMonth();
    }

    /**
     * ___ Возвращает дату в виде Carbon
     * @noinspection PhpUndefinedFieldInspection
     * @param string|Carbon|Order $entity
     * @return Carbon
     */
    public static function normalizeToCarbon(string|Carbon|Order $entity): Carbon
    {
        return match (true) {
            is_string($entity) => (function () use ($entity) {
                try {
                    return Carbon::parse($entity);
                } catch (\Exception $e) {
                    return Carbon::now();
                }
            })(),
            $entity instanceof Carbon => $entity,
            $entity instanceof Order => Carbon::parse($entity->load_at),
            default => Carbon::now(),
            // default => throw new \InvalidArgumentException(
            //     'Ожидается строка, Carbon или PlanLoad, получен: ' . get_debug_type($entity)
            // )
        };
    }


    /**
     * ___ Возвращает тип изделий в русской нотации
     * @param string $value
     * @return string
     */
    public static function getElementsTypeRender(string $value): string
    {
        $search  = [
            ElementTypes::UNDEFINED->value,
            ElementTypes::MATTRESSES->value,
            ElementTypes::COVERS->value,
            ElementTypes::ACCESSORIES->value,
            ElementTypes::MIXED->value,
            ElementTypes::AVERAGE->value,
        ];
        $replace = [
            'не известно',
            'матрасы',
            'чехлы',
            'аксессуары',
            'смешанная',
            'прогнозная',
        ];
        return str_replace($search, $replace, $value);
    }

    /**
     * ___ Возвращает тип изделий из русской нотации в референсный
     * @param string $value
     * @return string
     */
    public static function getElementsTypeFromRender(string $value): string
    {
        if (mb_stripos($value, 'матрас') !== false) {
            return ElementTypes::MATTRESSES->value;
        } elseif (mb_stripos($value, 'аксессуар') !== false) {
            return ElementTypes::ACCESSORIES->value;
        } elseif (mb_stripos($value, 'чехлы') !== false || mb_stripos($value, 'чехол') !== false) {
            return ElementTypes::COVERS->value;
        } elseif (mb_stripos($value, 'прогноз') !== false || mb_stripos($value, 'план') !== false) {
            return ElementTypes::AVERAGE->value;
        }

        return ElementTypes::UNDEFINED->value;
    }


    /**
     *  __ Добавляем прогнозную заявку
     * @param int $clientId
     * @param string $orderNo
     * @param string|ElementTypes $elementType
     * @param string|Carbon $loadAt
     * @param int $amount
     * @param string|Carbon|null $unloadAt
     * @return void
     * @throws Throwable
     */
    public static function addAverageOrder(
        int                 $clientId,
        string              $orderNo,
        string|ElementTypes $elementType,
        string|Carbon       $loadAt,
        int                 $amount,
        string|Carbon|null  $unloadAt = null,
    ): void
    {
        // __ Находим Клиента
        $client = null;
        if ($clientId !== 0) {
            $client = Client::query()->findOrFail($clientId);
        }
        if (!$client) {
            throw new Exception('Client with id = ' . $clientId . ' not found');
        }

        // __ Проверяем тип на соответствие
        if (is_string($elementType)) {
            $enumValue = ElementTypes::tryFrom($elementType);

            // __ Если не нашли по оригиналу, получаем Тип изделий из Типа рендера: "матрасы" или "аксессуары"
            if (!$enumValue) {
                $enumValue = ElementTypes::tryFrom(OrdersService::getElementsTypeFromRender($elementType));
            }

            if (!$enumValue || !$enumValue->isProduct()) {
                throw new Exception('Element type not found or wrong');
            }
            $elementType = $enumValue; // Теперь здесь гарантированно нужный Enum
        }

        // __ Нормализуем дату загрузки
        $loadAt = normalizeToCarbon($loadAt);

        // __ Пробуем найти Заявку в БД, причем с учетом периода, если не нашли - пробуем соседние периоды
        // __ Вероятность, что Заявка у такого клиента с таким номером попадет другой период (+- год) практически нулевая
        // __ Перебираем периоды, чтобы наверняка исключить косяки с датами из 1С
        $existOrder = OrdersService::getOrderByClientIdOrderNoElementsTypeLoadAt(
            $clientId,
            $orderNo,
            $elementType->value,
            $loadAt,
        );

        // __ Обрабатываем ошибку существующего заказа
        if ($existOrder) {
            throw new Exception('Заказ с таким номером уже существует');
        }

        // __ Проверяем наличие средней модели
        $averageModel = ModelsService::createAverageModel($client->id, $elementType->value);
        if (!$averageModel) {
            throw new Exception('Error while creating average model with Client id = ' . $clientId);

        }

        // __ Получаем тип заявки по номеру (гар. рем, серийная и т.д.)
        // __  Устанавливаем Тип - Прогнозная Заявка
        $orderType = OrdersService::getOrderTypeByIndex(AVERAGE_TYPE_INDEX);
        // $orderType = OrdersService::getOrderTypeByOrderNoAndClientId($planLoad['order_no'], $client->id);

        DB::transaction(function () use ($averageModel, $orderType, $loadAt, $unloadAt, $amount, $elementType, $orderNo, $client) {

            $createdOrder = PlanLoad::query()->create([
                'client_id'         => $client->id,
                'order_type_id'     => $orderType->id,
                // 'plan_load_id'      => null, // TODO: Повесить Observer и создать плановую загрузку, пока не будем управлять сущностью
                'order_no_num'      => parseNumericValue($orderNo),
                'order_no_str'      => $orderNo,
                'order_no_origin'   => $orderNo,
                'no_1c'             => '',   // Используем на фронте
                'is_forecast'       => true, // Прогнозная Заявка
                'order_period'      => PlanService::getOrderPeriod($loadAt),
                'elements_type'     => $elementType->value,
                'elements_type_ref' => OrdersService::getOrderElementsTypeReference($elementType->value),

                // __ Вставляем именно массивом, без преобразования в json
                'amounts'           => [
                    'averages'   => $amount,
                    'mattresses' => 0,
                    'up_covers'  => 0,
                    'covers'     => 0,
                    'children'   => 0,
                    'formats'    => 0,
                    'unknowns'   => 0,
                    'totals'     => $amount,
                ],

                'load_at'   => $loadAt,
                'unload_at' => empty($unloadAt) ? null : normalizeToCarbon($unloadAt),
                // 'unload_at' => null,

                // 'responsible'        => null,
                // 'manager_load_date'  => null,
                // 'manager_start'      => null,
                // 'manager_end'        => null,
                // 'design_start'       => null,
                // 'design_end'         => null,
                // 'no_1c'              => null,
                // 'code_1c'            => null,
                // 'base_order_code_1c' => null,
                // 'comment_1c'         => null,
                // 'client_code_1c'     => null,
                // 'client_name_1c'     => null,
                // 'service'            => null,

                // 'status'             => $order[''],
                // 'shown'              => $order[''],
                // 'stat_include'       => $order[''],
                // 'service_ext'        => $order[''],
                // 'extended_meta'      => $order[''],
                // 'description'        => $order[''],
                // 'comment'            => $order[''],
                // 'note'               => $order[''],
                // 'meta'               => $order[''],
                // 'history'            => $order[''],
                // 'meta_ext'           => $order[''],
                // 'active'             => $order[''],
            ]);

            if (!$createdOrder) {
                throw new Exception('Error while creating Average Order with Client id = ' . $client->id . ' is failed');
            }

            // __ Вставляем содержимое Прогнозной Заявки
            // __ Получаем размеры
            $AVERAGE_SIZE_STR = '0x0x0';
            $dims             = SizeService::getDimensions($AVERAGE_SIZE_STR);

            $createLine = OrderLine::query()->create(
                [
                    'order_id'      => $createdOrder->id,
                    'size'          => $AVERAGE_SIZE_STR,
                    'width'         => $dims->getWidth(),
                    'length'        => $dims->getLength(),
                    'height'        => $dims->getHeight(),
                    'model_name'    => $averageModel->name,
                    'model_code_1c' => $averageModel->code_1c,
                    'amount'        => $amount,
                    // 'textile'       => $orderLine['t'],
                    // 'composition'   => $orderLine['d'],
                    // 'describe_1'    => $orderLine['d1'],
                    // 'describe_2'    => $orderLine['d2'],
                    // 'describe_3'    => $orderLine['d3'],
                    // 'active'        => $orderLine[''],
                    // 'status'        => $orderLine[''],
                    // 'description'   => $orderLine[''],
                    // 'comment'       => $orderLine[''],
                    // 'note'          => $orderLine[''],
                    // 'meta'          => $orderLine[''],
                ]
            );

            if (!$createLine) {
                throw new Exception('Error while creating Average Order Line with Client id = ' . $client->id . ' is failed');
            }


            // __ Устанавливаем статус Заявки - Создано
            $createdOrder->statuses()->attach([
                OrderStatus::ORDER_STATUS_CREATED_ID => [
                    'set_at'     => now(),
                    'created_by' => auth()->id(),
                ]
            ]);


            // __ Тут начинаем формировать СЗ на различные участки !!! Точка рождения СЗ

            // __ Если тип элементов в Заявке - не матрасы, то пропускаем
            // __ Создаем СЗ на Участки только для матрасов
            $typeRef = $createdOrder->elements_type_ref;
            if ($createdOrder->elements_type_ref === ElementTypes::MATTRESSES->value) {

                // __ Создаем СЗ на Пошив
                $sewingTask = SewingService::createSewingTaskFromOrderId($createdOrder->id);
                if (!$sewingTask) {
                    throw new Exception('Error while creating Sewing Task with Client id = ' . $client->id);
                };

            }
        });


    }


}





// "client_id":63,
// "client_full_name":"RAY",
// "client_add_name":"",
// "client_code":"004033",
// "order_code":"0000045235",
// "order_no":"5",
// "order_no_1c":"Заявка ИП №5",
// "manuf_at_1c":"19.12.2025",
// "load_at":"21.12.2025",
// "load_at_1c":"21.12.2025",
// "unload_at":"",
// "kb_start":"16.12.2025 11:22:46",
// "kb_end":"16.12.2025 11:30:53",
// "mg_start":"",
// "mg_end":"",
// "responsible":"Ольга Кептюха",
// "comment":"Заявка ИП №5",
// "base":"",
// "service":"16.12.2025 11:22:46#16.12.2025 11:30:53#19.12.2025#21.12.2025#Ольга Кептюха#Заявка ИП №5##",
// "items":
// [
// {
//     "n":"S1 (H2)",
// "с":"000003904",
// "s":"90x200x21",
// "t":"дж сток ИП",
// "a":3,
// "d":"Минюк В.И.",
// "d1":"",
// "d2":"",
// "d3":""
// },
