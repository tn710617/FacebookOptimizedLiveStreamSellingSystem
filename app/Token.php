<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Token extends Model
{
    //
    public static function checkIfTokenExists(Request $request)
    {
        $receivedToken = $request->bearerToken();
        return Token::where('name', $receivedToken)->count();
    }

    public static function checkIfTokenExpired($token)
    {
        return ((Token::where('name', $token)->first()->expiry_time) < time());
    }

    public static function checkIfTokenReceived(Request $request)
    {
        $token = $request->bearerToken();
        return isset($token);
    }

    public static function checkIfTokenValid($token)
    {
        $me = Helpers::getFacebookResources($token);
        if($me)
        {
            return true;
        }
        return false;
    }

    public static function checkIfUserExists($FacebookResources)
    {
        return User::where('FB_id', $FacebookResources->getId())->count();
    }

    public static function getLatestToken($user_id)
    {
        return Token::where('user_id', $user_id)->latest()->first()->name;
    }

    public static function deleteInvalidToken()
    {
        $tokens = (new self)->all();
        foreach ($tokens as $token)
        {
            if (!Token::checkIfTokenValid($token->name))
            {
                $token->delete();
            }
        }
    }
}
