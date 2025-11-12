<?php


use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
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
