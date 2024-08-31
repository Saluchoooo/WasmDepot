<?php

namespace App\Models;

use Illuminate\Contracts\Auth\Authenticatable;
use MongoDB\Laravel\Eloquent\Model as Eloquent;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Log;

class User extends Eloquent implements JWTSubject, Authenticatable
{
    use Notifiable;

    protected $connection = 'mongodb';

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'role',
        'favorites'
    ];

    protected $hidden = [
        'password',
    ];


    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }






    /**
     * Get the name of the "auth" identifier column.
     *
     * @return string
     */
    public function getAuthIdentifierName()
    {
        return '_id';
    }

    /**
     * Get the primary key value for the model.
     *
     * @return mixed
     */
    public function getAuthIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->attributes['password'];
    }

    /**
     * Get the token value for the "remember me" session.
     *
     * @return string|null
     */
    public function getRememberToken()
    {
        return null;
    }

    /**
     * Set the token value for the "remember me" session.
     *
     * @param  string|null  $value
     * @return void
     */
    public function setRememberToken($value)
    {
        // No implementat perquè MongoDB no utilitza tokens de recordatori
    }

    /**
     * Get the name of the "remember me" token column.
     *
     * @return string
     */
    public function getRememberTokenName()
    {
        return 'remember_token';
    }

    public function getAuthPasswordName()
    {
        return 'password'; // Nom de la columna de contrasenya
    }
}
