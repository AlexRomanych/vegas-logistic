<?php

namespace App\Models\Shared;


class Location
{
    public function __construct(
        public string|null $address = null,
        public float $latitude = 0,
        public float $longitude = 0,
    )
    {
    }
}
