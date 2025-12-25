<?php


use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

if (!function_exists('getCloneByJSON')) {

    /**
     * Descr: Функция копирует один объект в другой через JSON
     * @param mixed $data
     * @return mixed
     */
    function getCloneByJSON(mixed $data = []): mixed
    {
        $serialized = serialize($data);
        return unserialize($serialized);
    }
}


if (!function_exists('correctTimeZone')) {

    /**
     * Descr: Функция переводит дату из UTC в текущее время зоны
     * @param string $dateString
     * @return Carbon
     */
    function correctTimeZone(string $dateString = ''): Carbon
    {
        $dateInUtc = Carbon::parse($dateString);
        $currentTimezone = Config::get('app.timezone', 'Europe/Minsk');
        return $dateInUtc->setTimezone($currentTimezone);
    }
}

if (!function_exists('getCorrectDate')) {

    /**
     * Descr: Функция переводит дату из UTC в текущее время зоны и добавляет 3 часа
     * @param string $dateString
     * @return Carbon
     */
    function getCorrectDate(string $dateString = ''): Carbon
    {
        $dateInUtc = Carbon::parse($dateString);
        $currentTimezone = Config::get('app.timezone', 'Europe/Minsk');
        return $dateInUtc->setTimezone($currentTimezone)->addHours(3);
    }
}

/**
 * Возвращает преобразованную строку (обычно это название в 1С)
 */
if (!function_exists('getPrettyNameFrom1CData')) {
    function getPrettyNameFrom1CData(string $name): string
    {
        $name = trim($name);
        //        $name = Str::replace(',', ', ', $name);
        $name = Str::replace(':', ': ', $name);
        $name = Str::replace('( ', '(', $name);
        $name = Str::replace(' )', ')', $name);
        $name = Str::replace(' /', '/', $name);
        $name = Str::replace('/ ', '/', $name);
        $name = Str::replace('\ ', '\\', $name);
        $name = Str::replace(' \\', '\\', $name);


        while (Str::contains($name, '  ')) {
            $name = Str::replace('  ', ' ', $name);
        }

        return $name;
    }
}


/**
 * ___ Возвращает только цифры из входящей строки
 * @return string
 */
if (!function_exists('getDigitPart')) {
    function getDigitPart(string $inStr = ''): string
    {
        $result = '';
        for ($i = 0; $i < strlen($inStr); $i++) {
            if (ctype_digit($inStr[$i])) {
                $result .= $inStr[$i];
            }
        }
        return $result;
    }
}

/**
 * ___ Возвращает только цифры из входящей строки + точку и запятую
 * @return string
 */
if (!function_exists('getDigitPartAndDotAndComma')) {
    function getDigitPartAndDotAndComma(string $inStr = ''): string
    {
        $result = '';
        for ($i = 0; $i < strlen($inStr); $i++) {
            if (ctype_digit($inStr[$i]) || $inStr[$i] === '.' || $inStr[$i] === ',') {
                $result .= $inStr[$i];
            }
        }
        return $result;
    }
}


/**
 * ___Возвращает только буквы из входящей строки
 * @return string
 */
if (!function_exists('getLetterPart')) {
    function getLetterPart(string $inStr = ''): string
    {
        $result = '';
        for ($i = 0; $i < strlen($inStr); $i++) {
            if (!ctype_digit($inStr[$i])) {
                $result .= $inStr[$i];
            }
        }
        return $result;
    }
}


/**
 * Обертка над validator
 */
if (!function_exists('validate')) {
    function validate(array $data = [], array $rules = []): array
    {
        return validator($data, $rules)->validate();
    }
}


function apiDebug($data = []): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\View\View
{
    return view('dd', ['data' => $data]);
}


/**
 * ___ Проверяет массив дат на дубликаты
 * __ [
 * __   'has_duplicates' => true,
 * __   'duplicates' => ['2023-01-01 00:00:00' => 2],
 * __   'unique_count' => 3,
 * __   'total_count' => 4
 * __ ]
 */
if (!function_exists('checkCarbonDuplicates')) {
    function checkCarbonDuplicates(array $dates, bool $includeTime = false): array
    {
        $format = $includeTime ? 'Y-m-d H:i:s' : 'Y-m-d';

        $counts = array_count_values(array_map(
            fn($date) => $date->format($format),
            $dates
        ));

        return [
            'has_duplicates' => count($dates) !== count($counts),
            'duplicates'     => array_filter($counts, fn($count) => $count > 1),
            'unique_count'   => count($counts),
            'total_count'    => count($dates)
        ];
    }
}


/**
 * ___ Устанавливает значение SEQUENCE
 * @param string $tableName - имя таблицы
 * @param string $columnName - имя столбца
 * @param int|null $startValue - начальное значение
 * @return bool
 */
if (!function_exists('setSequence')) {
    function setSequence(string $tableName, string $columnName = 'id', int|null $startValue = null): bool
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
     * ___ Преобразует строку из Excel в чистое число или возвращает null
     */
    if (!function_exists('parseNumericValue')) {

        /**
         * @param $value
         * @return float|null
         */
        function parseNumericValue($value): ?float
        {
            if (is_null($value) || $value === '') {
                return 0.0;
            }

            // 1. Убираем лишние пробелы (в т.ч. неразрывные)
            $value = trim(str_replace("\xc2\xa0", ' ', $value));

            // 2. Меняем запятую на точку (для локальных форматов)
            $value = str_replace(',', '.', $value);

            // 3. Убираем всё, кроме цифр, точек и минуса
            $value = preg_replace('/[^0-9.-]/', '', $value);

            return is_numeric($value) ? (float)$value : null;
        }
    }


    /**
     * ___ Вот функция PHP, которая находит минимальное пропущенное число
     * ___ в последовательности уникальных целых чисел от 1 до n, при условии,
     * ___ что из исходной последовательности были удалены некоторые числа
     * @param array $numbers
     * @return int|null
     */

    if (!function_exists('findMinMissingNumber')) {

        function findMinMissingNumber(array $numbers): ?int
        {
            // Если массив пуст, и мы ожидаем числа от 1, то 1 - минимальное пропущенное.
            if (empty($numbers)) {
                return 1;
            }

            // 1. Сортируем массив по возрастанию.
            // Это критически важно для эффективной проверки последовательности.
            sort($numbers);

            // 2. Проверяем, отсутствует ли число '1'.
            // Если первый элемент не равен 1, то 1 - минимальное пропущенное число.
            if ($numbers[0] !== 1) {
                return 1;
            }

            // 3. Итерируем по отсортированному массиву, чтобы найти первый пробел.
            // Мы ожидаем, что каждое число будет ровно на единицу больше предыдущего.
            // Цикл начинается со второго элемента (индекс 1).
            for ($i = 1; $i < count($numbers); $i++) {
                // Если текущее число не равно предыдущему числу + 1,
                // то пропущенное число - это (предыдущее число + 1).
                if ($numbers[$i] !== $numbers[$i - 1] + 1) {
                    return $numbers[$i - 1] + 1;
                }
            }

            // 4. Если пробелов не найдено, то минимальное пропущенное число - это n + 1.
            // Это происходит, если массив содержит все числа от 1 до своего максимального элемента.
            // В этом случае наименьшее пропущенное число будет следующим целым числом после наибольшего в массиве.
            return end($numbers) + 1;
        }
    }
}
