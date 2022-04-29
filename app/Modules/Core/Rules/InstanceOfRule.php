<?php

namespace App\Modules\Core\Rules;

use Illuminate\Contracts\Validation\Rule;

class InstanceOfRule implements Rule
{
    protected string $className;

    /**
     * Create a new rule instance.
     *
     * @param string $className
     */
    public function __construct(string $className)
    {
        $this->className = $className;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        if (is_array($value) && array_key_exists('context', $value)) {
            return $value['context'] instanceof $this->className;
        }

        return $value instanceof $this->className;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return 'Параметр :attribute должен быть экземпляром класса ' . $this->className;
    }
}
