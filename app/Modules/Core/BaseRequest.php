<?php

namespace App\Modules\Core;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use ReflectionClass;

abstract class BaseRequest
{
    public bool $isValidated = false;

    abstract public function rules(): array;

    /**
     * @return array
     */
    protected function messages(): array
    {
        return [];
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $reflection = new ReflectionClass(static::class);
        $data = [];
        foreach ($reflection->getProperties() as $property) {
            if ($property->isInitialized($this)) {
                $value = $property->getValue($this);
                if (
                    !$value instanceof Model &&
                    $value instanceof Arrayable
                ) {
                    $value = array_merge(
                        [
                            'context' => $value
                        ],
                        $value->toArray()
                    );
                }
                $data[$property->name] = $value;
            }
        }

        return $data;
    }

    /**
     * @throws ValidationException
     */
    public function validate(): void
    {
        if ($this->isValidated) {
            return;
        }

        $validator = Validator::make(
            $this->toArray(),
            $this->rules(),
            $this->messages()
        );
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
        $this->isValidated = true;
    }
}
