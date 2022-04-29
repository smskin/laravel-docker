<?php

namespace App\Modules\Core\Helpers\Imaginary;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    public function register()
    {
        $this->app->singleton(Imaginary::class, function () {
            return new Imaginary(config('services.imaginary.endpoint'));
        });
    }
}
