<?php

namespace App\Modules\Core\Rules;

use Illuminate\Contracts\Validation\Rule;

class InnUlRule implements Rule
{
	protected string $errorMessage;

	/**
	 * Create a new rule instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		//
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
		if (!$value) {
			$this->errorMessage = 'ИНН пуст';
			return false;
		}

		if (preg_match('/[^0-9]/', $value)) {
			$this->errorMessage = 'ИНН может состоять только из цифр';
			return false;
		}

		if (strlen($value) !== 10) {
			$this->errorMessage = 'ИНН может состоять только из 10 цифр';
			return false;
		}

		$check_digit = function ($inn, $coefficients) {
			$n = 0;
			foreach ($coefficients as $i => $k) {
				$n += $k * (int)$inn[$i];
			}
			return $n % 11 % 10;
		};
		$n10 = $check_digit($value, [2, 4, 10, 3, 5, 9, 4, 6, 8]);
		if ($n10 === (int)$value[9]) {
			return true;
		}

		$this->errorMessage = 'Введите существующий ИНН';
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
