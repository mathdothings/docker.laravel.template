<?php

namespace App\Services;

use App\Services\Repositories\UserRepository;

class UserService
{
    public function __construct(private UserRepository $repository) {}
}
