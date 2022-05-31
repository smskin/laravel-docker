<?php

namespace App\Modules\ExampleModule;

use App\Modules\ExampleModule\Controllers\CSomeController;
use App\Modules\ExampleModule\Requests\RTestRequest;
use SMSkin\LaravelSupport\BaseModule;

class ExampleModule extends BaseModule
{
    public function test(RTestRequest $request): bool
    {
        return app(CSomeController::class, [
            'request' => $request
        ])->execute()->getResult();
    }
}
