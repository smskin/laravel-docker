<?php

namespace App\Modules\ExampleModule;

use App\Modules\Core\BaseModule;
use App\Modules\ExampleModule\Controllers\CSomeController;
use App\Modules\ExampleModule\Requests\RTestRequest;

class ExampleModule extends BaseModule
{
    public function test(RTestRequest $request): bool
    {
        return app(CSomeController::class, [
            'request' => $request
        ])->execute()->getResult();
    }
}
