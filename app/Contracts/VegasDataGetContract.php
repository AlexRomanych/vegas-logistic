<?php

namespace App\Contracts;

interface VegasDataGetContract
{
    public function getDataFromFile(string $fileName): array;
}
