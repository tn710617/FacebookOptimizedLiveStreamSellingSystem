<?php

namespace App;

use Illuminate\Http\Request;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'FB_id', 'host', 'id', 'channel_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public static function getUserIDViaFACEBOOK($FacebookResources)
    {
        return User::where('email', $FacebookResources->getEmail())->first()->id;
    }

    public static function getUserID(Request $request)
    {
        return Token::where('name', $request->bearerToken())->first()->user_id;
    }

    public static function checkIfUserInAChannel(Request $request)
    {
        if(User::where('id', self::getUserID($request))->first()->channel_id !== 0)
        {
            return true;
        }
        return false;
    }
}
