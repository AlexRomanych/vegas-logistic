<?php


namespace App\Enums;

// ___ Константы Категорий причин Группы "Стежка"
enum ReasonCategoryGroupFabric: int
{
    case REASON_ROLL_FALSE = 1;      // Статус рулона "Не выполнено"
    case REASON_ROLL_ROLLING = 2;   // Статус рулона "Переходящий"
    case REASON_ROLL_CANCELLED = 3;   // Статус рулона "Отменено"
    case REASON_ROLL_ADDING = 4;   // Статус рулона "Добавлен во время выполнения"
    case REASON_ROLL_PAUSED = 5;   // Статус рулона "Приостановка выполнения"

}


