<?php

namespace App\Modules\Core\Rules;

use Illuminate\Contracts\Validation\Rule;

class ExistsEloquentModelRule extends InstanceOfRule implements Rule
{
    protected string $errorMessage;

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        if (!$value instanceof $this->className) {
            $this->errorMessage = 'Параметр :attribute должен быть экземпляром класса ' . $this->className;
            return false;
        }

        if ($value->exists) {
            return true;
        }
        $this->errorMessage = 'Параметр :attribute должен быть ранее созданным объектом БД';
        return false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return $this->errorMessage;
    }
}
