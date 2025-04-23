<?php

namespace App\Services\Repositories;

use App\Models\Usuario;
use Exception;

interface UserRepositoryInterface
{
    public static function getAll();

    public static function getById(int $id): Usuario|Exception;

    public static function change(int $id, Usuario $changes): bool|Exception;

    public static function remove(int $id): bool|Exception;
}
