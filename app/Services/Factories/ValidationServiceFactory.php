<?php

namespace App\Services\Factories;

use App\Services\Validation\DashAuthValidationService;
use App\Services\Validation\ValidationServiceInterface;

class ValidationServiceFactory
{
    /**
     * Create the appropriate validation service instance based on the type of validation required.
     *
     * @param  string  $type  The type of validation service.
     * @return ValidationServiceInterface The validation service instance.
     *
     * @throws \InvalidArgumentException If an invalid validation service type is provided.
     */
    public static function create(string $type): ValidationServiceInterface
    {
        switch ($type) {
            case 'dash':
                return new DashAuthValidationService;
            default:
                throw new \InvalidArgumentException("Invalid validation service type: $type");
        }
    }
}
