<?php

namespace App\Services;


final class OrdersService
{

    // ___ Проверяем Заявки перед загрузкой в БД на вшивость
    public static function validateOrders(array $orders)
    {

        // $checkResult = checkClientsExist;

        // $validOrderResult = [
        //     'is_order_exist' => 'false',
        //     'is_client_valid' => 'false',
        // ];
        //

        $validOrderResult = [];


        foreach ($orders as $order) {
            if (
                $order['client_id'] === 0 ||
                !ClientsService::getClientById($order['client_id']) ||
                !ClientsService::getClientByName($order['client_full_name'], $order['client_add_name']) ||
                !ClientsService::getClientByCode_1c($order['client_code'])
            ) {
                $order['client_id_check'] = false;
                $order['client_id_action'] = 'add';
            }



        }
    }


    private function checkClientsExist(string $orders)
    {

    }

    private function checkOrderExist()
    {
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
