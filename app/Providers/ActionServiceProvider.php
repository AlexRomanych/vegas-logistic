<?php

namespace App\Providers;

use App\Contracts\VegasDataGetContract;
use App\Shared\DataGetterFromJsonFile;
use Illuminate\Support\ServiceProvider;

class ActionServiceProvider extends ServiceProvider
{

//    public array $singletons = [];

    public array $bindings = [
        VegasDataGetContract::class => DataGetterFromJsonFile::class,
    ];

    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
