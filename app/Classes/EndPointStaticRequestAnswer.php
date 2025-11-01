<?php

namespace App\Classes;


use Tymon\JWTAuth\Providers\Auth\Illuminate;
use Illuminate\Http\JsonResponse;

class EndPointStaticRequestAnswer
{

    /**
     * Возвращает что-то в случае успешного выполнения запроса
     * @param string $data
     * @return string
     */
    public static function ok(string $data = OK_STATUS_WORD): string
    {
        return json_encode([
            'data' => OK_STATUS_WORD,
            'payload' => $data,
        ]);
    }

    /**
     * ___ Возвращает что-то в случае неуспешного выполнения запроса
     * @param mixed|null $responseData
     * @return string
     */
    public static function fail(mixed $responseData = null): string
    {
//         if ($data) {
// //            return $data;
//             return json_encode($data);
//         }
//         $class = get_class($responseData);
        // return FAIL_STATUS_WORD;
        if ($responseData instanceof JsonResponse) {
            return json_encode([
                'data'  => FAIL_STATUS_WORD,
                'error' => $responseData->original->getMessage()
            ]);
        } else if ($responseData instanceof \Exception) {
            return json_encode([
                'data'  => FAIL_STATUS_WORD,
                'error' => $responseData->getMessage()
            ]);
        }

        return json_encode([
            'data'  => FAIL_STATUS_WORD,
            'error' => 'Нет данных.'
        ]);

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
