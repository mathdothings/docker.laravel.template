<?php

namespace App\Http\Resources;

use App\Enums\Database\DatabaseConnections;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class UserResource extends JsonResource
{
    public static $wrap = null;

    public function toArray($request)
    {
        $usuario = DB::connection(DatabaseConnections::AQUARIUS->value)
            ->table('usuario')
            ->where('usua_email', $this->email)
            ->where('usua_situacao', 'a')
            ->first();

        $active = $usuario ? true : false;

        if (! $active) {
            return response()->json(null, Response::HTTP_UNAUTHORIZED);
        }

        return [
            'name' => $this->name,
            'email' => $this->email,
            'active' => $active,
        ];
    }
}
