<?php

namespace App\Enums;

// ___ Используется для идентификации типа заявки
enum ElementTypes: string
{
    case UNDEFINED = 'undefined';           // Заявка не понятно из каких элементов
    case MATTRESSES = 'mattresses';         // Заявка из матрасов
    case ACCESSORIES = 'accessories';       // Заявка из аксессуаров
    case MIXED = 'mixed';                   // Заявка из матрасов и аксессуаров
    case AVERAGE = 'average';               // Заявка из прогнозных данных
    case COVERS = 'covers';                 // Заявка из чехлов


}

