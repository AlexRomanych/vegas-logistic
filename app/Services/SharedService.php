<?php

namespace App\Services;


use App\Facades\Plan;
use App\Models\Shared\Period;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SharedService
{

    /**
     * ___ Устанавливает значение SEQUENCE
     * @param string $tableName         - имя таблицы
     * @param string $columnName        - имя столбца
     * @param int|null $startValue      - начальное значение
     * @return bool
     */
    public static function setSequence(string $tableName, string $columnName = 'id', int | null $startValue = null): bool
    {
        try {

            // __ Это работает
            // $tableName = self::TABLE_NAME;
            // $sql = "SELECT setval(
            //     pg_get_serial_sequence(?, 'id'),
            //     (SELECT COALESCE(MAX(id), 0) FROM {$tableName})
            // );";
            //
            // DB::statement($sql, [$tableName]);

            // pg_get_serial_sequence возвращает имя sequence в формате 'схема.имя'
            $sequenceObj = DB::selectOne(
                "SELECT pg_get_serial_sequence(?, ?)",
                [$tableName, $columnName]
            );

            // Функция selectOne возвращает объект, где ключ - это имя поля (pg_get_serial_sequence)
            if (!($sequenceObj && property_exists($sequenceObj, 'pg_get_serial_sequence'))) return false;


            if (!is_null($startValue)) {
                $maxId = $startValue;
            } else {
                // Получаем максимальный ID из таблицы 'clients'
                $maxId = DB::table($tableName)->max($columnName);

                if ($maxId === null) return false;
            }

            $sequenceName = $sequenceObj->pg_get_serial_sequence;

            // Выполнение команды setval:
            // Устанавливаем SEQUENCE на $maxId, чтобы следующий ID был $maxId + 1.
            DB::statement("SELECT setval(?, ?, TRUE)", [$sequenceName, $maxId]);

            return true;
        } catch (\Throwable $e) {
            return false;
        }

    }











    /**
     * Проверяет на валидность поля выбора дат на страницах с выбором периода
     * @param Request $request
     * @return Period
     */
    public function validPlanDates(Request $request): Period
    {

        $result = $request->validate([
            'start-period' => ['nullable', 'before_or_equal:end-period'],
            'end-period' => ['nullable', 'after_or_equal:start-period'],
        ]);

        if (isset($result['start-period']) && isset($result['end-period'])) {
            return new Period($result['start-period'], $result['end-period']);
        } else {
            return Plan::getPeriod();
        }

    }
}
