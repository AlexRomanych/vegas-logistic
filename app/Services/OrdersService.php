<?php

namespace App\Services;


use App\Enums\ElementTypes;
use App\Models\Order\OrderType;
use Exception;
use Illuminate\Support\Facades\Log;

final class OrdersService
{
    private static array $orderTypesCache = [];     // Типы заявок


    // ___ Проверяем Заявки перед загрузкой в БД на вшивость
    public static function validateOrders(array $orders)
    {
        $orders = self::prepareOrderData($orders);
        // $checkResult = checkClientsExist;

        // $validOrderResult = [
        //     'is_order_exist' => 'false',
        //     'is_client_valid' => 'false',
        // ];
        //

        $validOrderResult = [];


        foreach ($orders as $order) {


            $orderType = self::getOrderElementsTypeFromFront($order);

            $a = 0;

            // if (
            //     $order['client_id'] === 0 ||
            //     !ClientsService::getClientById($order['client_id']) ||
            //     !ClientsService::getClientByName($order['client_full_name'], $order['client_add_name']) ||
            //     !ClientsService::getClientByCode_1c($order['client_code'])
            // ) {
            //     $order['client_id_check'] = false;
            //     $order['client_id_action'] = 'add';
            // }


        }
    }


    private function checkClientsExist(string $orders)
    {

    }

    private function checkOrderExist()
    {
    }


    /**
     * ___ Получаем тип Заявки (матрасы, аксессуары, смешанное или непонятно что)
     * @param array|string $order
     * @return string
     */
    public static function getOrderElementsTypeFromFront(array|string $order): string
    {


        $result = [
            // ElementTypes::UNDEFINED->value => 0,
            // ElementTypes::MATTRESSES->value => 0,
            // ElementTypes::ACCESSORIES->value => 0,
            // ElementTypes::MIXED->value => 0,
        ];

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

        // __ Если найден один тип
        if (count($result) === 1) {

            foreach (ElementTypes::cases() as $type) {
                if (isset($result[$type->value])) {
                    return $type->value;
                };
                // echo "Ключ: {$type->name}, Значение: {$type->value}" . PHP_EOL;
            }

        }

        // __ Если найдено два типа, но в них только чехлы и матрасы или неопределенные, возвращаем тип - Матрасы
        if (count($result) === 1) {
            if (
                isset($result[ElementTypes::MATTRESSES->value])
                // && isset($result[ElementTypes::COVERS->value])
            ) {
                return ElementTypes::MATTRESSES->value;
            }
        } else if (
            isset($result[ElementTypes::ACCESSORIES->value])
        ) {
            return ElementTypes::ACCESSORIES->value;
        }

        return ElementTypes::MIXED->value;
    }


    /**
     * ___ Форматируем входящий JSON с заявками в массив
     * @param array|string $orders
     * @return array
     */
    private static function prepareOrderData(array|string $orders): array
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
