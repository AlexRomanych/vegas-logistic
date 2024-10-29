<?php

namespace App\Models;

use App\Models\Abstract\Human;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class Manager extends Human
{
    public const DEFAULT_MANAGER_NAME = 'Не определено';

    protected $fillable = [
        'id',
        'surname', 'first_name', 'second_name',
        'description',
        'created_at','updated_at',
    ];


    public function clients(): HasMany
    {
        return $this->hasMany(Client::class);
    }
}
