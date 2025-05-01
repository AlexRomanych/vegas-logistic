<?php

namespace App\Classes;


class EndPointStaticRequestAnswer
{

    /**
     * Возвращает что-то в случае успешного выполнения запроса
     * @param string $data
     * @return string
     */
    public static function ok(string $data = OK_STATUS_WORD): string
    {
        return json_encode(['data' => $data]);
    }

    /**
     * Возвращает что-то в случае неуспешного выполнения запроса
     * @param mixed|null $data
     * @return string
     */
    public static function fail(mixed $data = null): string
    {
        if ($data) {
            return json_encode($data);
        }

        return FAIL_STATUS_WORD;
    }

    /**
     * Возвращает список дубликатов, если они есть
     * @param array $dubs
     * @return string
     */
    public static function dubs(array $dubs): string
    {
        return json_encode($dubs);
    }

}
