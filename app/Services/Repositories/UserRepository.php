<?php

namespace App\Services\Repositories;

use App\Models\Usuario;
use Exception;
use Illuminate\Http\Response;

class UserRepository implements RepositoryInterface
{
    public function __construct(private Usuario $usuario) {}

    public static function getAll()
    {
        try {
            return Usuario::getAll();
        } catch (\Exception $exception) {
            return response()->json($exception, Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public static function getById(int $id): Usuario|Exception
    {
        try {
            return Usuario::find($id);
        } catch (\Exception $exception) {
            return response()->json($exception, Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public static function change(int $id, Usuario $changes): bool|Exception
    {
        try {
            $usuario = UserRepository::getById($id);
            $usuario = $changes;
            $usuario->save();

            return true;
        } catch (\Exception $exception) {
            return response()->json($exception, Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public static function remove(int $id): bool|Exception
    {
        try {
            $usuario = UserRepository::getById($id);
            $usuario->delete();

            return true;
        } catch (\Exception $exception) {
            return response()->json($exception, Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}
