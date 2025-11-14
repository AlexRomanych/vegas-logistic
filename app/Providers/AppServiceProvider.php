<?php

namespace App\Providers;

use App\Models\Manufacture\Cells\Fabric\FabricTaskRoll;
use App\Observers\FabricTaskRollObserver;
use Illuminate\Support\ServiceProvider;
// use Illuminate\Http\Resources\Json\JsonResource;

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

        FabricTaskRoll::observe(FabricTaskRollObserver::class);

//        JsonResource::withoutWrapping();  // удаляет обертку для OrderResource
//        JsonResource::wrap('test');  // задает глобальную обертку для OrderResource
    }
}
