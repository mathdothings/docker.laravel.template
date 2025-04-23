<?php

namespace App\Enums\Database;

enum DatabaseConnections: string
{
    case AQUARIUS = 'pgsql_aquarius';
    case DASH = 'pgsql';
}
