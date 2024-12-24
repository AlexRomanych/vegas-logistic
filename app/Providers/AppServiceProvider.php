<?php

namespace App\Providers;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        mb_internal_encoding('UTF-8'); // Устанавливаем кодировку

//        JsonResource::withoutWrapping();  // удаляет обертку для OrderResource
//        JsonResource::wrap('test');  // задает глобальную обертку для OrderResource
    }
}
