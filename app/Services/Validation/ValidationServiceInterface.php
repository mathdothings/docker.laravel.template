<?php

namespace App\Services\Validation;

use App\Models\Usuario;

interface ValidationServiceInterface
{
    /**
     * Validate user data.
     *
     * @return bool True if validation passes, false otherwise.
     */
    public static function validate(Usuario $user): bool;
}
