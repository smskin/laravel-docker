<?php

namespace App\Modules;

use Illuminate\Support\ServiceProvider;

class ModuleServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(Core\ServiceProvider::class);
        $this->app->register(ExampleModule\ServiceProvider::class);
    }
}
