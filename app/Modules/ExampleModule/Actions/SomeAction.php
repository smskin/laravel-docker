<?php

namespace App\Modules\ExampleModule\Actions;

use App\Modules\Core\BaseAction;
use App\Modules\Core\BaseRequest;
use App\Modules\ExampleModule\Requests\RTestRequest;

class SomeAction extends BaseAction
{
    protected RTestRequest|BaseRequest|null $request;

    protected ?string $requestClass = RTestRequest::class;

    public function execute(): self
    {
        $this->result = true;
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
