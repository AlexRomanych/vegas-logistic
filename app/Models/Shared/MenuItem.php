<?php

namespace App\Models\Shared;



class MenuItem
{
    public function __construct(
        public string $title = 'NoName',
        public string $link = '#',
        public string $icon = '',
        public array $items = [],
        public bool $active = false,
    )
    {
    }
}
