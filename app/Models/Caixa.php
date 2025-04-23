<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Caixa extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'aquarius_dm.dm_caixa'; // Replace with your actual table name

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id'; // Replace with your actual primary key if different

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false; // Set to false if your table doesn't have `created_at` and `updated_at` columns
}
