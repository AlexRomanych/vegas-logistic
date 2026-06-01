<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Model as EloquentModel;
//use App\Facades\Model;
use App\Models\Manufacture\Cells\Fabric\FabricTaskRoll;
use App\Observers\FabricTaskRollObserver;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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

        //if ($this->app->environment('local')) {
        //    DB::listen(function ($query) {
        //        // Ищем в истории вызовов первый файл, который находится в нашей папке app/
        //        $trace = collect(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS))
        //            ->first(function ($frame) {
        //                return isset($frame['file']) &&
        //                    str_contains($frame['file'], base_path('app')) &&
        //                    !str_contains($frame['file'], 'vendor'); // Исключаем сам фреймворк
        //            });
        //
        //        $location = $trace
        //            ? str_replace(base_path() . '/', '', $trace['file']) . ':' . $trace['line']
        //            : 'Unknown';
        //
        //        Log::info("SQL: {$query->sql} | Bindings: " . json_encode($query->bindings) . " | Вызвано в: {$location}");
        //    });
        //}


        // Логируем запросы только в локальной среде разработки
        //if (app()->environment('local')) {
        //    DB::listen(function ($query) {
        //        Log::info($query->sql, [
        //            'bindings' => $query->bindings,
        //            'time' => $query->time . 'ms'
        //        ]);
        //    });
        //}

        //EloquentModel::preventLazyLoading(! app()->isProduction());


//        JsonResource::withoutWrapping();  // удаляет обертку для OrderResource
//        JsonResource::wrap('test');  // задает глобальную обертку для OrderResource
    }
}
