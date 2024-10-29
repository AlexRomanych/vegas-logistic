<?php

namespace App\Models\Abstract;

use App\Exceptions\InvalidInputDataException;
use Illuminate\Database\Eloquent\Model;

abstract class Human extends Model
{
    public function __construct(array $attributes = [])
    {

//        dd($attributes);

        parent::__construct($attributes);

//        try {
//            if (!isset($attributes['surname']) || is_null($attributes['surname'] || $attributes['surname'] === '' )) {
//                throw new InvalidInputDataException('Отсутствует или неверная фамилия');
//            }
//                parent::__construct($attributes);
//        } catch (InvalidInputDataException $e) {
//            report($e);
//        }

    }
}
