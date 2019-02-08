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

    public static function checkIfTokenExpired(Request $request)
    {
        return ((Token::where('name', $request->bearerToken())->first()->expiry_time) < time());
    }

    public static function checkIfTokenReceived(Request $request)
    {
        $token = $request->bearerToken();
        return isset($token);
    }

    public static function checkIfTokenValid(Request $request)
    {
        $me = Helpers::getFacebookResources($request->bearerToken());
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
}
