<?php

namespace App\Modules\Core;

use App\Modules\Core\Traits\RequestTrait;
use Illuminate\Validation\ValidationException;

abstract class BaseAction
{
    use RequestTrait;

    protected mixed $result;

    /**
     * BaseAction constructor.
     *
     * @param BaseRequest|null $request
     * @throws ValidationException
     */
    final public function __construct(?BaseRequest $request = null)
    {
        if ($request) {
            $this->request = $request;
            $this->validateRequest();
        }
    }

    abstract public function execute(): self;

    public function getResult(): mixed
    {
        return $this->result;
    }
}
