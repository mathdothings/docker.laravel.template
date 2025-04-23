<?php

namespace App\Services\Validation;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class RequestValidationService
{
    /**
     * Validate request data against defined rules
     *
     * @return array|false Returns validated data or false if validation fails
     */
    public static function validate(array $data, array $rules): array|false
    {
        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            return false;
        }

        return $validator->validated();
    }

    /**
     * Validate data against rules and throw exception if fails
     *
     * @throws ValidationException
     */
    public static function validateOrFail(array $data, array $rules): array
    {
        return Validator::make($data, $rules)->validated();
    }
}
