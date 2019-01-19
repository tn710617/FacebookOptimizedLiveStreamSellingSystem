<?php

namespace App\Http\Controllers;

use App\Helpers;
use App\Token;
use Illuminate\Http\Request;

class TestController extends Controller
{
    //
    public function test(Request $request)
    {
        $endpoint = 'me?fields=id,name,email,picture';
        $FacebookResources = Helpers::getFacebookResources($request->bearerToken(), $endpoint);
        dd(Token::checkIfUserExists($FacebookResources));
    }
}
