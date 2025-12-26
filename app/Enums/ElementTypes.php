<?php

namespace App\Enums;

// ___ Используется для идентификации типа заявки
enum ElementTypes: string
{
    // warning!!! Считаем, что MATTRESSES и ACCESSORIES - не могут быть вместе (не пересекаются)
    case MATTRESSES = 'mattresses';         // Заявка из матрасов
    case ACCESSORIES = 'accessories';       // Заявка из аксессуаров


    case AVERAGE = 'average';               // Заявка из прогнозных данных
    case COVERS = 'covers';                 // Заявка из чехлов
    case UNDEFINED = 'undefined';           // Заявка не понятно из каких элементов
    case MIXED = 'mixed';                   // Заявка из матрасов и аксессуаров

}

