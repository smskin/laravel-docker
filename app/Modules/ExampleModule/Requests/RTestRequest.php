<?php

namespace App\Modules\ExampleModule\Requests;

use SMSkin\LaravelSupport\BaseRequest;

class RTestRequest extends BaseRequest
{
    public string $field1;

    public function rules(): array
    {
        return [
            'field1' => 'required'
        ];
    }

    /**
     * @param string $field1
     * @return RTestRequest
     */
    public function setField1(string $field1): RTestRequest
    {
        $this->field1 = $field1;
        return $this;
    }
}
