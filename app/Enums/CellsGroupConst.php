<?php

namespace App\Enums;

// ___ Константы Групп ПЯ (Это id таблицы "cells_groups")
enum CellsGroupConst: int
{
    case COMMON_GROUP = 0;      // 'name' => 'Общая'
    case FABRIC_GROUP = 1;      //'name' => 'Стежка'
    case CUTTING_GROUP = 2;     // 'name' => 'Раскрой'
    case SEWING_GROUP = 3;      // 'name' => 'Швейный цех'
    case ASSEMBLY_GROUP = 4;    // 'name' => 'Сборка'
    case PACK_GROUP = 5;        // 'name' => 'Упаковка'
    case BLOCK_GROUP = 6;       //  'name' => 'Участок ПБ'

}
