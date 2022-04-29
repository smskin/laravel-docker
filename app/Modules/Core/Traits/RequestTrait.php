<?php

namespace App\Modules\Core\Traits;

use App\Modules\Core\BaseRequest;
use Exception;
use Illuminate\Validation\ValidationException;

trait RequestTrait
{
    protected ?BaseRequest $request;

    protected ?string $requestClass;

    /**
     * @throws ValidationException
     * @throws Exception
     */
    protected function validateRequest(): void
    {
        if ($this->requestClass && !$this->request)
        {
            throw new Exception('Request not initialized');
        }

        if ($this->requestClass && !($this->request instanceof $this->requestClass)) {
            throw new Exception(
                sprintf(
                    "Invalid request type. Expected: %s, Received: %s. Action: %s",
                    $this->requestClass,
                    get_class($this->request),
                    static::class
                )
            );
        }
        if ($this->request instanceof BaseRequest) {
            $this->request->validate();
        }
    }
}
