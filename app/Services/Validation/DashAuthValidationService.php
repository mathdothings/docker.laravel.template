<?php

namespace App\Services\Validation;

use App\Models\Usuario;

class DashAuthValidationService implements ValidationServiceInterface
{
    /**
     * Validate a Usuario object for Dash's authentication.
     *
     * @param  Usuario  $user  The user to validate.
     * @return bool True if validation passes, false otherwise.
     */
    public static function validate(Usuario $user): bool
    {
        if (! $user->active) {
            return false;
        }

        return true;
    }
}
