<?php


use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;

if (! function_exists('getCloneByJSON')) {

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


if (! function_exists('correctTimeZone')) {

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

if (! function_exists('getCorrectDate')) {

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
if (! function_exists('getPrettyNameFrom1CData')) {
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
 * Возвращает только цифры из входящей строки
 * @return string
 */
if (! function_exists('getDigitPart')) {
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
 * Возвращает только буквы из входящей строки
 * @return string
 */
if (! function_exists('getLetterPart')) {
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
if (! function_exists('validate')) {
    function validate(array $data = [], array $rules = []): array
    {
        return validator($data, $rules)->validate();
    }
}


function apiDebug($data = []): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\View\View
{
    return view('dd', ['data' => $data]);
}
