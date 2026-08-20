<?php

namespace App\Classes;


class Settings
{

    /**
     * ___ Создавать или нет СЗ Сборки
     * @return bool
     */
    public static function createAssemblyTasksAvailable(): bool
    {
        return true;
    }

    /**
     * ___ Создавать или нет СЗ Раскроя
     * @return bool
     */
    public static function createCuttingTasksAvailable(): bool
    {
        return true;
    }

    /**
     * ___ Создавать или нет СЗ Пошива
     * @return bool
     */
    public static function createSewingTasksAvailable(): bool
    {
        return true;
    }

    /**
     * ___ Создавать или нет СЗ Блоков
     * @return false
     */
    public static function createBlockTasksAvailable(): bool
    {
        return true;
    }

}
