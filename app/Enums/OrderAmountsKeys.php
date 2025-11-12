<?php

namespace App\Enums;

// ___ Константы количества изделий в заявке
enum OrderAmountsKeys: string
{
    case MATTRESSES = "mattresses";
    case UP_COVERS = "up_covers";
    case AVERAGES = "averages";
    case COVERS = "covers";
    case CHILDREN = "children";
    case FORMATS = "formats";
    case UNKNOWNS = "unknowns";
    case TOTALS = "totals";

}

