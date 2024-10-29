<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Support\Facades\Log;

class InvalidInputDataException extends Exception
{
    public function report()
    {
        Log::debug($this->getMessage());
        dd($this);
    }
}
