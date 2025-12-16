<?php

namespace App\Enums;

enum MaterialUnits: string
{
    case PIC = 'шт.';
    case KG = 'кг';
    case METERS = 'м.';
    case SQUARE_METERS = 'м2';   // 'м²'
    case CUBIC_METERS = 'м3';   // 'м³'
    case RUNNING_METERS = 'м.п.';
    case UNDEFINED = 'н/д';

}

