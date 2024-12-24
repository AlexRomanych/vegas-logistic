<?php

namespace App\Providers;

use App\Models\Shared\Facades\ModelFacade;
use Illuminate\Support\ServiceProvider;

class FacadeProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton('model', fn () => new ModelFacade());
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
