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
            'data'    => OK_STATUS_WORD,
            'payload' => $data,
            'error'   => null
        ]);
    }




    /**
     * ___ Возвращает JSON-ответ в случае неуспешного выполнения запроса
     * @param mixed|null $responseData
     * @param int $statusCode HTTP-статус ошибки (по умолчанию 500)
     * @return JsonResponse
     */
    //public static function fail(mixed $responseData = null, int $statusCode = 500): JsonResponse
    //{
    //    $errorMessage = 'Произошла непредвиденная ошибка сервера.';
    //
    //    // Извлекаем сообщение, если передали Exception
    //    if ($responseData instanceof \Exception) {
    //        $errorMessage = $responseData->getMessage();
    //    }
    //    // Извлекаем сообщение, если передали уже готовый JsonResponse с ошибкой
    //    elseif ($responseData instanceof JsonResponse) {
    //        $original = $responseData->getOriginalContent();
    //        $errorMessage = is_array($original) && isset($original['message'])
    //            ? $original['message']
    //            : ($responseData->statusText() ?: 'Ошибка валидации/запроса');
    //        $statusCode = $responseData->getStatusCode();
    //    }
    //    // Если передали обычную строку
    //    elseif (is_string($responseData)) {
    //        $errorMessage = $responseData;
    //    }
    //
    //    // Возвращаем ЧЕСТНЫЙ JsonResponse. Laravel автоматически упакует
    //    // его в UTF-8 и проставит заголовок Content-Type: application/json
    //    return response()->json([
    //        'data'    => defined('FAIL_STATUS_WORD') ? FAIL_STATUS_WORD : 'fail',
    //        'error'   => $errorMessage,
    //        'payload' => null,
    //    ], $statusCode);
    //}













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
                'data'    => FAIL_STATUS_WORD,
                'error'   => $responseData->original->getMessage(),
                'payload' => null,
            ]);
        } else {
            if ($responseData instanceof \Exception) {
                return json_encode([
                    'data'    => FAIL_STATUS_WORD,
                    'error'   => $responseData->getMessage(),
                    'payload' => null,
                ]);
            }
        }

        return response()->json_encode([
            'data'    => FAIL_STATUS_WORD,
            'error'   => 'Нет данных.',
            'payload' => null,
        ]);
    }


    public static function failResponse(mixed $responseData = null):  \Illuminate\Http\JsonResponse
    {
        $jsonData = [
            'data'    => FAIL_STATUS_WORD,
            'error'   => 'Нет данных.',
            'payload' => null,
        ];

        if ($responseData instanceof JsonResponse) {
            $jsonData = [
                'data'    => FAIL_STATUS_WORD,
                'error'   => $responseData->original->getMessage(),
                'payload' => null,
            ];
        } else {
            if ($responseData instanceof \Exception) {
                $jsonData = [
                    'data'    => FAIL_STATUS_WORD,
                    'error'   => $responseData->getMessage(),
                    'payload' => null,
                ];
            }
        }

        return response()->json($jsonData, 500);
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
