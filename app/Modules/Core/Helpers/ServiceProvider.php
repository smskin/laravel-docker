<?php

namespace App\Modules\Core\Helpers;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    public function register()
    {
        $this->app->singleton(Helper::class, function () {
            return new Helper();
        });

        $this->app->register(Imaginary\ServiceProvider::class);
    }
}
