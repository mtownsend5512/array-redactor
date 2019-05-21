<?php

namespace Mtownsend\ArrayRedactor\Providers;

use Illuminate\Support\ServiceProvider;
use Mtownsend\ArrayRedactor\ArrayRedactor;

class ArrayRedactorServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/arrayredactor.php' => config_path('arrayredactor.php')
        ], 'config');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('array_redactor', function () {
            return new ArrayRedactor([], config('arrayredactor.keys'), config('arrayredactor.ink'));
        });
    }
}
