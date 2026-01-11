<?php

namespace App\Models\Order;

use Illuminate\Database\Eloquent\Model;

class OrderStatus extends Model
{
    public const ORDER_STATUS_CREATED_ID = 1;   //Создание плановой заявки
    public const ORDER_STATUS_LOADED_FROM_1C_ID = 2;   //Загружена из 1С
    public const ORDER_STATUS_3 = 3;   //Раскрой: СЗ создано
    public const ORDER_STATUS_4 = 4;   //Раскрой: СЗ готово к выполнению
    public const ORDER_STATUS_5 = 5;   //Раскрой: СЗ в процессе
    public const ORDER_STATUS_6 = 6;   //Раскрой: СЗ выполнено
    public const ORDER_STATUS_7 = 7;   //Пошив: СЗ создано
    public const ORDER_STATUS_8 = 8;   //Пошив: СЗ готово к выполнению
    public const ORDER_STATUS_9 = 9;   //Пошив: СЗ в процессе
    public const ORDER_STATUS_10 = 10; //Пошив: СЗ выполнено
    public const ORDER_STATUS_11 = 11; //Сборка: СЗ создано
    public const ORDER_STATUS_12 = 12; //Сборка: СЗ готово к выполнению
    public const ORDER_STATUS_13 = 13; //Сборка: СЗ в процессе
    public const ORDER_STATUS_14 = 14; //Сборка: СЗ выполнено
    public const ORDER_STATUS_MOVED_TO_WAREHOUSE_ID = 15; //Передана на склад
    public const ORDER_STATUS_UNLOADED_ID = 16; //Отгружена

}
