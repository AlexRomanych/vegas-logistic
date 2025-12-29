<?php

namespace App\Services\Plan;


use App\Models\Client;
use App\Models\Plan\PlanLoad;
use App\Services\OrdersService;

final class PlanLoadsService
{

    /**
     * ___ Валидируем План Загрузок, которые загрузили с диска из отчета
     * @param array $planLoads
     * @return array
     */
    public static function validatePlanLoads(array &$planLoads): array
    {
        // TODO!!!!! Проверка Дубликатов!!!

        $ORDER_STATUS_FIELD = 'order_status';
        $ORDER_READY_STRING = 'раскрытая';
        $ORDER_AVERAGE_STRING = 'прогнозная';

        // __ Подготавливаем данные (если строка json, преобразуем в массив)
        $planLoads = OrdersService::prepareOrderData($planLoads);

        // __ Проходим по всем Заявкам
        foreach ($planLoads as &$planLoad) {

            // __ Получаем Тип изделий в Заявках
            $elementsType = $planLoad['elements_type'];
            $planLoad['elements_type'] = OrdersService::getElementsTypeRender($elementsType);

            // __ Пробуем получить клиента по ID
            $client = null;
            if ($planLoad['client_id'] !== 0) {
                $client = Client::query()->find($planLoad['client_id']);
            }

            if (!$client) { // Если не нашли клиента по ID
                $planLoad[VALIDATE_FIELD][CHECK_FIELD] = 'Клиент отсутствует в базе.';
                $planLoad[VALIDATE_FIELD][ACTION_FIELD] = ACTION_NONE;
                $planLoad[VALIDATE_FIELD][ADVICE_FIELD] = 'Добавить клиента через Справочник Клиентов и в ЕПС и заново загрузить Заявки.';

                continue;
            }

            $planLoad[CLIENT_SHORT_NAME_FIELD] = $client->short_name;  // Записываем имя клиента из БД

            // __ Пробуем найти Заявку в БД, причем с учетом периода, если не нашли - пробуем соседние периоды
            // __ Вероятность, что Заявка у такого клиента с таким номером попадет другой период (+- год) практически нулевая
            // __ Перебираем периоды, чтобы наверняка исключить косяки с датами из 1С
            $existOrder = OrdersService::getOrderByClientIdOrderNoElementsTypeLoadAt(
                $client->id,
                $planLoad['order_no'],
                $elementsType,
                $planLoad['load_at']
            );

            if ($existOrder) {  // __ Если нашли Заявку с таким номером в БД
                // __ Если нашли Заявку с таким номером в БД, но она прогнозная - оставляем как есть
                if (OrdersService::isOrderAverageType($existOrder, $elementsType)) {
                    $planLoad[VALIDATE_FIELD][CHECK_FIELD] = 'Прогнозная Заявка с таким номером уже есть в базе.';
                    $planLoad[$ORDER_STATUS_FIELD] = $ORDER_AVERAGE_STRING;
                } else {
                    $planLoad[VALIDATE_FIELD][CHECK_FIELD] = 'Заявка с таким номером уже есть в базе, причем раскрытая!';
                    $planLoad[$ORDER_STATUS_FIELD] = $ORDER_READY_STRING;
                }

                $planLoad[VALIDATE_FIELD][ACTION_FIELD] = ACTION_ORDER_IGNORE;
                $planLoad[VALIDATE_FIELD][ADVICE_FIELD] = 'Для обновления Заявки, сначала удалите ее из базы.';

            } else if ($planLoad['amounts']['averages'] !== 0) {  // __ Если не нашли Заявку с таким номером в БД, но она прогнозная - создаем новую

                $planLoad[VALIDATE_FIELD][CHECK_FIELD] = 'Заявка с таким номером не найдена в базе.';
                $planLoad[VALIDATE_FIELD][ACTION_FIELD] = ACTION_ORDER_ADD;
                $planLoad[VALIDATE_FIELD][ADVICE_FIELD] = 'Прогнозная заявка будет создана в базе.';
                $planLoad[$ORDER_STATUS_FIELD] = $ORDER_AVERAGE_STRING;

            } else if ($planLoad['amounts']['totals'] !== 0) {

                $planLoad[VALIDATE_FIELD][CHECK_FIELD] = 'Раскрытая Заявка с таким номером не найдена в базе.';
                $planLoad[VALIDATE_FIELD][ACTION_FIELD] = ACTION_ORDER_IGNORE;
                $planLoad[VALIDATE_FIELD][ADVICE_FIELD] = 'Заявка раскрыта, но не загружена в БД. Заявку нужно загрузить через Загрузку Заявок.';
                $planLoad[$ORDER_STATUS_FIELD] = $ORDER_READY_STRING;

            } else {

                $planLoad[VALIDATE_FIELD][CHECK_FIELD] = 'Заявка неизвестного типа и происхождения.';
                $planLoad[VALIDATE_FIELD][ACTION_FIELD] = ACTION_ORDER_IGNORE;
                $planLoad[VALIDATE_FIELD][ADVICE_FIELD] = 'Непонятные данные. Возможно это не Заявка или ошибка в файле.';
                $planLoad[$ORDER_STATUS_FIELD] = '';
            }
        }

        return $planLoads;
    }


}
