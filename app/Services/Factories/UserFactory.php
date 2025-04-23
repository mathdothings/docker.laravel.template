<?php

namespace App\Services\Factories;

use App\Models\Usuario;
use App\Services\Repositories\UserRepository;
use App\Services\UserService;

class UserFactory
{
    public static function create(): UserService
    {
        $userModel = new Usuario;
        $userRepository = new UserRepository($userModel);
        $userService = new UserService($userRepository);

        return $userService;
    }
}
