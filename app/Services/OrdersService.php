<?php

namespace App\Services;


use App\Enums\ElementTypes;
use App\Models\Client;
use App\Models\Order\Order;
use App\Models\Order\OrderType;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;

final class OrdersService
{
    private static array $orderTypesCache = [];     // Типы заявок


    // ___ Проверяем Заявки перед загрузкой в БД на вшивость
    public static function validateOrders(array &$orders): array
    {
        // TODO!!!!! Проверка Дубликатов!!!

        // __ Поля, которые получаем из базы, если заявка существует
        $ORDER_METADATA_FIELD = 'metadata';
        $CLIENT_SHORT_NAME_FIELD = 'client_short_name';
        $ORDER_NO_FIELD = 'order_no';
        $ORDER_PERIOD_FIELD = 'order_period';


        $VALIDATE_FIELD = 'validate';
        $CHECK_FIELD = 'check';
        $CHECK_PASS = 'ok';
        $CHECK_FAIL = 'fail';

        $ADVICE_FIELD = 'advice';

        $ORDER_ELEMENTS_TYPE_FIELD = 'elements_type';

        $ACTION_FIELD = 'action';
        $ACTION_NONE = 'none';
        $ACTION_CLIENT_IGNORE = 'Игнорировать Клиента';
        $ACTION_CLIENT_ADD = 'Создать Клиента';
        $ACTION_ORDER_UPDATE = 'Обновить Заявку';
        $ACTION_ORDER_IGNORE = 'Игнорировать Заявку';
        $ACTION_ORDER_ADD = 'Создать Заявку';
        $ACTION_MODEL_ADD = 'Создать Модель';
        $ACTION_MODEL_IGNORE = 'Игнорировать Модель';

        // __ Подготавливаем данные (если строка json, преобразуем в массив)
        $orders = self::prepareOrderData($orders);


        // __ Проходим по всем Заявкам
        foreach ($orders as &$order) {

            // __ Получаем Тип изделий в Заявках
            $elementsType = self::getOrderElementsTypeFromFront($order);
            $elementsTypeRef = self::getOrderElementsTypeReference($elementsType);
            $order[$ORDER_ELEMENTS_TYPE_FIELD] = self::getElementsTypeRender($elementsTypeRef);

            // __ Пробуем получить клиента по ID
            $client = null;
            if ($order['client_id'] === 0) { // Приходит с фронта сразу не определенный ID, пробуем его найти по code_1c
                $client = Client::query()->where(CODE_1C, $order['client_code'])->first();
            } else {
                $client = Client::query()->find($order['client_id']);
            }

            if (!$client) { // Если не нашли клиента по ID или code_1c
                // Действие зависит от того, какого типа заявка
                if (self::isOrderUndefinedType($elementsTypeRef)) {
                    if (self::isOrderCoversType($elementsType)) {  // Если референсный тип Не определен, но есть Чехлы - предлагаем добавить клиента
                        $order[$VALIDATE_FIELD][$CHECK_FIELD] = 'Клиент отсутствует в базе, но Тип изделий в заявке - Чехлы.';
                        $order[$VALIDATE_FIELD][$ACTION_FIELD] = $ACTION_CLIENT_ADD;
                        $order[$VALIDATE_FIELD][$ADVICE_FIELD] = 'Добавить клиента здесь или через Справочник Клиентов и заново загрузить Заявки.';
                    } else {
                        $order[$VALIDATE_FIELD][$CHECK_FIELD] = 'Клиент отсутствует в базе и Тип изделий в заявке не определен.';
                        $order[$VALIDATE_FIELD][$ACTION_FIELD] = $ACTION_CLIENT_IGNORE;
                        $order[$VALIDATE_FIELD][$ADVICE_FIELD] = 'Пропустить Заявку. Возможно это не матрасы или аксессуары.';
                    }

                } else {
                    $order[$VALIDATE_FIELD][$CHECK_FIELD] = 'Клиент отсутствует в базе.';
                    $order[$VALIDATE_FIELD][$ACTION_FIELD] = $ACTION_CLIENT_ADD;
                    $order[$VALIDATE_FIELD][$ADVICE_FIELD] = 'Добавить клиента здесь или через Справочник Клиентов и заново загрузить Заявки.';
                }

            } else { // Если нашли клиента

                $order['client_full_name'] = $client->short_name;   // Перезаписываем имя клиента из БД
                $order['client_id'] = $client->id;                  // Перезаписываем id клиента из БД, если нашли по коду из 1С

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

                if ($existOrder) {  // __ Если нашли Заявку с таким номером в БД
                    // __ Если нашли Заявку с таким номером в БД, но она прогнозная - перезаписываем
                    if (self::isOrderAverageType($existOrder, $elementsType)) {

                        $order[$VALIDATE_FIELD][$CHECK_FIELD] = 'Прогнозная Заявка с таким номером уже есть в базе.';
                        $order[$VALIDATE_FIELD][$ACTION_FIELD] = $ACTION_ORDER_UPDATE;
                        $order[$VALIDATE_FIELD][$ADVICE_FIELD] = 'Обновить прогнозную заявку данными из 1С.';

                    } else {

                        $order[$VALIDATE_FIELD][$CHECK_FIELD] = 'Заявка с таким номером уже есть в базе.';
                        $order[$VALIDATE_FIELD][$ACTION_FIELD] = $ACTION_ORDER_IGNORE;
                        $order[$VALIDATE_FIELD][$ADVICE_FIELD] = 'Для обновления Заявки, сначала удалите ее из базы.';

                    }
                } else {  // __ Если не нашли Заявку с таким номером в БД

                    if (self::isOrderMattressesType($elementsType)
                        || self::isOrderAccessoriesType($elementsType)) {

                        $order[$VALIDATE_FIELD][$CHECK_FIELD] = 'Заявка с таким номером не найдена в базе.';
                        $order[$VALIDATE_FIELD][$ACTION_FIELD] = $ACTION_ORDER_ADD;
                        $order[$VALIDATE_FIELD][$ADVICE_FIELD] = 'Добавить заявку в базу.';

                    } else {

                        $order[$VALIDATE_FIELD][$CHECK_FIELD] = 'Заявка с таким номером не найдена в базе и Тип изделий в заявке не определен.';
                        $order[$VALIDATE_FIELD][$ACTION_FIELD] = $ACTION_ORDER_IGNORE;
                        $order[$VALIDATE_FIELD][$ADVICE_FIELD] = 'Тип изделий не определен. Возможно требуется обновление моделей из 1С. Заявка не рекомендуется к добавлению в базу.';

                    }

                }


            }

            // __ Проходим по всем позициям в Заявке
            foreach ($order['items'] as &$orderLine) {
                $model = ModelsService::getModelByCode1C($orderLine['c']);
                if (!$model) {
                    $orderLine[$VALIDATE_FIELD][$CHECK_FIELD] = 'Модель не найдена в базе.';
                    $orderLine[$VALIDATE_FIELD][$ACTION_FIELD] = $ACTION_MODEL_IGNORE;
                    $orderLine[$VALIDATE_FIELD][$ADVICE_FIELD] = 'Добавить модель в базу через обновление Моделей и загрузить Заявки заново. Код Модели из 1С = ' . $orderLine['c'];
                } else {
                    $orderLine[$VALIDATE_FIELD][$CHECK_FIELD] = $CHECK_PASS;
                    $orderLine[$VALIDATE_FIELD][$ACTION_FIELD] = $ACTION_NONE;
                    $orderLine[$VALIDATE_FIELD][$ADVICE_FIELD] = '';
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
        int    $clientId,
        string $orderNo,
        string $elementsType,
        string $loadAt): ?Order
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
     * @param array|string $order
     * @return string
     */
    public static function getOrderElementsTypeFromFront(array|string $order): string
    {

        $result = [];

        foreach ($order['items'] as $element) {

            $orderElementGroup = ModelsService::getElementTypeGroup($element['c'], $element['n']);  // code_1c, name

            if (!isset($result[$orderElementGroup])) {
                $result[$orderElementGroup] = $element['a'];
            } else {
                $result[$orderElementGroup] += $element['a'];
            }

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
            self::isOrderMattressesType($orderType)  => ElementTypes::MATTRESSES->value,
            self::isOrderAccessoriesType($orderType) => ElementTypes::ACCESSORIES->value,
            default                                  => ElementTypes::UNDEFINED->value,
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
            is_string($entity)        => (function () use ($entity) {
                try {
                    return Carbon::parse($entity);
                } catch (\Exception $e) {
                    return Carbon::now();
                }
            })(),
            $entity instanceof Carbon => $entity,
            $entity instanceof Order  => Carbon::parse($entity->load_at),
            default                   => Carbon::now(),
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
        $search = [
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
