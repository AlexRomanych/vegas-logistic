<?php


use Illuminate\Support\Str;

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
 * Обертка над validator
 */
if (! function_exists('validate')) {
    function validate(array $data = [], array $rules = []): array
    {
        return validator($data, $rules)->validate();
    }
}
