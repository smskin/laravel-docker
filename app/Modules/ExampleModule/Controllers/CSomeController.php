<?php

namespace App\Modules\ExampleModule\Controllers;

use App\Modules\Core\BaseController;
use App\Modules\Core\BaseRequest;
use App\Modules\ExampleModule\Actions\SomeAction;
use App\Modules\ExampleModule\Requests\RTestRequest;

class CSomeController extends BaseController
{
    protected RTestRequest|BaseRequest|null $request;

    protected ?string $requestClass = RTestRequest::class;

    public function execute(): self
    {
        $this->result = app(SomeAction::class, [
            'request' => $this->request
        ])->execute()->getResult();
        return $this;
    }

    /**
     * @return bool
     */
    public function getResult(): bool
    {
        return parent::getResult();
    }
}
