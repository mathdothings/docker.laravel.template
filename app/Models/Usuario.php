<?php

namespace App\Models;

use App\Enums\Database\DatabaseConnections;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Usuario extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;

    protected $table = 'usuario';

    protected $connection = DatabaseConnections::AQUARIUS->value;

    protected $primaryKey = 'usua_codigo';

    public $incrementing = true;

    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'usua_nome',
        'usua_situacao',
        'usua_email',
        'usua_token_dash',
        'usua_token_google',
        'usua_rota_conexao',
        'usua_codigo_incl',
        'usua_data_incl',
        'usua_codigo_alt',
        'usua_data_alt',
    ];

    protected $attributes = [
        'usua_situacao' => 'a',
    ];

    protected $casts = [
        'usua_situacao' => 'boolean',
    ];

    public function getActiveAttribute()
    {
        return $this->attributes['usua_situacao'] === 'a';
    }

    public function setActiveAttribute($value)
    {
        $this->attributes['usua_situacao'] = $value ? 'a' : 'i';
    }

    public function getNameAttribute()
    {
        return $this->attributes['usua_nome'];
    }

    public function getEmailAttribute()
    {
        return $this->attributes['usua_email'];
    }

    public function getDashTokenAttribute()
    {
        return $this->attributes['usua_token_dash'];
    }

    public function getGoogleTokenAttribute()
    {
        return $this->attributes['usua_token_google'];
    }

    public function getConnectionRouteAttribute()
    {
        return $this->attributes['usua_rota_conexao'];
    }

    public function getInclusionCodeAttribute()
    {
        return $this->attributes['usua_codigo_incl'];
    }

    public function getInclusionDateAttribute()
    {
        return $this->attributes['usua_data_incl'];
    }

    public function getAlterationCodeAttribute()
    {
        return $this->attributes['usua_codigo_alt'];
    }

    public function getAlterationDateAttribute()
    {
        return $this->attributes['usua_data_alt'];
    }

    public function setNameAttribute($value)
    {
        $this->attributes['usua_nome'] = $value;
    }

    public function setEmailAttribute($value)
    {
        $this->attributes['usua_email'] = $value;
    }

    public function setDashTokenAttribute($value)
    {
        $this->attributes['usua_token_dash'] = $value;
    }

    public function setGoogleTokenAttribute($value)
    {
        $this->attributes['usua_token_google'] = $value;
    }

    public function setConnectionRouteAttribute($value)
    {
        $this->attributes['usua_rota_conexao'] = $value;
    }

    public function setInclusionCodeAttribute($value)
    {
        $this->attributes['usua_codigo_incl'] = $value;
    }

    public function setInclusionDateAttribute($value)
    {
        $this->attributes['usua_data_incl'] = $value;
    }

    public function setAlterationCodeAttribute($value)
    {
        $this->attributes['usua_codigo_alt'] = $value;
    }

    public function setAlterationDateAttribute($value)
    {
        $this->attributes['usua_data_alt'] = $value;
    }
}
