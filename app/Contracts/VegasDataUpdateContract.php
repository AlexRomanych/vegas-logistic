<?php

namespace App\Contracts;

interface VegasDataUpdateContract
{
    public function updateData(VegasDataGetContract $getter): void;
}
